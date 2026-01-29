<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/Contact.php';

class ContactController
{
    private $model;
    private $db;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }
        $this->db = (new Database())->getConnection();
        $this->model = new Contact($this->db);
    }

    // Cari Listesi (Ana Sayfa)
    public function index()
    {
        $contacts = $this->model->getAll($_SESSION['user_id']);

        // Özet Hesaplama
        $receivable = 0;
        $payable = 0;
        foreach ($contacts as $c) {
            if ($c['type'] == 'customer') $receivable += $c['balance'];
            if ($c['type'] == 'supplier') $payable += abs($c['balance']); // Tedarikçi borcu genelde eksi tutulur ama gösterim için mutlak değer
        }

        $pageTitle = "Cari Hesaplar | Tersier ERP";
        ob_start();
        require_once '../app/Views/contacts/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Cari Detay Sayfası (Ekstre ve Fiyatlar)
    public function detail()
    {
        $id = $_GET['id'];
        $contact = $this->model->getById($id);

        if (!$contact || $contact['user_id'] != $_SESSION['user_id']) {
            header("Location: /contact/index");
            exit;
        }

        // Ekstre ve Özel Fiyatlar
        $movements = $this->model->getStatement($id);
        $specialPrices = $this->model->getSpecialPrices($id);

        // Ürün Listesi (Fiyat tanımlarken lazım)
        require_once '../app/Models/Product.php';
        $prodModel = new Product($this->db);
        $products = $prodModel->getAll($_SESSION['user_id']);

        $pageTitle = $contact['title'] . " | Detay";
        ob_start();
        require_once '../app/Views/contacts/detail.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Yeni Cari Kaydet
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'type' => $_POST['type'],
                'title' => $_POST['title'],
                'tax_office' => $_POST['tax_office'],
                'tax_id' => $_POST['tax_id'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'city' => $_POST['city'],
                'address' => $_POST['address'],
                'risk_limit' => $_POST['risk_limit'],
                'payment_term' => $_POST['payment_term'],
                'iban' => $_POST['iban']
            ];

            if ($this->model->create($data)) {
                header("Location: /contact/index?success=created");
            } else {
                header("Location: /contact/index?error=failed");
            }
        }
    }

    // Özel Fiyat Kaydet
    public function save_price()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model->setSpecialPrice($_POST['contact_id'], $_POST['product_id'], $_POST['special_price']);
            header("Location: /contact/detail?id=" . $_POST['contact_id'] . "&tab=prices&success=1");
        }
    }

    // Özel Fiyat Sil
    public function delete_price()
    {
        $this->model->deleteSpecialPrice($_GET['id']);
        header("Location: /contact/detail?id=" . $_GET['cid'] . "&tab=prices");
    }

    // Cari Sil
    public function delete()
    {
        $this->model->delete($_GET['id']);
        header("Location: /contact/index?success=deleted");
    }
}
