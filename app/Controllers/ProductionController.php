<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/Production.php';
require_once '../app/Models/Product.php'; // <--- BU SATIR ÖNEMLİ

class ProductionController
{
    private $db;
    private $model;
    private $productModel; // <--- BU DEĞİŞKEN ÖNEMLİ

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }

        $this->db = (new Database())->getConnection();
        $this->model = new Production($this->db);
        $this->productModel = new Product($this->db); // <--- BU TANIMLAMA ÖNEMLİ
    }

    // Üretim Dashboard (Listeleme)
    public function index()
    {
        $producibles = $this->model->getProducibleProducts($_SESSION['user_id']);
        $pageTitle = "Üretim Merkezi | Tersier ERP";
        ob_start();
        require_once '../app/Views/production/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Reçete Düzenleme Sayfası (Ağaç Yapısı)
    public function recipe($id)
    {
        $product = $this->productModel->getAll($_SESSION['user_id']); // Tüm ürünler (Ham maddeleri seçmek için)
        // Ana ürünü bul
        $mainProduct = null;
        foreach ($product as $p) {
            if ($p['id'] == $id) $mainProduct = $p;
        }

        $recipe = $this->model->getRecipe($id);

        $pageTitle = "Reçete: " . $mainProduct['name'];
        ob_start();
        require_once '../app/Views/production/recipe.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // Reçeteye Ekle (POST)
    public function add_ingredient()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model->addIngredient([
                'product_id' => $_POST['product_id'],
                'ingredient_id' => $_POST['ingredient_id'],
                'quantity' => $_POST['quantity']
            ]);
            header("Location: /production/recipe/" . $_POST['product_id']);
        }
    }

    // Reçeteden Sil (GET)
    public function delete_ingredient()
    {
        $id = $_GET['id'];
        $pid = $_GET['pid'];
        $this->model->removeIngredient($id);
        header("Location: /production/recipe/" . $pid);
    }

    // Üretim Yap (POST)
    public function produce()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->model->executeProduction(
                $_SESSION['user_id'],
                $_POST['product_id'],
                $_POST['quantity']
            );

            if ($result === true) {
                header("Location: /production/index?success=produced");
            } else {
                // Hata mesajı ile geri dön
                header("Location: /production/index?error=" . urlencode($result));
            }
        }
    }


    // GÖRSEL REÇETE OLUŞTURUCU SAYFASI
    public function builder($id)
    {
        // Ana ürünü bul
        $mainProduct = $this->productModel->getById($id);

        // Eklenebilir Malzemeler (Kendisi hariç diğerleri)
        $materials = $this->productModel->getAll($_SESSION['user_id']);
        $materials = array_filter($materials, fn($m) => $m['id'] != $id); // Kendini listeden çıkar

        // Mevcut Reçeteyi Getir
        $recipe = $this->model->getRecipe($id);

        $pageTitle = "Görsel Reçete: " . $mainProduct['name'];
        ob_start();
        require_once '../app/Views/production/builder.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }

    // AJAX: Malzeme Ekle (Sürükle Bırak Sonrası)
    public function ajax_add_ingredient()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $result = $this->model->addIngredient([
            'product_id' => $data['product_id'],
            'ingredient_id' => $data['ingredient_id'],
            'quantity' => $data['quantity']
        ]);

        echo json_encode(['status' => $result ? 'success' : 'error']);
    }

    // AJAX: Malzeme Sil
    public function ajax_delete_ingredient()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Modelde delete fonksiyonu ID ile çalışıyordu, buraya özel bir silme gerekebilir
        // Veya ID'yi bulup silebiliriz. Hızlı çözüm için SQL çalıştıralım:
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("DELETE FROM recipes WHERE product_id = ? AND ingredient_id = ?");
        $result = $stmt->execute([$data['product_id'], $data['ingredient_id']]);

        echo json_encode(['status' => $result ? 'success' : 'error']);
    }
}
