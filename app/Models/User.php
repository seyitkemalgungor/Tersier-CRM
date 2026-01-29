<?php
class User
{
    private $conn;
    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // E-posta daha önce alınmış mı?
    public function emailExists($email)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    // Yeni Kullanıcı Oluştur
    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (company_name, tax_id, email, password, ip_address) 
                  VALUES (:company, :tax, :email, :pass, :ip)";

        $stmt = $this->conn->prepare($query);

        // Şifreyi Hashle (Güvenlik)
        $hashed_password = password_hash($data['pass'], PASSWORD_DEFAULT);

        return $stmt->execute([
            ':company' => htmlspecialchars(strip_tags($data['company'])),
            ':tax'     => htmlspecialchars(strip_tags($data['tax'])),
            ':email'   => htmlspecialchars(strip_tags($data['email'])),
            ':pass'    => $hashed_password,
            ':ip'      => $_SERVER['REMOTE_ADDR']
        ]);
    }

    // Giriş Kontrolü
    public function login($email, $password)
    {
        // 1. E-postaya ait kullanıcıyı bul
        $query = "SELECT id, company_name, email, password FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            // 2. Şifre doğru mu?
            if (password_verify($password, $row['password'])) {
                unset($row['password']); // Güvenlik için şifreyi diziden çıkar
                return $row;
            }
        }
        return false;
    }
}
