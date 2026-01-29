<?php
class WorkOrder
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // İş Emirlerini Getir
    public function getAll($user_id)
    {
        $sql = "SELECT wo.*, p.name as product_name, p.code as product_code, p.unit 
                FROM work_orders wo
                JOIN products p ON wo.product_id = p.id
                WHERE wo.user_id = ? 
                ORDER BY FIELD(wo.status, 'started', 'planned', 'completed', 'cancelled'), wo.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tekil Getir
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM work_orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // LOGLARI GETİR (YENİ)
    public function getLogs($work_order_id)
    {
        // Kullanıcı ismini (company_name) de çekelim ki kimin yaptığı görünsün
        $sql = "SELECT l.*, u.company_name as user_name 
                FROM work_order_logs l
                JOIN users u ON l.user_id = u.id
                WHERE l.work_order_id = ? 
                ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$work_order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // LOG EKLEME FONKSİYONU (YENİ)
    public function addLog($work_order_id, $action, $desc)
    {
        $sql = "INSERT INTO work_order_logs (work_order_id, user_id, action, description) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$work_order_id, $_SESSION['user_id'], $action, $desc]);
    }

    // Yeni İş Emri Oluştur (Loglu)
    public function create($data)
    {
        // Otomatik Kod
        $year = date('Y');
        $countSql = "SELECT COUNT(*) FROM work_orders WHERE user_id = ?";
        $stmt = $this->conn->prepare($countSql);
        $stmt->execute([$_SESSION['user_id']]);
        $count = $stmt->fetchColumn() + 1;
        $code = "WO-" . $year . "-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO work_orders (user_id, order_code, product_id, quantity, priority, planned_start_date, planned_end_date, notes) 
                VALUES (:uid, :code, :pid, :qty, :prio, :start, :end, :note)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':code' => $code,
            ':pid' => $data['product_id'],
            ':qty' => $data['quantity'],
            ':prio' => $data['priority'],
            ':start' => $data['planned_start_date'],
            ':end' => $data['planned_end_date'],
            ':note' => $data['notes']
        ]);

        if ($result) {
            $lastId = $this->conn->lastInsertId();
            $this->addLog($lastId, 'created', "İş emri oluşturuldu. Kod: $code, Hedef: {$data['quantity']} Adet");
            return true;
        }
        return false;
    }

    // Durum Güncelle (Loglu)
    public function updateStatus($id, $status)
    {
        $actualEnd = ($status == 'completed') ? date('Y-m-d') : null;

        $sql = "UPDATE work_orders SET status = ?, actual_end_date = IFNULL(actual_end_date, ?) WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$status, $actualEnd, $id]);

        if ($result) {
            // Duruma göre log mesajı
            $msgs = [
                'started' => 'Üretim süreci başlatıldı.',
                'completed' => 'Üretim başarıyla tamamlandı ve stoklara işlendi.',
                'cancelled' => 'İş emri iptal edildi.'
            ];
            $this->addLog($id, $status, $msgs[$status] ?? 'Durum değiştirildi.');
        }
        return $result;
    }
}
