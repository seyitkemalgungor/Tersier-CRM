<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/Finance.php';
require_once '../app/Models/Contact.php';

class FinanceController
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
        $this->model = new Finance($this->db);
    }

    public function index()
    {
        // 1. Filtreleri Al
        $filters = [
            'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
            'end_date'   => $_GET['end_date'] ?? date('Y-m-d'),
            'contact_id' => $_GET['contact_id'] ?? '',
            'type'       => $_GET['type'] ?? ''
        ];

        // 2. Verileri Çek
        $transactions = $this->model->getFilteredTransactions($_SESSION['user_id'], $filters);
        $daily = $this->model->getDailyStats($_SESSION['user_id']);

        // ALACAKLAR (Müşteriler, Bakiyesi > 0)
        $receivables = $this->model->getReceivablesList($_SESSION['user_id']);

        // BORÇLAR (Tedarikçiler, Bakiyesi < 0) - YENİ EKLENDİ
        // Modelde getPayablesList yoksa diye burada manuel sorgu yapmıyoruz,
        // Model dosyasında getPayablesList olduğundan eminiz (önceki adımlarda yazdık).
        $payables = $this->model->getPayablesList($_SESSION['user_id']);

        // 3. Cari Listesi
        $contactModel = new Contact($this->db);
        $contacts = $contactModel->getAll($_SESSION['user_id']);

        // 4. View
        $pageTitle = "Finans Yönetimi | Tersier ERP";
        ob_start();
        require_once '../app/Views/finance/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    public function save_transaction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model->addPayment([
                'contact_id' => $_POST['contact_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'payment_method' => $_POST['payment_method'],
                'payment_date' => $_POST['payment_date'],
                'description' => $_POST['description']
            ]);
            header("Location: /finance/index?success=saved");
        }
    }
}
