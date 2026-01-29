<?php
class Contact
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Tüm Carileri Getir
    public function getAll($user_id)
    {
        $sql = "SELECT * FROM contacts WHERE user_id = ? ORDER BY title ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tek Bir Cariyi Getir
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Yeni Cari Oluştur (Detaylı)
    public function create($data)
    {
        $sql = "INSERT INTO contacts 
                (user_id, type, title, tax_office, tax_id, email, phone, city, address, risk_limit, payment_term, iban) 
                VALUES 
                (:uid, :type, :title, :tax_off, :tax_id, :email, :phone, :city, :addr, :risk, :term, :iban)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':type' => $data['type'],
            ':title' => $data['title'],
            ':tax_off' => $data['tax_office'],
            ':tax_id' => $data['tax_id'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':city' => $data['city'],
            ':addr' => $data['address'],
            ':risk' => $data['risk_limit'] ?? 0,
            ':term' => $data['payment_term'] ?? 0,
            ':iban' => $data['iban'] ?? null
        ]);
    }

    // Cari Ekstresi (Hareket Geçmişi)
    public function getStatement($contact_id)
    {
        // Stok hareketlerinden bu cariye ait işlemleri çekiyoruz
        $sql = "SELECT sm.*, p.name as product_name, p.code as product_code 
                FROM stock_movements sm
                JOIN products p ON sm.product_id = p.id
                WHERE sm.contact_id = ? 
                ORDER BY sm.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$contact_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Özel Fiyat Tanımla / Güncelle
    public function setSpecialPrice($contact_id, $product_id, $price)
    {
        // Önce var mı kontrol et
        $check = $this->conn->prepare("SELECT id FROM customer_prices WHERE contact_id=? AND product_id=?");
        $check->execute([$contact_id, $product_id]);

        if ($check->rowCount() > 0) {
            $sql = "UPDATE customer_prices SET special_price = ? WHERE contact_id=? AND product_id=?";
            return $this->conn->prepare($sql)->execute([$price, $contact_id, $product_id]);
        } else {
            $sql = "INSERT INTO customer_prices (user_id, contact_id, product_id, special_price) VALUES (?, ?, ?, ?)";
            return $this->conn->prepare($sql)->execute([$_SESSION['user_id'], $contact_id, $product_id, $price]);
        }
    }

    // Özel Fiyatları Listele
    public function getSpecialPrices($contact_id)
    {
        $sql = "SELECT cp.*, p.name as product_name, p.code as product_code, p.sell_price as list_price
                FROM customer_prices cp
                JOIN products p ON cp.product_id = p.id
                WHERE cp.contact_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$contact_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Özel Fiyat Sil
    public function deleteSpecialPrice($id)
    {
        return $this->conn->prepare("DELETE FROM customer_prices WHERE id=?")->execute([$id]);
    }

    // Cari Sil
    public function delete($id)
    {
        return $this->conn->prepare("DELETE FROM contacts WHERE id=?")->execute([$id]);
    }
}
