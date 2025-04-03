<?php
namespace modules\product\model;

use base\model\Database;
use PDO;

class Category
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForProduct(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT c.*
            FROM categories c
            JOIN product_category pc ON c.id = pc.category_id
            WHERE pc.product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveForProduct(int $productId, array $categoryIds): void {
        // Smažeme staré kategorie
        $stmt = $this->db->prepare("DELETE FROM product_category WHERE product_id = ?");
        $stmt->execute([$productId]);
        error_log("Ukládám kategorie pro produkt ID $productId: " . implode(', ', $categoryIds));

        // Vložíme nové
        $stmt = $this->db->prepare("INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $categoryId) {
            $stmt->execute([(int)$productId, (int)$categoryId]);
            error_log("Ukládám kategorie pro produkt ID foreach $productId: " . implode(', ', $categoryIds));

        }
        
    }

    public function getTree(?int $warehouseId = null, int $parentId = null, int $level = 0): array {
        $query = "SELECT * FROM categories WHERE deleted_at IS NULL";
        $params = [];
    
        if ($warehouseId !== null) {
            $query .= " AND warehouse_id = ?";
            $params[] = $warehouseId;
        }
    
        if ($parentId === null) {
            $query .= " AND parent_id IS NULL";
        } else {
            $query .= " AND parent_id = ?";
            $params[] = $parentId;
        }
    
        $query .= " ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $tree = [];
        foreach ($categories as $category) {
            $category['level'] = $level;
            $tree[] = $category;
            $tree = array_merge($tree, $this->getTree($warehouseId, (int)$category['id'], $level + 1));
        }
    
        return $tree;
    }
    

    public function getTreeByWarehouse(): array {
        $stmt = $this->db->query("
            SELECT * 
            FROM categories 
            WHERE deleted_at IS NULL 
            ORDER BY warehouse_id, parent_id, name
        ");
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $grouped = [];
        foreach ($all as $cat) {
            $cat['level'] = 0;
            $grouped[$cat['warehouse_id']][$cat['id']] = $cat;
        }
    
        // Sestavit stromy pro každý sklad
        $trees = [];
        foreach ($grouped as $warehouseId => $cats) {
            $trees[$warehouseId] = [];
    
            foreach ($cats as $cat) {
                if ($cat['parent_id'] === null) {
                    $trees[$warehouseId][] = self::buildSubtree($cat, $cats, 0);
                }
            }
        }
    
        // Zarovnáme do plochého seznamu seřazeného dle stromu
        $flatten = [];
        foreach ($trees as $warehouseId => $roots) {
            foreach ($roots as $node) {
                self::flattenTree($flatten, $node);
            }
        }
    
        return $flatten;
    }
    
    private static function buildSubtree(array $cat, array $all, int $level): array {
        $cat['level'] = $level;
        $cat['children'] = [];
    
        foreach ($all as $child) {
            if ($child['parent_id'] == $cat['id']) {
                $cat['children'][] = self::buildSubtree($child, $all, $level + 1);
            }
        }
    
        return $cat;
    }
    
    private static function flattenTree(array &$flat, array $node): void {
        $copy = $node;
        unset($copy['children']);
        $flat[] = $copy;
    
        foreach ($node['children'] as $child) {
            self::flattenTree($flat, $child);
        }
    }
    
    public function getTreeGroupedByWarehouse(): array {
        $stmt = $this->db->query("SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY warehouse_id, name");
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $grouped = [];
        foreach ($all as $cat) {
            $grouped[$cat['warehouse_id']][$cat['id']] = $cat;
        }
    
        // Vytvoříme strom pro každý sklad
        $trees = [];
        foreach ($grouped as $warehouseId => $cats) {
            $trees[$warehouseId] = [];
            foreach ($cats as $cat) {
                if ($cat['parent_id'] === null) {
                    $trees[$warehouseId][] = $this->buildCategoryNode($cat, $cats, 0);
                }
            }
        }
    
        return $trees;
    }
    
    private function buildCategoryNode(array $cat, array $all, int $level): array {
        $cat['level'] = $level;
        $cat['children'] = [];
    
        foreach ($all as $child) {
            if ($child['parent_id'] == $cat['id']) {
                $cat['children'][] = $this->buildCategoryNode($child, $all, $level + 1);
            }
        }
    
        return $cat;
    }
    
    
    

    public function create(string $name, ?int $parentId = null): void {
        $stmt = $this->db->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->execute([$name, $parentId]);
    }

    public function softDelete(int $id): void {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function restore(int $id): void {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function deletePermanently(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function getDeleted(): array {
        $stmt = $this->db->query("SELECT * FROM categories WHERE deleted_at IS NOT NULL ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update(int $id, string $name, ?int $parentId = null): void {
        $stmt = $this->db->prepare("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?");
        $stmt->execute([$name, $parentId, $id]);
    }
    
    public function getBreadcrumb(int $id): array {
        $breadcrumb = [];
        while($id){
            $stmt = $this->db->prepare("SELECT id, name, parent_id FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row) break;

            array_unshift($breadcrumb, $row);
            $id = $row['parent_id'];
        }
        return $breadcrumb;
    }
    
}
