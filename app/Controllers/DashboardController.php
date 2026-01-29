<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/Product.php';

class DashboardController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }

        $db = (new Database())->getConnection();
        $model = new Product($db);

        // İstatistikleri Çek
        $stats = $model->getStats($_SESSION['user_id']);

        $pageTitle = "Dashboard | Tersier ERP";
        ob_start();
        require_once '../app/Views/dashboard/index.php';
        $content = ob_get_clean();
        require_once '../app/Views/layout/main.php';
    }
}
