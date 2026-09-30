const fs = require('fs');
const path = require('path');

const XLSX_DIR = path.join(__dirname, '..', 'storage', '_xlsx_temp', 'xl');
const EXCEL_FILE = process.argv[2] || 'c:\\Users\\mathi\\Desktop\\Inventaire_CPNV_Multimedia_copie.xlsx';
const SKIP_SHEETS = new Set(['Graphique1']);

function decodeXml(text) {
  return text
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&apos;/g, "'");
}

function readSharedStrings() {
  const xml = fs.readFileSync(path.join(XLSX_DIR, 'sharedStrings.xml'), 'utf8');
  const strings = [];
  for (const match of xml.matchAll(/<si>([\s\S]*?)<\/si>/g)) {
    const block = match[1];
    if (block.includes('<r>')) {
      strings.push(
        [...block.matchAll(/<t[^>]*>([\s\S]*?)<\/t>/g)]
          .map((part) => decodeXml(part[1]))
          .join('')
      );
    } else {
      const tMatch = block.match(/<t[^>]*>([\s\S]*?)<\/t>/);
      strings.push(tMatch ? decodeXml(tMatch[1]) : '');
    }
  }
  return strings;
}

function colIndex(col) {
  let n = 0;
  for (const ch of col) {
    n = n * 26 + (ch.charCodeAt(0) - 64);
  }
  return n - 1;
}

function parseSheet(sheetPath, sharedStrings) {
  const xml = fs.readFileSync(sheetPath, 'utf8');
  const rows = new Map();
  for (const rowMatch of xml.matchAll(/<row[^>]*r="(\d+)"[^>]*>([\s\S]*?)<\/row>/g)) {
    const rowNum = Number(rowMatch[1]);
    const cells = {};
    for (const cellMatch of rowMatch[2].matchAll(
      /<c[^>]*r="([A-Z]+)(\d+)"(?:[^>]*\st="([^"]*)")?[^>]*>(?:<v>([\s\S]*?)<\/v>|<is><t[^>]*>([\s\S]*?)<\/t><\/is>)?<\/c>/g
    )) {
      const col = cellMatch[1];
      const type = cellMatch[3] || '';
      let value = cellMatch[5] !== undefined ? decodeXml(cellMatch[5]) : decodeXml(cellMatch[4] || '');
      if (type === 's') {
        value = sharedStrings[Number(value)] || '';
      }
      cells[col] = value.trim();
    }
    if (Object.keys(cells).length) {
      rows.set(rowNum, cells);
    }
  }
  return rows;
}

function isHeaderDuplicateRow(record, headers) {
  const nom = record['NOM'] || record[headers[0]] || '';
  const nombre = record['NOMBRE'] || record[headers[1]] || '';
  return nom === 'NOM' && (nombre === 'NOMBRE' || nombre === 'Nombre');
}

function rowsToTable(rows) {
  const sorted = [...rows.entries()].sort((a, b) => a[0] - b[0]);
  if (!sorted.length) {
    return { headers: [], headerCols: [], data: [] };
  }

  const headerEntry = sorted[0][1];
  const headerCols = Object.keys(headerEntry).sort((a, b) => colIndex(a) - colIndex(b));
  const rawHeaders = headerCols.map((col) => headerEntry[col]);
  const headers = dedupeHeaders(rawHeaders);

  const data = [];
  for (const [, cells] of sorted.slice(1)) {
    const record = {};
    let hasValue = false;
    headerCols.forEach((col, idx) => {
      const header = headers[idx];
      const value = String(cells[col] ?? '').trim();
      if (value !== '') {
        record[header] = value;
        hasValue = true;
      }
    });
    if (!hasValue || isHeaderDuplicateRow(record, headers)) {
      continue;
    }
    data.push(record);
  }

  return { headers, headerCols, data };
}

const SKIP_IMPORT_HEADERS = new Set(['NOMBRE']);
const UNIT_HEADERS = new Set([
  'Identifiants individuels',
  'Identifiants individuels (2)',
  'État',
  'Contenu',
  'OBJ manquant',
]);

function isUnitHeader(header) {
  return UNIT_HEADERS.has(header) || /^Identifiants individuels/i.test(header);
}

