<?php
namespace modules\warehouse\model;

use PDO;
use base\model\Database;

class Warehouse
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM warehouses ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithStock(): array {
        $stmt = $this->db->query("
            SELECT w.*, 
                   (SELECT COUNT(*) FROM product_warehouse pw WHERE pw.warehouse_id = w.id) AS product_count,
                   (SELECT SUM(pw.quantity) FROM product_warehouse pw WHERE pw.warehouse_id = w.id) AS total_quantity
            FROM warehouses w
            ORDER BY w.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM warehouses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    public function getProductsInWarehouse(int $warehouseId): array {
        $stmt = $this->db->prepare("
            SELECT p.*, pw.quantity
            FROM product_warehouse pw
            JOIN products p ON p.id = pw.product_id
            WHERE pw.warehouse_id = ?
            ORDER BY p.name
        ");
        $stmt->execute([$warehouseId]);
        return $stmt->fetchAll();
    }
    
}
