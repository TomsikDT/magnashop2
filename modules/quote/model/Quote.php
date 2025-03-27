<?php
namespace modules\quote\model;

use base\model\Database;
use PDO;

class Quote
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createQuote(
        int $userId,
        string $customer,
        string $email,
        string $customerNote,
        string $noteTop,
        string $noteBottom,
        float $totalWOVat,
        float $totalWithVat,
        array $items,
        string $quoteNumber
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO quotes (
                user_id, customer_name, customer_email, customer_note,
                note_top, note_bottom, total_without_vat, total_with_vat, quote_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $customer,
            $email,
            $customerNote,
            $noteTop,
            $noteBottom,
            $totalWOVat,
            $totalWithVat,
            $quoteNumber
        ]);
    
        $quoteId = $this->db->lastInsertId();
    
        foreach ($items as $item) {
            $stmt = $this->db->prepare("
                INSERT INTO quote_items (
                    quote_id, product_id, product_name, qty,
                    price, vat_rate, total, total_vat
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $quoteId,
                $item['id'],
                $item['name'],
                $item['qty'],
                $item['price'],
                $item['vat_rate'],
                $item['total'],
                $item['total_vat']
            ]);
        }
    
        return $quoteId;
    }
    

    public function getAllQuotes(): array {
        $stmt = $this->db->query("SELECT * FROM quotes ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM quotes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function getItems(int $quoteId): array {
        $stmt = $this->db->prepare("SELECT * FROM quote_items WHERE quote_id = ?");
        $stmt->execute([$quoteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countQuotesForDate(string $date): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM quotes WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        return (int)$stmt->fetchColumn();
    }
}
