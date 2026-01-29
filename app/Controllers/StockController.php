<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/Product.php';

class StockController
{
    private $model;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }
        $db = (new Database())->getConnection();
        $this->model = new Product($db);
    }

    public function index()
    {
        $products = $this->model->getAll($_SESSION['user_id']);
        $pageTitle = "Stok Yönetimi | Tersier ERP";
        ob_start();
        require_once '../app/Views/stock/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Ürün Oluştur
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'unit' => $_POST['unit'],
                'buy' => $_POST['buy'],
                'sell' => $_POST['sell'],
                'min' => $_POST['min']
            ];
            $this->model->create($data);
            header("Location: /stock/index?success=1");
        }
    }

    // Stok İşlemi (Giriş/Çıkış)
    // AJAX: Seçilen Müşteri ve Ürün için Fiyatı Getir
    public function get_price()
    {
        if (!isset($_POST['product_id'])) {
            echo 0;
            exit;
        }

        $pid = $_POST['product_id'];
        $cid = $_POST['contact_id'] ?? null; // Cari seçilmemiş olabilir

        echo $this->model->getPriceForCustomer($pid, $cid);
    }

    // Stok İşlemi (Güncellenmiş)
    public function movement()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type = $_POST['process_type'];
            // Giriş mi Çıkış mı?
            $is_entry = in_array($type, ['purchase', 'return_in', 'count_plus']) ? 1 : 0;

            $data = [
                'product_id'   => $_POST['product_id'],
                'contact_id'   => $_POST['contact_id'] ?: null, // Boşsa NULL yap
                'process_type' => $type,
                'is_entry'     => $is_entry,
                'quantity'     => $_POST['quantity'],
                'price'        => $_POST['price'], // Bu fiyat artık dinamik gelecek
                'document_no'  => $_POST['document_no'],
                'description'  => $_POST['description'],
                'maturity_date' => $_POST['maturity_date'] ?: date('Y-m-d'),
            ];

            $result = $this->model->addMovement($data);

            if ($result === true) {
                header("Location: /stock/index?success=moved");
            } else {
                header("Location: /stock/index?error=" . urlencode($result));
            }
        }
    }


    // Yeni Ürün Sayfasını Göster
    public function create_page()
    {
        $pageTitle = "Yeni Stok Kartı | Tersier ERP";
        ob_start();
        require_once '../app/Views/stock/create.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Ürünü Kaydet (POST işlemi)
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'code'        => $_POST['code'],
                'barcode'     => $_POST['barcode'],
                'name'        => $_POST['name'],
                'category'    => $_POST['category'],
                'brand'       => $_POST['brand'],
                'unit'        => $_POST['unit'],
                'vat'         => $_POST['vat'],
                'buy'         => $_POST['buy'],
                'sell'        => $_POST['sell'],
                'min'         => $_POST['min'],
                'description' => $_POST['description']
            ];

            if ($this->model->create($data)) {
                header("Location: /stock/index?success=created");
            } else {
                header("Location: /stock/create_page?error=failed");
            }
        }
    }

    // Carileri Getir (AJAX için)
    public function get_contacts()
    {
        require_once '../app/Models/Contact.php';
        $db = (new Database())->getConnection();
        $contactModel = new Contact($db);
        echo json_encode($contactModel->getAll($_SESSION['user_id']));
    }

    // Geçmiş (AJAX)
    public function history()
    {
        $id = $_GET['id'];
        echo json_encode($this->model->getMovements($id));
    }
}
