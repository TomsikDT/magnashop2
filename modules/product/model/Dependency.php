<?php
namespace modules\product\model;

use base\model\Database;
use PDO;

class Dependency
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getDependencies(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT d.*, p.name AS dependency_name, p.type AS dependency_type
            FROM product_dependency d
            JOIN products p ON p.id = d.dependency_product_id
            WHERE d.product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addDependency(int $productId, int $depId, float $multiplier, int $autoAdd, ?string $note): void {
        $stmt = $this->db->prepare("
            INSERT INTO product_dependency (product_id, dependency_product_id, quantity_multiplier, auto_add, note)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$productId, $depId, $multiplier, $autoAdd, $note]);
    }
}
