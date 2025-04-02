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

    public function getTree(int $parentId = null, int $level = 0): array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE parent_id " . ($parentId === null ? "IS NULL" : "= ?") . " AND deleted_at IS NULL ORDER BY name");
        $stmt->execute($parentId === null ? [] : [$parentId]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $tree = [];
        foreach ($categories as $category) {
            $category['level'] = $level;
            $tree[] = $category;
            $tree = array_merge($tree, $this->getTree((int)$category['id'], $level + 1));
        }
        return $tree;
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
    
    
}
