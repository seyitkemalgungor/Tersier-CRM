<?php
require_once '../app/Config/Database.php';
require_once '../app/Models/User.php';

class AuthController
{

    // KAYIT SAYFASI
    public function register()
    {
        // Giriş yapmışsa Dashboard'a gönder
        if (isset($_SESSION['user_id'])) {
            header("Location: /dashboard");
            exit;
        }

        $message = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = (new Database())->getConnection();
            $user = new User($db);

            $data = [
                'company' => $_POST['company_name'],
                'tax'     => $_POST['tax_id'],
                'email'   => $_POST['email'],
                'pass'    => $_POST['password']
            ];

            if ($user->emailExists($data['email'])) {
                $message = '<div class="alert alert-warning">Bu e-posta zaten kullanımda.</div>';
            } else {
                if ($user->create($data)) {
                    // Kayıt başarılı, direkt giriş sayfasına at
                    header("Location: /auth/login?status=registered");
                    exit;
                } else {
                    $message = '<div class="alert alert-danger">Kayıt sırasında hata oluştu.</div>';
                }
            }
        }
        require_once '../app/Views/auth/register.php';
    }

    // GİRİŞ SAYFASI
    public function login()
    {
        // Giriş yapmışsa Dashboard'a gönder
        if (isset($_SESSION['user_id'])) {
            header("Location: /dashboard");
            exit;
        }

        $message = '';

        // Kayıttan geliyorsa başarı mesajı göster
        if (isset($_GET['status']) && $_GET['status'] == 'registered') {
            $message = '<div class="alert alert-success">Kayıt başarılı! Şimdi giriş yapabilirsiniz.</div>';
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = (new Database())->getConnection();
            $user = new User($db);

            $email = $_POST['email'];
            $password = $_POST['password'];

            $loggedInUser = $user->login($email, $password);

            if ($loggedInUser) {
                // OTURUM BAŞLAT
                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['company_name'] = $loggedInUser['company_name'];
                $_SESSION['email'] = $loggedInUser['email'];

                header("Location: /dashboard");
                exit;
            } else {
                $message = '<div class="alert alert-danger">E-posta veya şifre hatalı!</div>';
            }
        }
        require_once '../app/Views/auth/login.php';
    }

    // ÇIKIŞ İŞLEMİ
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /auth/login");
        exit;
    }
}
