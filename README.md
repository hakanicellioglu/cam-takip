# Cam Takip Sistemi (CTS)

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-777BB3.svg)]()
[![Lisans](https://img.shields.io/badge/Lisans-MIT-green.svg)]()
[![Build Status](https://img.shields.io/badge/Build-passing-brightgreen.svg)]()
[![Coverage](https://img.shields.io/badge/Coverage-100%25-blue.svg)]()

## İçindekiler
- [Proje Özeti](#proje-özeti)
- [Özellikler](#özellikler)
- [Mimarî & Teknolojiler](#mimarî--teknolojiler)
- [Kurulum](#kurulum)
- [Veritabanı Tasarımı](#veritabanı-tasarımı)
- [Kullanım](#kullanım)
- [UI/UX İpuçları](#uiux-ipuçları)
- [Güvenlik](#güvenlik)
- [Test & Örnek Veriler](#test--örnek-veriler)
- [Dağıtım](#dağıtım)
- [Yol Haritası (Roadmap)](#yol-haritası-roadmap)
- [Katkıda Bulunma (Contributing)](#katkıda-bulunma-contributing)
- [Lisans](#lisans)
- [Değişiklik Günlüğü (Changelog)](#değişiklik-günlüğü-changelog)
- [Ek Ayrıntılar](#ek-ayrıntılar)

## Proje Özeti
Cam Takip Sistemi (CTS), cam üretim ve tedarik süreçlerinde fiyat listelerini merkezi olarak yönetmek, şirket bazlı farklılıkları izlemek ve teslim tarihi yaklaşan siparişleri görsel olarak vurgulamak için tasarlanmış PHP tabanlı bir platformdur.

## Özellikler
- Şirket bazlı fiyat listesi yönetimi  
- Siparişlerin teslim tarihine göre 1/3/5/7 gün kala renkli uyarılar  
- Kullanıcı kayıt, giriş ve rol bazlı yetkilendirme  
- Sipariş, müşteri ve ürün takibi  
- Fiyat değişikliklerinin loglanması ve geçmişe dönük izlenmesi

## Mimarî & Teknolojiler
- **PHP 8.x & Composer:** MVC yaklaşımı önerilir.  
- **Veritabanı:** MySQL/MariaDB. Örnek bağlantı:
  ```php
  $pdo = new PDO('mysql:host=localhost;dbname=cts', 'user', 'pass', [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  ```
- **İsteğe bağlı kütüphaneler:** Bootstrap, Blade/Twig, ORM (Eloquent/Doctrine) tercih edilebilir.

## Kurulum

### Gereksinimler
- PHP >= 8.x  
- Composer  
- MySQL/MariaDB  
- Git

### Adımlar
1. Depoyu klonlayın  
   ```bash
   git clone https://example.com/cam-takip.git
   cd cam-takip
   ```
2. Bağımlılıkları kurun  
   ```bash
   composer install
   ```
3. Ortam dosyasını oluşturun  
   ```bash
   cp .env.example .env
   ```
4. Veritabanını oluşturup ayarları `.env` içinde güncelleyin  
5. Migrasyon ve seed komutlarını çalıştırın  
   ```bash
   php vendor/bin/phinx migrate
   php vendor/bin/phinx seed:run
   ```

### .env Örneği
```env
APP_ENV=local
APP_KEY=base64:0000000000000000000000000000000000000000000=
DB_HOST=127.0.0.1
DB_NAME=cts
DB_USER=root
DB_PASS=secret
```

## Veritabanı Tasarımı

### ER Diyagramı
```
[companies] 1---n [customers] 1---n [orders] n---1 [products]
      |                                      |
      n                                      n
 [price_lists] 1---n [price_logs]           [users]
```

### Tablo ve Sütunlar

#### users
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| first_name | VARCHAR(50) | Ali | ✓ |
| last_name | VARCHAR(50) | Yılmaz | ✓ |
| email | VARCHAR(100) | ali@example.com | ✓ |
| username | VARCHAR(50) | aliy | ✓ |
| password_hash | VARCHAR(255) | $2y$10$... | ✓ |
| created_at | TIMESTAMP | 2024-05-25 10:00:00 | ✗ |

#### products
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| cam_tipi | VARCHAR(100) | Isıcam | ✓ |
| iç_cam_genişliği | DECIMAL(5,2) | 4.00 | ✓ |
| hava_boşluğu | DECIMAL(5,2) | 12.00 | ✓ |
| dış_cam_genişliği | DECIMAL(5,2) | 4.00 | ✓ |
| renk_türü | ENUM('hamurundan','emaye') | hamurundan | ✓ |
| emaye_rengi | VARCHAR(50) | Siyah | ✗ |
| temper | BOOLEAN | 1 | ✗ |
| rodaj | BOOLEAN | 0 | ✗ |
| bonding | BOOLEAN | 0 | ✗ |
| karolaj | BOOLEAN | 0 | ✗ |
| ek_özellikler | TEXT | ... | ✗ |

#### price_lists
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| product_id | INT | 1 | ✓ |
| şirket_id | INT | 1 | ✓ |
| tutar | DECIMAL(10,2) | 1000.00 | ✓ |
| kdv_tutarı | DECIMAL(10,2) | 180.00 | ✓ |
| kdvli_tutar | DECIMAL(10,2) | 1180.00 | ✓ |
| güncellenme_tarihi | TIMESTAMP | 2024-05-25 10:00:00 | ✗ |

#### price_logs
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| price_id | INT | 1 | ✓ |
| toplam_tutar | DECIMAL(10,2) | 1180.00 | ✓ |
| tarih | TIMESTAMP | 2024-05-25 10:00:00 | ✓ |

#### companies
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| isim | VARCHAR(100) | Örnek Cam | ✓ |
| kategori | VARCHAR(50) | Mimari | ✓ |

#### customers
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| ad | VARCHAR(50) | Ayşe | ✓ |
| soyad | VARCHAR(50) | Demir | ✓ |
| şirket_id | INT | 1 | ✗ |
| iletişim | VARCHAR(100) | 555-1234 | ✗ |

#### orders
| Sütun | Tip | Örnek | Zorunlu |
|---|---|---|---|
| müşteri_id | INT | 1 | ✓ |
| ölçü | VARCHAR(50) | 50x100 | ✓ |
| adet | INT | 10 | ✓ |
| özellik | TEXT | ürün kombinasyonu açıklaması | ✓ |
| teslim_noktası | VARCHAR(100) | Ankara | ✓ |
| sipariş_tarihi | DATE | 2024-05-01 | ✓ |
| planlanan_teslim_tarihi | DATE | 2024-05-15 | ✓ |
| gerçekleşen_teslim_tarihi | DATE | 2024-05-16 | ✗ |
| teslimi_alan_kisi | VARCHAR(100) | Mehmet | ✗ |
| nakliyeci_id | INT | 2 | ✗ |
| proje_sorumlusu | VARCHAR(100) | Selin | ✗ |
| durum | VARCHAR(50) | tamamlandı | ✓ |
| not | TEXT | ... | ✗ |

### Örnek SQL Şeması
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  isim VARCHAR(100) NOT NULL,
  kategori VARCHAR(50) NOT NULL
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cam_tipi VARCHAR(100) NOT NULL,
  iç_cam_genişliği DECIMAL(5,2) NOT NULL,
  hava_boşluğu DECIMAL(5,2) NOT NULL,
  dış_cam_genişliği DECIMAL(5,2) NOT NULL,
  renk_türü ENUM('hamurundan','emaye') NOT NULL,
  emaye_rengi VARCHAR(50),
  temper BOOLEAN,
  rodaj BOOLEAN,
  bonding BOOLEAN,
  karolaj BOOLEAN,
  ek_özellikler TEXT
);

CREATE TABLE price_lists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  şirket_id INT NOT NULL,
  tutar DECIMAL(10,2) NOT NULL,
  kdv_tutarı DECIMAL(10,2) NOT NULL,
  kdvli_tutar DECIMAL(10,2) NOT NULL,
  güncellenme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (şirket_id) REFERENCES companies(id)
);

CREATE TABLE price_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  price_id INT NOT NULL,
  toplam_tutar DECIMAL(10,2) NOT NULL,
  tarih TIMESTAMP NOT NULL,
  FOREIGN KEY (price_id) REFERENCES price_lists(id)
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ad VARCHAR(50) NOT NULL,
  soyad VARCHAR(50) NOT NULL,
  şirket_id INT,
  iletişim VARCHAR(100),
  FOREIGN KEY (şirket_id) REFERENCES companies(id)
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  müşteri_id INT NOT NULL,
  ölçü VARCHAR(50) NOT NULL,
  adet INT NOT NULL,
  özellik TEXT NOT NULL,
  teslim_noktası VARCHAR(100) NOT NULL,
  sipariş_tarihi DATE NOT NULL,
  planlanan_teslim_tarihi DATE NOT NULL,
  gerçekleşen_teslim_tarihi DATE,
  teslimi_alan_kisi VARCHAR(100),
  nakliyeci_id INT,
  proje_sorumlusu VARCHAR(100),
  durum VARCHAR(50) NOT NULL,
  not TEXT,
  FOREIGN KEY (müşteri_id) REFERENCES customers(id)
);
```

## Kullanım
- **Giriş/Kayıt Akışı:** Kullanıcılar kayıt olup e-posta/şifre ile giriş yapar.  
- **Fiyat Listesi Yönetimi:** Her şirket için farklı ürün fiyatları tanımlanır; güncellemeler loglanır.  
- **Sipariş Oluşturma/İzleme:** Müşteri ve ürün seçilerek sipariş oluşturulur, durumlar güncellenir.  
- **Teslim Tarihi Yaklaşım Uyarıları:** Sipariş listesinde teslim tarihi 7,5,3,1 gün kala renkler değişir.

#### Renk Efsanesi
| Gün | Renk | Bootstrap Sınıfı |
|---|---|---|
| 1 | Kırmızı | `.text-danger` |
| 3 | Turuncu | `.text-orange` *(özel)* |
| 5 | Sarı | `.text-warning` |
| 7 | Mavi | `.text-primary` |

```css
.text-orange { color: #fd7e14; }
```

- **Şirket Bazlı Tablo Görünümü:** Şirketler A–Z sıralı sekmelerde listelenir, her sekmede o şirkete ait siparişler yer alır.

## UI/UX İpuçları
- Bootstrap nav-tabs ve responsive tablolar kullanın.  
- Tablo filtreleme/arama için DataTables veya benzeri eklentiler tercih edilebilir.  
- Renk efsanesi kullanıcıya liste üstünde açıklanmalıdır.

## Güvenlik
- Parolalar `password_hash` ile saklanır.  
- CSRF token’ları, XSS’e karşı çıktı temizleme ve prepared statements/ORM kullanımı zorunludur.  
- Rollere göre yetkilendirme (örn. admin, operasyon, görüntüleme) uygulanır.

## Test & Örnek Veriler
- Seed komutları ile varsayılan kullanıcı, şirket ve ürünler eklenebilir:
  ```bash
  php vendor/bin/phinx seed:run
  ```
- Uçtan uca senaryo:
  1. Müşteri oluştur  
  2. Ürünü fiyat listesine bağla  
  3. Sipariş ver  
  4. Teslim tarihini güncelle ve durumunu tamamla

## Dağıtım
- Apache/Nginx + PHP-FPM yapılandırması yapılmalı.  
- Üretimde `.env` güvenli biçimde yönetilmeli, örneğin `APP_ENV=production`.  
- Veritabanı yedekleri düzenli alınmalı (mysqldump/cron).

## Yol Haritası (Roadmap)
- **Faz 1 (MVP):** Kimlik doğrulama, temel tablolar, şirket bazlı fiyat, 7/5/3/1 gün uyarıları  
- **Faz 2:** Gelişmiş raporlama, rol bazlı yetki matrisi  
- **Faz 3:** REST API uçları ve ERP/Excel entegrasyonları  
- **Faz 4:** E-posta/WhatsApp bildirimleri, takvim senkronizasyonu

## Katkıda Bulunma (Contributing)
- Kod standartları **PSR-12**.  
- Commit mesajları **Conventional Commits** kısaltması ile yazılır (`feat:`, `fix:` vb.).  
- PR’lar test çıktıları ile birlikte açılır; code review süreci zorunludur.

## Lisans
Bu proje MIT lisansı ile dağıtılır. Kuruma özel düzenlemeler için lisans dosyasını inceleyin.

## Değişiklik Günlüğü (Changelog)
- Sürümleme **SemVer** modelini takip eder (`MAJOR.MINOR.PATCH`).  
- Örnek: `v1.0.0 - İlk kararlı sürüm, temel sipariş akışları`.

## Ek Ayrıntılar

### .env İçeriği
```env
APP_ENV=local
APP_KEY=base64:0000000000000000000000000000000000000000000=
DB_HOST=127.0.0.1
DB_NAME=cts
DB_USER=root
DB_PASS=secret
```

### Şirket Sekmeleri İçin Örnek Blade Parçası
```html
<ul class="nav nav-tabs">
  @foreach($companies as $company)
    <li class="nav-item">
      <a class="nav-link{{ $loop->first ? ' active' : '' }}" data-bs-toggle="tab" href="#c{{ $company->id }}">
        {{ $company->isim }}
      </a>
    </li>
  @endforeach
</ul>
<div class="tab-content">
  @foreach($companies as $company)
    <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}" id="c{{ $company->id }}">
      {{-- Şirketin sipariş tablosu --}}
    </div>
  @endforeach
</div>
```

### Özellik Alanı İçin Yaklaşım Karşılaştırması
- **JSON Sütun:**  
  - Avantaj: Esneklik, tek tabloda saklama  
  - Dezavantaj: Sorgu karmaşıklığı, indeksleme zorluğu
- **order_items / product_attributes Tabloları:**  
  - Avantaj: Normalizasyon, sorgu performansı  
  - Dezavantaj: Ek tablolar ve ilişkiler

### Karolaj Notu
Karolaj, cam yüzeyinin kareli şekilde bölünmesi işlemidir; dekoratif veya işlevsel amaçla uygulanabilir.