/**
 * Propage le nom (NOM) et les infos produit sur chaque exemplaire.
 * Sur une ligne de suite, seuls identifiant / état / contenu / obj manquant sont lus.
 */
function normalizeRows(data, headers) {
  if (!headers.includes('NOM')) {
    return data.map((row) => {
      const copy = { ...row };
      delete copy.NOMBRE;
      return copy;
    });
  }

  let currentProduct = null;
  const normalized = [];

  for (const row of data) {
    const nom = (row.NOM || '').trim();

    if (nom) {
      currentProduct = {};
      for (const [key, value] of Object.entries(row)) {
        if (SKIP_IMPORT_HEADERS.has(key)) {
          continue;
        }
        if (String(value).trim() !== '') {
          currentProduct[key] = value;
        }
      }
    }

    if (!currentProduct || !currentProduct.NOM) {
      continue;
    }

    const merged = { ...currentProduct };

    if (!nom) {
      for (const [key, value] of Object.entries(row)) {
        if (SKIP_IMPORT_HEADERS.has(key) || !isUnitHeader(key)) {
          continue;
        }
        if (String(value).trim() !== '') {
          merged[key] = value;
        }
      }
    }

    merged.NOM = currentProduct.NOM;
    delete merged.NOMBRE;

    if (Object.values(merged).some((value) => String(value).trim() !== '')) {
      normalized.push(merged);
    }
  }

  return normalized;
}

function importHeaders(headers) {
  return headers.filter((header) => !SKIP_IMPORT_HEADERS.has(header));
}

function dedupeHeaders(headers) {
  const seen = {};
  return headers.map((header) => {
    const base = header || 'colonne';
    seen[base] = (seen[base] || 0) + 1;
    return seen[base] > 1 ? `${base} (${seen[base]})` : base;
  });
}

function slugify(value) {
  return value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_+|_+$/g, '');
}

function sqlColumnName(header) {
  const map = {
    NOM: 'nom',
    NOMBRE: 'nombre',
    'Nom du matériel': 'nom_du_materiel',
    Marque: 'marque',
    'Modèle': 'modele',
    'Catégorie': 'categorie',
    'Identifiants individuels': 'identifiants_individuels',
    'Identifiants individuels (2)': 'identifiants_individuels_2',
    'État': 'etat',
    'Prix (CHF)': 'prix_chf',
    Contenu: 'contenu',
    Description: 'description',
    'OBJ manquant': 'obj_manquant',
  };

  if (map[header]) {
    return map[header];
  }

  if (/^identifiants individuels/i.test(header)) {
    return slugify(header).replace(/^identifiants_individuels$/, 'identifiants_individuels');
  }

  return slugify(header).slice(0, 60) || 'colonne';
}

