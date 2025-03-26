<?php
namespace modules\admin\model;

use PDO;

class Setting {
    protected PDO $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=magnashop2;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function get(string $module, string $key): ?string {
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE module = ? AND setting_key = ?");
        $stmt->execute([$module, $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['setting_value'] ?? null;
    }

    public function set(string $module, string $key, string $value): void {
        $stmt = $this->db->prepare("REPLACE INTO settings (module, setting_key, setting_value) VALUES (?, ?, ?)");
        $stmt->execute([$module, $key, $value]);
    }
}
