<?php
class BmiModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function saveBmiRecord($userId, $name, $weight, $height, $bmi, $status) {
        $stmt = $this->db->prepare("INSERT INTO bmi_records (user_id, name, weight, height, bmi, status)
                                    VALUES (:user_id, :name, :weight, :height, :bmi, :status)");
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':weight' => $weight,
            ':height' => $height,
            ':bmi' => $bmi,
            ':status' => $status
        ]);
    }

    public function getUserBmiHistory($userId) {
        $stmt = $this->db->prepare("SELECT timestamp, bmi FROM bmi_records WHERE user_id = :user_id ORDER BY timestamp ASC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
