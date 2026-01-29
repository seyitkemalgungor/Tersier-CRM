# 🛡️ Tersier ERP - Yeni Nesil Üretim ve Finans Yönetim Sistemi

![Project Status](https://img.shields.io/badge/Status-Active-success)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4)
![License](https://img.shields.io/badge/License-MIT-blue)

**Tersier ERP**, üretim yapan teknoloji firmaları için özel olarak geliştirilmiş; modern arayüzlü, kullanıcı deneyimi (UX) odaklı ve yüksek performanslı bir web tabanlı yönetim panelidir. Standart muhasebe yazılımlarının aksine, **Cyberpunk/High-Tech** tasarım dili ve **Glassmorphism** efektleriyle şık bir çalışma ortamı sunar.

## 🚀 Öne Çıkan Özellikler

### 1. 🏭 Gelişmiş Üretim Merkezi (Visual BOM Builder)
* **Sürükle-Bırak Reçete Oluşturucu:** Ürün ağaçlarını (BOM) sıkıcı tablolarla değil, interaktif sürükle-bırak (Drag & Drop) arayüzü ile görsel olarak oluşturun.
* **Akıllı Stok Düşümü:** Üretim tamamlandığında hammaddeler stoktan otomatik düşer, bitmiş ürün stoğa eklenir.
* **İş Emirleri:** Üretim süreçlerini (Planlandı, Başladı, Bitti) anlık takip edin ve yönetin.

### 2. 📦 Stok ve Envanter Yönetimi
* **Kategorizasyon:** Ham madde, Yarı mamul ve Tam mamul ayrımı.
* **Kritik Stok Uyarısı:** Belirlenen seviyenin altına düşen ürünler için Dashboard ve liste bazlı görsel alarmlar.
* **Stok Hareketleri:** Giriş/Çıkış logları ve detaylı tarihçe.

### 3. 💰 Finans ve Kasa Yönetimi
* **Birleşik Hesap Ekstresi:** Cari bazlı fatura ve ödeme hareketlerini tek bir zaman tünelinde (Timeline) görüntüleyin.
* **Alacak/Verecek Takibi:** Müşteri ve Tedarikçi bakiyelerini ayrı sekmelerde, renk kodlu (Yeşil/Kırmızı) sistemle yönetin.
* **Anlık Nakit Akışı:** Günlük tahsilat ve ödeme performansını grafiklerle izleyin.

### 4. 🤝 CRM (Cari Hesaplar)
* **Müşteri & Tedarikçi Ayrımı:** Katı modül ayrımı ile listeler birbirine karışmaz.
* **Özel Fiyatlandırma:** Her müşteriye özel ürün fiyatı tanımlayabilme ve satışta otomatik fiyat getirme.
* **Risk Yönetimi:** Müşteri risk limitleri ve vade takibi.

### 5. 🎨 Modern Arayüz (UI/UX)
* **Koyu Mod (Dark Mode):** Göz yormayan, profesyonel siyah/turuncu konsept.
* **Glassmorphism:** Buzlu cam efektli kartlar ve paneller.
* **İnteraktif Grafikler:** ApexCharts ile güçlendirilmiş canlı veri görselleştirmesi.

---

## 🛠️ Teknolojiler ve Kütüphaneler

Bu proje, performans ve sürdürülebilirlik odaklı modern teknolojilerle geliştirilmiştir:

* **Backend:** Native PHP (MVC Mimarisi), PDO MySQL
* **Frontend:** HTML5, CSS3 (Custom Properties), JavaScript (ES6+)
* **Framework:** Bootstrap 5 (Özelleştirilmiş)
* **Veritabanı:** MySQL / MariaDB
* **Kütüphaneler:**
    * `SortableJS` (Sürükle Bırak İşlemleri için)
    * `ApexCharts` (Finansal Grafikler için)
    * `SweetAlert2` (Modern Popup Bildirimleri için)
    * `FontAwesome 6` (İkon Seti)
    * `Google Fonts` (Inter Font Ailesi)

---

## 📸 Ekran Görüntüleri

| Dashboard | Üretim Ağacı |
|-----------|--------------|
| *Dashboard, nakit akışı ve kritik stok uyarılarını içerir.* | *Sürükle-bırak ile reçete oluşturma ekranı.* |

| Finans Yönetimi | Stok Listesi |
|-----------------|--------------|
| *Detaylı kasa hareketleri ve filtreleme.* | *Kritik stok uyarılı envanter takibi.* |

---

## ⚙️ Kurulum

Projeyi yerel makinenizde çalıştırmak için aşağıdaki adımları izleyin:

1.  **Repoyu Klonlayın:**
    ```bash
    git clone https://github.com/seyitkemalgungor/Tersier-CRM.git
    ```
2.  **Veritabanını İçe Aktarın:**
    * `phpMyAdmin` veya tercih ettiğiniz bir SQL istemcisini açın.
    * `tersier_db` adında bir veritabanı oluşturun.
    * Proje dizinindeki `database.sql` dosyasını içe aktarın.
3.  **Veritabanı Bağlantısını Yapılandırın:**
    * `app/Config/Database.php` dosyasını açın ve yerel sunucu bilgilerinizi girin:
    ```php
    private $host = "localhost";
    private $db_name = "tersier_db";
    private $username = "root";
    private $password = "";
    ```
4.  **Çalıştırın:**
    * Tarayıcınızda `http://localhost/tersier-crm` (veya kurduğunuz klasör adı) adresine gidin.
    * **Varsayılan Giriş:** (Kayıt ol sayfasından yeni kullanıcı oluşturabilirsiniz).

---

## 📄 Lisans

Bu proje [MIT License](LICENSE) altında lisanslanmıştır. Açık kaynak olarak geliştirilmeye ve katkıya açıktır.

---

<p align="center">
  <sub>Developed with ❤️ by Tersier Technology</sub>
</p>