function sqlEscape(value) {
  return String(value).replace(/\\/g, '\\\\').replace(/'/g, "''");
}

function sqlValue(header, value) {
  if (SKIP_IMPORT_HEADERS.has(header)) {
    return null;
  }

  if (value === undefined || value === null || String(value).trim() === '') {
    return null;
  }

  return `'${sqlEscape(String(value))}'`;
}

function readWorkbookSheets() {
  const workbook = fs.readFileSync(path.join(XLSX_DIR, 'workbook.xml'), 'utf8');
  const rels = fs.readFileSync(path.join(XLSX_DIR, '_rels', 'workbook.xml.rels'), 'utf8');
  const relMap = {};
  for (const match of rels.matchAll(/Id="([^"]+)"[^>]*Target="([^"]+)"/g)) {
    relMap[match[1]] = match[2].replace(/^\.\.\//, '');
  }

  const sheets = [];
  for (const match of workbook.matchAll(/<sheet[^>]*name="([^"]+)"[^>]*r:id="([^"]+)"/g)) {
    const name = decodeXml(match[1]);
    const target = relMap[match[2]];
    sheets.push({
      name,
      file: path.join(XLSX_DIR, target),
      tableName: `materials_${slugify(name)}`,
    });
  }
  return sheets;
}

function buildSheetSql(sheet, sharedStrings) {
  if (SKIP_SHEETS.has(sheet.name) || !fs.existsSync(sheet.file)) {
    return { sheet: sheet.name, tableName: sheet.tableName, rows: 0, sql: '' };
  }

  const { headers, data: rawData } = rowsToTable(parseSheet(sheet.file, sharedStrings));
  if (!headers.length) {
    return { sheet: sheet.name, tableName: sheet.tableName, rows: 0, sql: '' };
  }

  const data = normalizeRows(rawData, headers);
  const headersForImport = importHeaders(headers);
  const columns = headersForImport.map(sqlColumnName);
  const lines = [
    `-- Espace : ${sheet.name}`,
    `DROP TABLE IF EXISTS \`${sheet.tableName}\`;`,
    `CREATE TABLE \`${sheet.tableName}\` (`,
    '  `id` INT AUTO_INCREMENT PRIMARY KEY,',
    ...columns.map((col) => `  \`${col}\` TEXT NULL,`),
    '  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
    '',
  ];

  for (const row of data) {
    const presentColumns = [];
    const presentValues = [];

    headersForImport.forEach((header) => {
      const value = sqlValue(header, row[header]);
      if (value !== null) {
        presentColumns.push(`\`${sqlColumnName(header)}\``);
        presentValues.push(value);
      }
    });

    if (!presentColumns.length) {
      continue;
    }

    lines.push(
      `INSERT INTO \`${sheet.tableName}\` (${presentColumns.join(', ')}) VALUES (${presentValues.join(', ')});`
    );
  }

  lines.push('');
  return { sheet: sheet.name, tableName: sheet.tableName, rows: data.length, sql: lines.join('\n') };
}

function ensureExtracted() {
  if (fs.existsSync(path.join(XLSX_DIR, 'workbook.xml'))) {
    return;
  }

  const zipCopy = path.join(__dirname, '..', 'storage', 'inventaire.zip');
  fs.copyFileSync(EXCEL_FILE, zipCopy);
  fs.mkdirSync(path.join(__dirname, '..', 'storage', '_xlsx_temp'), { recursive: true });
  throw new Error(
    'Fichier Excel non extrait. Copiez le .xlsx en .zip puis extrayez-le dans storage/_xlsx_temp.'
  );
}

function main() {
  ensureExtracted();
  const sharedStrings = readSharedStrings();
  const sheets = readWorkbookSheets();
  const parts = [
    '-- Inventaire CPNV importé depuis Excel',
    `-- Source : ${path.basename(EXCEL_FILE)}`,
    `-- Généré le : ${new Date().toISOString()}`,
    '-- Une table par feuille Excel (= un espace physique)',
    '',
    'USE `cpnv_gestmat`;',
    '',
    'CREATE TABLE IF NOT EXISTS `material_spaces` (',
    '  `id` INT AUTO_INCREMENT PRIMARY KEY,',
    '  `slug` VARCHAR(80) NOT NULL UNIQUE,',
    '  `label` VARCHAR(120) NOT NULL,',
    '  `table_name` VARCHAR(80) NOT NULL UNIQUE,',
    '  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
    '',
    'DELETE FROM `material_spaces`;',
    '',
  ];

  const summary = [];

  for (const sheet of sheets) {
    const result = buildSheetSql(sheet, sharedStrings);
    if (!result.sql) {
      continue;
    }

    parts.push(result.sql);
    parts.push(
      `INSERT INTO \`material_spaces\` (\`slug\`, \`label\`, \`table_name\`) VALUES ('${sqlEscape(slugify(sheet.name))}', '${sqlEscape(sheet.name)}', '${sqlEscape(sheet.tableName)}');`,
      ''
    );
    summary.push({ sheet: sheet.name, table: sheet.tableName, rows: result.rows });
  }

  const outFile = path.join(__dirname, 'import_inventory_by_space.sql');
  fs.writeFileSync(outFile, parts.join('\n'), 'utf8');

  console.log(`SQL écrit : ${outFile}`);
  console.log('\nRésumé :');
  summary.forEach((item) => {
    console.log(`- ${item.sheet} -> ${item.table} (${item.rows} lignes)`);
  });
}

main();
