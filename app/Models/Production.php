<?php
class Production
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Üretilebilir Ürünleri Getir (Sadece Mamul ve Yarı Mamuller)
    public function getProducibleProducts($user_id)
    {
        $sql = "SELECT * FROM products WHERE user_id = ? AND product_type IN ('product', 'semi') ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reçeteyi Getir (Ağaç Yapısı)
    public function getRecipe($product_id)
    {
        $sql = "SELECT r.*, p.code, p.name, p.unit, p.current_stock, p.buy_price 
                FROM recipes r 
                JOIN products p ON r.ingredient_id = p.id 
                WHERE r.product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reçeteye Malzeme Ekle
    public function addIngredient($data)
    {
        // Aynı malzeme zaten ekli mi kontrol et
        $check = $this->conn->prepare("SELECT id FROM recipes WHERE product_id=? AND ingredient_id=?");
        $check->execute([$data['product_id'], $data['ingredient_id']]);
        if ($check->rowCount() > 0) return false;

        $sql = "INSERT INTO recipes (product_id, ingredient_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$data['product_id'], $data['ingredient_id'], $data['quantity']]);
    }

    // Reçeteden Malzeme Sil
    public function removeIngredient($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM recipes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ÜRETİMİ GERÇEKLEŞTİR (EN ÖNEMLİ KISIM)
    public function executeProduction($user_id, $product_id, $quantity)
    {
        try {
            // Transaction Başlat (Hata olursa her şeyi geri al)
            $this->conn->beginTransaction();

            // 1. Reçeteyi Çek
            $ingredients = $this->getRecipe($product_id);
            if (empty($ingredients)) throw new Exception("Bu ürünün reçetesi yok, üretim yapılamaz.");

            // 2. Stok Kontrolü ve Ham Madde Düşümü
            foreach ($ingredients as $item) {
                $needed = $item['quantity'] * $quantity;

                // Stok Yeterli mi?
                if ($item['current_stock'] < $needed) {
                    throw new Exception("Yetersiz Stok: {$item['name']} (Gereken: $needed, Mevcut: {$item['current_stock']})");
                }

                // Stoktan Düş (Çıkış Hareketi)
                $sqlLog = "INSERT INTO stock_movements (user_id, product_id, process_type, is_entry, quantity, price, description) 
                           VALUES (?, ?, 'production_out', 0, ?, ?, ?)";
                $stmtLog = $this->conn->prepare($sqlLog);
                $stmtLog->execute([
                    $user_id,
                    $item['ingredient_id'],
                    $needed,
                    $item['buy_price'], // Maliyet fiyatından düşüyoruz
                    "Üretim Çıkışı: Ürün #$product_id için"
                ]);

                // Ana Stoktan Düş
                $upd = $this->conn->prepare("UPDATE products SET current_stock = current_stock - ? WHERE id = ?");
                $upd->execute([$needed, $item['ingredient_id']]);
            }

            // 3. Ana Ürünü Stoğa Ekle (Giriş Hareketi)
            // Maliyet Hesabı (Basit toplama)
            $totalCost = 0;
            foreach ($ingredients as $item) $totalCost += ($item['quantity'] * $item['buy_price']);
            $unitCost = $totalCost; // 1 birim maliyeti

            $sqlProdLog = "INSERT INTO stock_movements (user_id, product_id, process_type, is_entry, quantity, price, description) 
                           VALUES (?, ?, 'production_in', 1, ?, ?, ?)";
            $stmtProdLog = $this->conn->prepare($sqlProdLog);
            $stmtProdLog->execute([
                $user_id,
                $product_id,
                $quantity,
                $unitCost,
                "Üretim Girişi"
            ]);

            $updMain = $this->conn->prepare("UPDATE products SET current_stock = current_stock + ? WHERE id = ?");
            $updMain->execute([$quantity, $product_id]);

            // 4. Üretim Kaydı
            $logProd = $this->conn->prepare("INSERT INTO productions (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $logProd->execute([$user_id, $product_id, $quantity]);

            // İşlemi Onayla
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Hata varsa değişiklikleri iptal et
            return $e->getMessage();
        }
    }
}
