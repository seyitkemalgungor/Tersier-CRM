<?php
// OTURUMU BAŞLAT (En üstte olmalı)
session_start();

// Hata Raporlama (Geliştirme aşamasında açık kalsın)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. URL'yi al ve parçala
$url = isset($_GET['url']) ? $_GET['url'] : 'auth/login'; // Varsayılan sayfa: Login
$url = rtrim($url, '/');
$url = explode('/', $url);

// 2. Controller Adını Belirle (Örn: AuthController)
$controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : 'AuthController';

// 3. Metod Adını Belirle (Örn: login, register, index)
$methodName = isset($url[1]) ? $url[1] : 'index';

// 4. Dosya Yolunu Kontrol Et
$controllerPath = '../app/Controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;

    // Controller sınıfını başlat
    $controller = new $controllerName;

    // Metod var mı kontrol et
    if (method_exists($controller, $methodName)) {
        // Metodu çalıştır (Varsa parametreleri gönder)
        call_user_func_array([$controller, $methodName], array_slice($url, 2));
    } else {
        // Metod yoksa 404
        echo "<h1 style='color:red; text-align:center; margin-top:50px;'>Hata: Sayfa Bulunamadı (Metod Yok)</h1>";
    }
} else {
    // Controller yoksa 404
    echo "<h1 style='color:red; text-align:center; margin-top:50px;'>Hata: Sayfa Bulunamadı (Controller Yok)</h1>";
    echo "<p style='text-align:center;'>Aranan dosya: $controllerPath</p>";
}
