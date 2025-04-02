<?php
namespace modules\product\model;

use PDO;
use base\model\Database;

class Product
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT p.*, 
                   (SELECT SUM(quantity) FROM product_warehouse pw WHERE pw.product_id = p.id) AS stock_quantity
            FROM products p
            ORDER BY name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithQuantity(?int $categoryId = null): array {
        if ($categoryId) {
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       (SELECT SUM(quantity) FROM product_warehouse pw WHERE pw.product_id = p.id) AS total_quantity
                FROM products p
                JOIN product_category pc ON pc.product_id = p.id
                WHERE pc.category_id = ?
                AND p.deleted_at IS NULL
                GROUP BY p.id
            ");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("
                SELECT p.*, 
                       (SELECT SUM(quantity) FROM product_warehouse pw WHERE pw.product_id = p.id) AS total_quantity
                FROM products p
                WHERE p.deleted_at IS NULL
                ORDER BY p.name
            ");
        }
    
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // načíst kategorie
        $catStmt = $this->db->prepare("SELECT category_id FROM product_category WHERE product_id = ?");
        foreach ($products as &$product) {
            $catStmt->execute([$product['id']]);
            $product['category_ids'] = array_column($catStmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
        }
    
        return $products;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $name, string $description, float $price, float $vat, string $type, string $image): int {
        $stmt = $this->db->prepare("
            INSERT INTO products (name, description, price, vat_rate, type, image, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $description, $price, $vat, $type, $image]);

        return intval($this->db->lastInsertId());
    }

    public function update(int $id, string $name, float $price, float $vat, string $type, ?string $imagePath = null): void {
        if ($imagePath) {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = ?, price = ?, vat_rate = ?, type = ?, image = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $price, $vat, $type, $imagePath, $id]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = ?, price = ?, vat_rate = ?, type = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$name, $price, $vat, $type, $id]);
        }
    }

    public function setWarehouseQuantity(int $productId, int $warehouseId, int $qty): void {
        $stmt = $this->db->prepare("
            INSERT INTO product_warehouse (product_id, warehouse_id, quantity)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)
        ");
        $stmt->execute([$productId, $warehouseId, $qty]);
    }

    public function getWarehouseQuantities(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT warehouse_id, quantity
            FROM product_warehouse
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[$row['warehouse_id']] = $row['quantity'];
        }

        return $result;
    }

    public function getAllWarehouses(): array {
        $stmt = $this->db->query("SELECT * FROM warehouses ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Nahrazuje dřívější fyzické smazání
    public function delete(int $id): void {
        $stmt = $this->db->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getTrash(): array {
        $stmt = $this->db->query("SELECT * FROM products WHERE deleted_at IS NOT NULL ORDER BY updated_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restore(int $id): void {
        $stmt = $this->db->prepare("UPDATE products SET deleted_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function restoreAll(): void {
        $this->db->query("UPDATE products SET deleted_at = NULL WHERE deleted_at IS NOT NULL");
    }

    public function deletePermanently(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function deleteAllPermanently(): void {
        $this->db->query("DELETE FROM products WHERE deleted_at IS NOT NULL");
    }

    // Získání závislostí produktu
public function getDependencies(int $productId): array {
    $stmt = $this->db->prepare("
        SELECT d.*, p.name AS dependency_name
        FROM product_dependency d
        JOIN products p ON p.id = d.dependency_product_id
        WHERE d.product_id = ?
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Přidání nebo úprava závislosti
public function saveDependency(int $productId, int $dependencyId, float $multiplier, bool $autoAdd, ?string $note): void {
    $stmt = $this->db->prepare("
        INSERT INTO product_dependency (product_id, dependency_product_id, quantity_multiplier, auto_add, note)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            quantity_multiplier = VALUES(quantity_multiplier),
            auto_add = VALUES(auto_add),
            note = VALUES(note)
    ");
    $stmt->execute([$productId, $dependencyId, $multiplier, $autoAdd ? 1 : 0, $note]);
}

// Smazání závislosti
public function deleteDependency(int $productId, int $dependencyId): void {
    $stmt = $this->db->prepare("
        DELETE FROM product_dependency
        WHERE product_id = ? AND dependency_product_id = ?
    ");
    $stmt->execute([$productId, $dependencyId]);
}


}
