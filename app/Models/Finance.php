<?php
class Finance
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 1. GELİŞMİŞ HAREKET RAPORU (DÜZELTİLDİ)
    public function getFilteredTransactions($user_id, $filters)
    {
        $sql = "SELECT 
                    unified_table.*, 
                    COALESCE(c.title, 'Bilinmeyen Cari') as contact_name,
                    c.type as contact_type
                FROM (
                    -- KASA HAREKETLERİ
                    SELECT 
                        p.payment_date as date, 
                        p.type as type, 
                        p.amount, 
                        p.description, 
                        p.contact_id, 
                        p.user_id,
                        'payment' as source,
                        p.payment_method as method
                    FROM payments p
                    
                    UNION ALL
                    
                    -- STOK FATURALARI
                    SELECT 
                        sm.created_at as date, 
                        sm.process_type as type, 
                        (sm.quantity * sm.price) as amount, 
                        -- DÜZELTME BURADA: (0 + sm.quantity) diyerek gereksiz sıfırları MySQL'de atıyoruz
                        CONCAT(p.name, ' (', (0 + sm.quantity), ' ', p.unit, ')') as description,
                        sm.contact_id, 
                        sm.user_id,
                        'invoice' as source,
                        'Fatura' as method
                    FROM stock_movements sm
                    JOIN products p ON sm.product_id = p.id
                    WHERE sm.process_type IN ('sale', 'purchase', 'return_in', 'return_out') 
                    AND sm.contact_id IS NOT NULL
                ) as unified_table
                LEFT JOIN contacts c ON unified_table.contact_id = c.id
                WHERE unified_table.user_id = :uid";

        $params = [':uid' => $user_id];

        if (!empty($filters['start_date'])) {
            $sql .= " AND unified_table.date >= :start";
            $params[':start'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND unified_table.date <= :end";
            $params[':end'] = $filters['end_date'] . ' 23:59:59';
        }
        if (!empty($filters['contact_id'])) {
            $sql .= " AND unified_table.contact_id = :cid";
            $params[':cid'] = $filters['contact_id'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND unified_table.type = :type";
            $params[':type'] = $filters['type'];
        }

        $sql .= " ORDER BY unified_table.date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. ALACAKLAR (Müşteriler)
    public function getReceivablesList($user_id)
    {
        $sql = "SELECT id, title, phone, balance FROM contacts WHERE user_id = ? AND balance > 0.01 ORDER BY balance DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. BORÇLAR (Tedarikçiler)
    public function getPayablesList($user_id)
    {
        $sql = "SELECT id, title, phone, balance FROM contacts WHERE user_id = ? AND balance < -0.01 ORDER BY balance ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. GÜNLÜK KASA
    public function getDailyStats($user_id)
    {
        $today = date('Y-m-d');
        $in = $this->conn->prepare("SELECT SUM(amount) FROM payments WHERE user_id = ? AND type = 'collection' AND payment_date = ?");
        $in->execute([$user_id, $today]);

        $out = $this->conn->prepare("SELECT SUM(amount) FROM payments WHERE user_id = ? AND type = 'payment' AND payment_date = ?");
        $out->execute([$user_id, $today]);

        return ['in' => $in->fetchColumn() ?: 0, 'out' => $out->fetchColumn() ?: 0];
    }

    // 5. İŞLEM KAYDETME
    public function addPayment($data)
    {
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO payments (user_id, contact_id, type, amount, payment_method, description, payment_date) VALUES (:uid, :cid, :type, :amt, :method, :desc, :date)");
            $stmt->execute([':uid' => $_SESSION['user_id'], ':cid' => $data['contact_id'], ':type' => $data['type'], ':amt' => $data['amount'], ':method' => $data['payment_method'], ':desc' => $data['description'], ':date' => $data['payment_date']]);

            $op = ($data['type'] == 'collection') ? '-' : '+';
            $this->conn->prepare("UPDATE contacts SET balance = balance $op ? WHERE id = ?")->execute([$data['amount'], $data['contact_id']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
