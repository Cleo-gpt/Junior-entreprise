<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class InventorySpaceService
{
    private PDO $connection;
    private MaterialService $materials;

    /** Espaces affichés en tuiles sur le catalogue (ordre d’affichage). */
    private const CATALOG_SPACES = [
        'etagere_son' => [
            'label' => 'Étagère son',
            'short' => 'Son',
            'image' => 'img/spaces/son.svg',
            'accent' => '#1e5699',
        ],
        'etagere_lumiere' => [
            'label' => 'Étagère lumière',
            'short' => 'Lumière',
            'image' => 'img/spaces/lumiere.svg',
            'accent' => '#f0a500',
        ],
        'etagere_enreg_video' => [
            'label' => 'Étagère enreg. vidéo',
            'short' => 'Enreg. vidéo',
            'image' => 'img/spaces/enreg-video.svg',
            'accent' => '#2a9d8f',
        ],
        'etagere_support_video' => [
            'label' => 'Étagère support vidéo',
            'short' => 'Support vidéo',
            'image' => 'img/spaces/support-video.svg',
            'accent' => '#e76f51',
        ],
        'etagere_prod_studio' => [
            'label' => 'Étagère prod. studio',
            'short' => 'Prod. studio',
            'image' => 'img/spaces/prod-studio.svg',
            'accent' => '#6c5ce7',
        ],
    ];

    public function __construct()
    {
        $this->connection = Database::connection();
        $this->materials = new MaterialService();
    }

    public function catalogSpaces(): array
    {
        $spaces = [];

        foreach (self::CATALOG_SPACES as $slug => $meta) {
            $table = $this->resolveTableName($slug);
            $count = $table ? $this->countDistinctProducts($table) : 0;

            $spaces[] = [
                'slug' => $slug,
                'label' => $meta['label'],
                'short' => $meta['short'],
                'image' => $meta['image'],
                'accent' => $meta['accent'],
                'table_name' => $table,
                'count' => $count,
            ];
        }

        return $spaces;
    }

    public function findSpace(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '' || !isset(self::CATALOG_SPACES[$slug])) {
            return null;
        }

        $meta = self::CATALOG_SPACES[$slug];
        $table = $this->resolveTableName($slug);

        if (!$table) {
            return null;
        }

        return [
            'slug' => $slug,
            'label' => $meta['label'],
            'short' => $meta['short'],
            'image' => $meta['image'],
            'accent' => $meta['accent'],
            'table_name' => $table,
            'count' => $this->countDistinctProducts($table),
        ];
    }

    /**
     * Matériels regroupés par type (1 tuile = 1 modèle, pas 1 numéro de série).
     */
    public function productsForSpace(string $slug, ?string $search = null): array
    {
        $space = $this->findSpaceMeta($slug);
        if (!$space) {
            return [];
        }

        $table = $this->resolveTableName($slug);
        if (!$table || !$this->tableExists($table)) {
            return [];
        }

        $columns = $this->tableColumns($table);
        $orderBy = in_array('nom', $columns, true)
            ? 'ORDER BY nom IS NULL, nom ASC, id ASC'
            : (in_array('nom_du_materiel', $columns, true)
                ? 'ORDER BY nom_du_materiel IS NULL, nom_du_materiel ASC, id ASC'
                : 'ORDER BY id ASC');

        $sql = "SELECT * FROM `{$table}` {$orderBy}";
        $rows = $this->connection->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $groups = [];

        foreach ($rows as $row) {
            $item = $this->formatItem($row);
            $name = trim((string) ($item['nom'] ?: $item['nom_du_materiel'] ?: ''));
            if ($name === '') {
                continue;
            }

            $key = mb_strtolower($name);
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'name' => $name,
                    'nom_du_materiel' => $item['nom_du_materiel'],
                    'marque' => $item['marque'],
                    'modele' => $item['modele'],
                    'categorie' => $item['categorie'],
                    'description' => $item['description'],
                    'contenu' => $item['contenu'],
                    'prix_chf' => $item['prix_chf'],
                    'quantity_total' => 0,
                    'quantity_available' => 0,
                    'space_slug' => $slug,
                    'image' => $space['image'],
                    'accent' => $space['accent'],
                ];
            }

            $groups[$key]['quantity_total']++;

            if (!$this->isDamaged($item['etat'] ?? null)) {
                $groups[$key]['quantity_available']++;
            }

            // Enrichir les infos si absentes sur la 1ère ligne
            foreach (['marque', 'modele', 'categorie', 'description', 'contenu', 'prix_chf', 'nom_du_materiel'] as $field) {
                if (empty($groups[$key][$field]) && !empty($item[$field])) {
                    $groups[$key][$field] = $item[$field];
                }
            }
        }

        $products = [];
        foreach ($groups as $group) {
            $materialId = $this->productMaterialId($slug, $group['name']);
            $this->syncProductToMaterials($materialId, $group);

            $products[] = [
                'material_id' => $materialId,
                'name' => $group['name'],
                'nom_du_materiel' => $group['nom_du_materiel'],
                'marque' => $group['marque'],
                'modele' => $group['modele'],
                'categorie' => $group['categorie'],
                'description' => $this->buildDescription($group),
                'quantity_total' => $group['quantity_total'],
                'quantity_available' => $group['quantity_available'],
                'image' => $group['image'],
                'accent' => $group['accent'],
                'space_slug' => $slug,
            ];
        }

        usort($products, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        $search = $search !== null ? trim(mb_strtolower($search)) : '';
        if ($search === '') {
            return $products;
        }

        return array_values(array_filter($products, function (array $product) use ($search) {
            $haystack = mb_strtolower(implode(' ', [
                $product['name'],
                $product['nom_du_materiel'] ?? '',
                $product['marque'] ?? '',
                $product['modele'] ?? '',
                $product['categorie'] ?? '',
            ]));

            return str_contains($haystack, $search);
        }));
    }

    public function productMaterialId(string $spaceSlug, string $name): string
    {
        $hash = strtoupper(substr(md5($spaceSlug . '|' . mb_strtolower(trim($name))), 0, 10));

        return 'INV-' . $hash;
    }

    private function findSpaceMeta(string $slug): ?array
    {
        $slug = trim($slug);
        if (!isset(self::CATALOG_SPACES[$slug])) {
            return null;
        }

        return self::CATALOG_SPACES[$slug] + ['slug' => $slug];
    }

    private function syncProductToMaterials(string $materialId, array $group): void
    {
        $payload = [
            'id' => $materialId,
            'name' => $group['name'],
            'description' => $this->buildDescription($group) ?: $group['name'],
            'quantity_total' => max(0, (int) $group['quantity_available']),
            'cover_image' => null,
            'gallery' => [],
            'tracking_mode' => 'generic',
            'identifiers' => [],
            'replacement_cost' => $this->parsePrice($group['prix_chf'] ?? null),
            'damaged_items' => [],
        ];

        $existing = $this->materials->find($materialId);

        try {
            if ($existing) {
                $this->materials->update($materialId, $payload);
            } else {
                $this->materials->create($payload);
            }
        } catch (\Throwable $e) {
            // Ne bloque pas l'affichage du catalogue si la synchro échoue
        }
    }

    private function buildDescription(array $group): string
    {
        $parts = array_filter([
            trim((string) ($group['description'] ?? '')),
            !empty($group['contenu']) ? 'Contenu : ' . $group['contenu'] : '',
            !empty($group['marque']) ? 'Marque : ' . $group['marque'] : '',
            !empty($group['modele']) ? 'Modèle : ' . $group['modele'] : '',
        ]);

        return implode("\n", $parts);
    }

    private function parsePrice(?string $value): ?float
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (!preg_match('/(\d+(?:[.,]\d+)?)/', $value, $match)) {
            return null;
        }

        return (float) str_replace(',', '.', $match[1]);
    }

    private function isDamaged(?string $etat): bool
    {
        if ($etat === null || trim($etat) === '') {
            return false;
        }

        return (bool) preg_match('/abim|endomag|hors.?service|casse|cassé/i', $etat);
    }

    private function resolveTableName(string $slug): ?string
    {
        try {
            $stmt = $this->connection->prepare(
                'SELECT table_name FROM material_spaces WHERE slug = :slug LIMIT 1'
            );
            $stmt->execute(['slug' => $slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['table_name']) && $this->tableExists($row['table_name'])) {
                return $row['table_name'];
            }
        } catch (\PDOException $e) {
            // Fallback below
        }

        $fallback = 'materials_' . $slug;
        return $this->tableExists($fallback) ? $fallback : null;
    }

    private function tableExists(string $table): bool
    {
        if (!preg_match('/^[a-z0-9_]+$/i', $table)) {
            return false;
        }

        $stmt = $this->connection->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function countDistinctProducts(string $table): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $nameExpr = $this->productNameSqlExpression($table);
        if ($nameExpr === null) {
            return 0;
        }

        $sql = "SELECT COUNT(DISTINCT {$nameExpr}) FROM `{$table}` WHERE {$nameExpr} IS NOT NULL";

        return (int) $this->connection->query($sql)->fetchColumn();
    }

    private function countRows(string $table): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        return (int) $this->connection->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    }

    /**
     * Expression SQL pour le nom produit selon les colonnes réellement présentes.
     */
    private function productNameSqlExpression(string $table): ?string
    {
        $columns = $this->tableColumns($table);
        $parts = [];

        if (in_array('nom', $columns, true)) {
            $parts[] = "NULLIF(TRIM(`nom`), '')";
        }
        if (in_array('nom_du_materiel', $columns, true)) {
            $parts[] = "NULLIF(TRIM(`nom_du_materiel`), '')";
        }

        if ($parts === []) {
            return null;
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        return 'COALESCE(' . implode(', ', $parts) . ')';
    }

    private function tableColumns(string $table): array
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        $stmt = $this->connection->prepare(
            'SELECT COLUMN_NAME FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = :table_name'
        );
        $stmt->execute(['table_name' => $table]);
        $cache[$table] = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);

        return $cache[$table];
    }

    private function formatItem(array $row): array
    {
        return [
            'id' => $row['id'] ?? null,
            'nom' => $row['nom'] ?? null,
            'nom_du_materiel' => $row['nom_du_materiel'] ?? null,
            'marque' => $row['marque'] ?? null,
            'modele' => $row['modele'] ?? null,
            'categorie' => $row['categorie'] ?? null,
            'identifiants_individuels' => $row['identifiants_individuels'] ?? null,
            'identifiants_individuels_2' => $row['identifiants_individuels_2'] ?? null,
            'etat' => $row['etat'] ?? null,
            'prix_chf' => $row['prix_chf'] ?? null,
            'contenu' => $row['contenu'] ?? null,
            'description' => $row['description'] ?? null,
            'obj_manquant' => $row['obj_manquant'] ?? null,
        ];
    }
}
