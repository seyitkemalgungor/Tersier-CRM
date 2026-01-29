<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/WorkOrder.php';
require_once '../app/Models/Production.php';

class WorkOrderController
{
    private $db;
    private $model;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }
        $this->db = (new Database())->getConnection();
        $this->model = new WorkOrder($this->db);
    }

    public function index()
    {
        $orders = $this->model->getAll($_SESSION['user_id']);

        // Yeni emir formu için ürün listesi
        $prodModel = new Production($this->db);
        $producibles = $prodModel->getProducibleProducts($_SESSION['user_id']);

        $pageTitle = "İş Emirleri | Tersier ERP";
        ob_start();
        require_once '../app/Views/work_orders/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model->create([
                'product_id' => $_POST['product_id'],
                'quantity' => $_POST['quantity'],
                'priority' => $_POST['priority'],
                'planned_start_date' => $_POST['planned_start_date'],
                'planned_end_date' => $_POST['planned_end_date'],
                'notes' => $_POST['notes']
            ]);
            header("Location: /workorder/index?success=created");
        }
    }

    public function change_status()
    {
        $id = $_GET['id'];
        $status = $_GET['status'];

        if ($status == 'completed') {
            $order = $this->model->getById($id);
            if ($order['status'] == 'completed') {
                header("Location: /workorder/index?error=already_completed");
                exit;
            }

            // Stok Düşümü (MRP Motoru)
            $prodModel = new Production($this->db);
            $result = $prodModel->executeProduction(
                $_SESSION['user_id'],
                $order['product_id'],
                $order['quantity']
            );

            if ($result !== true) {
                header("Location: /workorder/index?error=" . urlencode($result));
                exit;
            }
        }

        $this->model->updateStatus($id, $status);
        header("Location: /workorder/index?success=status_updated");
    }

    // AJAX: Logları Getir
    public function get_logs()
    {
        $id = $_GET['id'];
        $logs = $this->model->getLogs($id);
        echo json_encode($logs);
    }
}
