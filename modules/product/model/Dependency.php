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
            WHERE d.product_id = ? AND d.deleted_at IS NULL
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

    public function softDelete(int $id): void {
        $stmt = $this->db->prepare("UPDATE product_dependency SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function restore(int $id): void {
        $stmt = $this->db->prepare("UPDATE product_dependency SET deleted_at = NULL WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function deleteDependencyPermanently(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM product_dependency WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function getDeletedDependencies(): array {
        $stmt = $this->db->query("
            SELECT d.*, 
               p.name AS dependency_name, 
               p.type AS dependency_type,
               main.name AS product_name
        FROM product_dependency d
        JOIN products p ON p.id = d.dependency_product_id
        JOIN products main ON main.id = d.product_id
        WHERE d.deleted_at IS NOT NULL
        ");
        return $stmt->fetchAll();
    }

    public function saveDependency(): void{
        $stmt = $this->db->query('
            INSER INTO 
                
                product_id,
                dependency_product_id, 
                quantity_multiplier,
                auto_add,
                note
            (?, ?, ?, ?, ?)
        ');
        $stmt->execute();
    }

    public function updateDependency(int $id, float $quantity, int $autoAdd, ?string $note): void {
        $stmt = $this->db->prepare("
            UPDATE product_dependency
            SET quantity_multiplier = ?, auto_add = ?, note = ?
            WHERE id = ?
        ");
        $stmt->execute([$quantity, $autoAdd, $note, $id]);
        error_log("Updating dependency ID $id: quantity=$quantity, auto_add=$autoAdd, note=$note");

    }
    
    
}
