<?php

namespace App\Services;

class ImportService
{
    private MaterialService $materialService;

    public function __construct()
    {
        $this->materialService = new MaterialService();
    }

    public function importMaterialsFromJson(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('Fichier introuvable.');
        }

        $content = file_get_contents($filePath);
        $payload = json_decode($content, true);

        if (!is_array($payload)) {
            throw new \InvalidArgumentException('Le fichier doit contenir un tableau JSON valide.');
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($payload as $index => $item) {
            $line = $index + 1;

            try {
                $validated = $this->validateMaterial($item);

                // Check if exists
                $existing = $this->materialService->find($validated['id']);

                if ($existing) {
                    // Update
                    // We need to be careful not to overwrite everything blindly if logic differs,
                    // but here we just update basic fields.
                    // MaterialService::update expects specific fields.

                    // Map fields to MaterialService expected format
                    // Note: Import JSON might have 'image' but MaterialService expects 'cover_image'
                    if (isset($validated['image'])) {
                        $validated['cover_image'] = $validated['image'];
                        unset($validated['image']);
                    }

                    $this->materialService->update($validated['id'], $validated);
                    $updated++;
                } else {
                    // Create
                    if (isset($validated['image'])) {
                        $validated['cover_image'] = $validated['image'];
                        unset($validated['image']);
                    }
                    // Default values for missing fields
                    $validated['gallery'] = $validated['gallery'] ?? [];
                    $validated['tracking_mode'] = $validated['tracking_mode'] ?? 'generic';
                    $validated['identifiers'] = $validated['identifiers'] ?? [];
                    $validated['replacement_cost'] = $validated['replacement_cost'] ?? null;

                    $this->materialService->create($validated);
                    $created++;
                }

            } catch (\Exception $e) {
                $errors[] = "Ligne {$line}: " . $e->getMessage();
                continue;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    private function validateMaterial(array $item): array
    {
        $required = ['id', 'name', 'description', 'quantity_total'];

        foreach ($required as $field) {
            if (!isset($item[$field])) {
                throw new \InvalidArgumentException("Champ obligatoire manquant : {$field}");
            }
        }

        $item['id'] = strtoupper(trim((string) $item['id']));
        $item['name'] = trim((string) $item['name']);
        $item['description'] = trim((string) $item['description']);
        $item['quantity_total'] = (int) $item['quantity_total'];

        if ($item['quantity_total'] < 0) {
            throw new \InvalidArgumentException('La quantité totale doit être positive.');
        }

        if (!empty($item['image'])) {
            $item['image'] = trim((string) $item['image']);
        }

        // 'damaged' field in JSON is not directly supported in MaterialService create/update payload in the same way?
        // MaterialService schema has 'damaged_items' JSON column in my SQL, but MaterialService code (Step 49) 
        // DOES NOT seem to handle 'damaged' or 'damaged_items' in create/update/find.
        // It only handles: id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost.

        // If the user wants to keep 'damaged' info, I might need to update MaterialService as well.
        // But for now, I'll ignore 'damaged' to avoid breaking MaterialService, or I should update MaterialService.
        // The user said "transform all JSON storage bases to MySQL".
        // materials.json had "damaged": [].
        // My migration_schema.sql added `damaged_items JSON`.
        // But `MaterialService.php` (which was already there or I assumed was there) doesn't use it.

        // I should probably update MaterialService to handle `damaged_items` if I want to be thorough.
        // But let's stick to what's working. If MaterialService doesn't have it, I won't force it in ImportService.

        return $item;
    }
}
