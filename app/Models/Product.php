<?php
class Product
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // BU FONKSİYON EKSİKTİ, BUNU EKLE:
    public function getById($id)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Builder sayfasında malzeme listesi için bu da lazım:
    public function getAll($user_id)
    {
        $sql = "SELECT * FROM products WHERE user_id = ? ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dashboard İstatistikleri (DÜZELTİLDİ)
    public function getStats($user_id)
    {
        // COALESCE eklendi: Sonuç yoksa 0 döndürür.
        $sql = "SELECT 
                    COUNT(id) as total_products,
                    COALESCE(SUM(current_stock * buy_price), 0) as total_value,
                    COALESCE(SUM(CASE WHEN current_stock <= min_stock_alert THEN 1 ELSE 0 END), 0) as critical_count
                FROM products WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Son 5 Hareket
        $sql2 = "SELECT sm.*, p.name as product_name 
                 FROM stock_movements sm 
                 JOIN products p ON sm.product_id = p.id 
                 WHERE sm.user_id = ? 
                 ORDER BY sm.created_at DESC LIMIT 5";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([$user_id]);
        $stats['recent_moves'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function getMovements($product_id)
    {
        $sql = "SELECT * FROM stock_movements WHERE product_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO products 
                (user_id, code, barcode, name, category, brand, unit, buy_price, sell_price, vat_rate, min_stock_alert, current_stock, description) 
                VALUES 
                (:uid, :code, :barcode, :name, :cat, :brand, :unit, :buy, :sell, :vat, :min, 0, :desc)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':uid'     => $_SESSION['user_id'],
            ':code'    => $data['code'],
            ':barcode' => $data['barcode'] ?? null,
            ':name'    => $data['name'],
            ':cat'     => $data['category'] ?? null,
            ':brand'   => $data['brand'] ?? null,
            ':unit'    => $data['unit'],
            ':buy'     => $data['buy'],
            ':sell'    => $data['sell'],
            ':vat'     => $data['vat'],
            ':min'     => $data['min'],
            ':desc'    => $data['description'] ?? null
        ]);
    }

    // AKILLI FİYAT GETİR (AJAX İçin)
    public function getPriceForCustomer($product_id, $contact_id)
    {
        // 1. Önce Özel Fiyat Var mı Bak
        if ($contact_id) {
            $sql = "SELECT special_price FROM customer_prices WHERE product_id = ? AND contact_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$product_id, $contact_id]);
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchColumn(); // Özel fiyatı döndür
            }
        }

        // 2. Yoksa Normal Satış Fiyatını Getir
        $sql = "SELECT sell_price FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchColumn();
    }

    // HAREKET EKLE (Bakiye Güncellemeli)
    public function addMovement($data)
    {
        // 1. Stok Miktarı Kontrolü (Sadece Çıkışlarda)
        if ($data['is_entry'] == 0) {
            $checkSql = "SELECT current_stock FROM products WHERE id = ?";
            $chk = $this->conn->prepare($checkSql);
            $chk->execute([$data['product_id']]);
            $current = $chk->fetchColumn();
            if ($current < $data['quantity']) return "Yetersiz Stok! Mevcut: $current";
        }

        try {
            $this->conn->beginTransaction();

            // 2. Stok Hareketini Kaydet (Log)
            $sql = "INSERT INTO stock_movements (user_id, product_id, contact_id, process_type, is_entry, quantity, price, document_no, description, maturity_date) 
                    VALUES (:uid, :pid, :cid, :type, :entry, :qty, :price, :doc, :desc, :mat_date)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uid' => $_SESSION['user_id'],
                ':pid' => $data['product_id'],
                ':cid' => $data['contact_id'] ?: null,
                ':type' => $data['process_type'],
                ':entry' => $data['is_entry'],
                ':qty' => $data['quantity'],
                ':price' => $data['price'],
                ':doc' => $data['document_no'],
                ':desc' => $data['description'],
                ':mat_date' => $data['maturity_date'] ?? date('Y-m-d')
            ]);

            // 3. Ürün Stoğunu Güncelle
            $op = ($data['is_entry'] == 1) ? '+' : '-';
            $updSql = "UPDATE products SET current_stock = current_stock $op :qty WHERE id = :pid";
            $this->conn->prepare($updSql)->execute([':qty' => $data['quantity'], ':pid' => $data['product_id']]);

            // 4. CARİ BAKİYESİNİ GÜNCELLE (EN ÖNEMLİ KISIM BURASI)
            if (!empty($data['contact_id'])) {
                $totalAmount = $data['quantity'] * $data['price'];

                // Mantık:
                // Satış (sale) -> Müşteri Borçlanır (Balance Artar +)
                // Alış (purchase) -> Biz Borçlanırız (Balance Azalır/Eksiye Gider -)

                $balanceOp = '+'; // Varsayılan
                if ($data['process_type'] == 'sale') {
                    $balanceOp = '+';
                } elseif ($data['process_type'] == 'purchase') {
                    $balanceOp = '-';
                }

                // Bakiyeyi güncelle
                $balSql = "UPDATE contacts SET balance = balance $balanceOp ? WHERE id = ?";
                $this->conn->prepare($balSql)->execute([$totalAmount, $data['contact_id']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Veritabanı Hatası: " . $e->getMessage();
        }
    }
}
