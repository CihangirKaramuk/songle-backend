# Songle Backend API 🚀

Songle, şarkı tahmin oyunu için gelişmiş bir PHP backend API'sidir. Bu API, şarkı yönetimi, kullanıcı yönetimi, kategori yönetimi ve kapsamlı işlem kayıtları sağlar.

## ✨ Özellikler

### 🎵 Şarkı Yönetimi

- Şarkı listeleme ve filtreleme
- Kategoriye göre şarkı filtreleme
- Yeni şarkı ekleme (manuel, Deezer, MP3)
- Mevcut şarkıyı güncelleme
- Şarkı silme
- Album kapak yönetimi

### 👥 Kullanıcı Yönetimi

- Yetkili kullanıcı ekleme/silme/güncelleme
- Rol tabanlı erişim kontrolü
- Şifre sıfırlama
- Kullanıcı rolü değiştirme
- Güvenli oturum yönetimi

### 📁 Kategori Yönetimi

- Ana kategori ve alt kategori yönetimi
- Kategori ekleme/silme/güncelleme
- Şarkı-kategori ilişkilendirme

### 📊 İşlem Kayıtları

- Tüm admin işlemlerinin detaylı loglanması
- Kullanıcı aktivite takibi
- İşlem tipi ve kaynak filtreleme
- Zaman damgalı kayıtlar
- Hedef kullanıcı bilgileri

### 🔗 Deezer Entegrasyonu

- Deezer API ile şarkı arama
- Otomatik şarkı indirme
- Album kapak otomatik yükleme

## 🚀 Kurulum

### Gereksinimler

- XAMPP veya benzeri bir web sunucusu (PHP 7.4+)
- MySQL 5.7+ veya MariaDB 10.2+
- PHP MySQL extension
- PHP cURL extension

### Kurulum Adımları

1. **Projeyi kopyalayın**

   ```bash
   cd c:\xampp\htdocs
   git clone [repo-url] songle-backend
   ```

2. **Veritabanını oluşturun**

   - PhpMyAdmin'de yeni veritabanı oluşturun: `songle`
   - `songle.sql` dosyasını import edin
   - Veya `config/database.php` üzerinden otomatik kurulum

3. **Yapılandırmayı kontrol edin**

   - `config/database.php` dosyasındaki veritabanı bilgilerini güncelleyin
   - API base URL'ini kontrol edin

4. **Test edin**
   ```bash
   http://localhost/songle-backend/
   ```

## 🔌 API Dokümantasyonu

### Temel Endpoint'ler

#### 🎵 Şarkı Yönetimi

- **GET** `/api/songs.php` - Tüm şarkıları listeler
- **GET** `/api/songs.php?kategori=turkce-rock` - Kategoriye göre filtreler
- **POST** `/api/songs.php` - Yeni şarkı ekler
- **PUT** `/api/songs.php` - Şarkı günceller
- **DELETE** `/api/songs.php?id=1` - Şarkı siler

#### 👥 Kullanıcı Yönetimi

- **POST** `/api/kullanicilar.php` - Kullanıcı CRUD işlemleri
  - `op: 'create'` - Yeni kullanıcı ekle
  - `op: 'update'` - Kullanıcı güncelle
  - `op: 'delete'` - Kullanıcı sil
  - `op: 'list'` - Kullanıcıları listele

#### 📁 Kategori Yönetimi

- **POST** `/api/kategoriler.php` - Kategori CRUD işlemleri
- **GET** `/api/kategoriler.php` - Kategorileri listele

#### 📊 İşlem Kayıtları

- **GET** `/api/islem-kayitlari.php` - İşlem kayıtlarını listele
- **POST** `/api/islem-kayit-ekle.php` - İşlem kaydı ekle

#### 🔗 Deezer Entegrasyonu

- **GET** `/api/deezer-search.php` - Deezer'da şarkı ara
- **POST** `/api/deezer-download.php` - Şarkı indir

### API Yanıt Formatı

```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_records": 100,
    "items_per_page": 20
  },
  "message": "İşlem başarılı"
}
```

## 🗄️ Veritabanı Yapısı

### Ana Tablolar

#### `sarkilar` Tablosu

```sql
CREATE TABLE sarkilar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(100) NOT NULL,
    cevap VARCHAR(255) NOT NULL,
    sarki TEXT NOT NULL,
    dosya TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `kullanicilar` Tablosu

```sql
CREATE TABLE kullanicilar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(100) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    yetki ENUM('admin', 'moderator', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `islem_kayitlari` Tablosu

```sql
CREATE TABLE islem_kayitlari (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    islem_tipi ENUM('sarki_ekleme', 'sarki_silme', 'sarki_degistirme',
                    'kategori_ekleme', 'kategori_silme', 'kategori_degistirme',
                    'yetkili_ekleme', 'yetkili_silme', 'yetkili_guncelleme',
                    'sifre_sifirlama', 'rol_degistirme') NOT NULL,
    kaynak ENUM('deezer', 'mp3', 'manuel', 'admin_panel') NOT NULL,
    kullanici_id INT(11) NOT NULL,
    kullanici_adi VARCHAR(100) NOT NULL,
    tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detay TEXT NOT NULL,
    hedef_kullanici_id INT(11) DEFAULT NULL,
    hedef_kullanici_adi VARCHAR(100) DEFAULT NULL,
    -- Diğer alanlar...
);
```

## 🔧 Geliştirme

### Proje Yapısı

```
songle-backend/
├── api/                    # API endpoint'leri
│   ├── songs.php          # Şarkı yönetimi
│   ├── kullanicilar.php   # Kullanıcı yönetimi
│   ├── kategoriler.php    # Kategori yönetimi
│   ├── islem-kayitlari.php # İşlem kayıtları
│   └── deezer-*.php       # Deezer entegrasyonu
├── config/                 # Yapılandırma dosyaları
│   └── database.php       # Veritabanı bağlantısı
├── songle.sql             # Veritabanı şeması
└── README.md              # Bu dosya
```

### Yeni Endpoint Ekleme

1. `api/` klasöründe yeni PHP dosyası oluşturun
2. Gerekli import'ları ekleyin
3. Veritabanı bağlantısını kurun
4. API yanıt formatını takip edin
5. İşlem kaydı ekleyin (gerekirse)

### Hata Yönetimi

```php
try {
    // API işlemleri
    $response = ['success' => true, 'data' => $result];
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
```

## 🔐 Güvenlik

### Oturum Yönetimi

- PHP session kullanımı
- Güvenli çıkış işlemi
- Oturum zaman aşımı kontrolü

### Veri Doğrulama

- SQL injection koruması (prepared statements)
- XSS koruması (HTML escape)
- Input validation ve sanitization

### Yetkilendirme

- Rol tabanlı erişim kontrolü
- Admin-only endpoint'ler
- Kullanıcı işlem logları

## 📊 İzleme ve Loglama

### İşlem Kayıtları

- Tüm admin işlemleri loglanır
- Kullanıcı aktivite takibi
- Hata ve uyarı logları

### Performans İzleme

- API yanıt süreleri
- Veritabanı sorgu performansı
- Dosya yükleme istatistikleri

## 🧪 Test Etme

### Manuel Test

1. PhpMyAdmin'de veritabanını kontrol edin
2. API endpoint'lerini tarayıcıda test edin
3. Admin panel üzerinden işlemleri test edin

### Otomatik Test

```bash
# Örnek veri ekleme
http://localhost/songle-backend/add_example_data.php
```

## 🚨 Sorun Giderme

### Yaygın Sorunlar

1. **Veritabanı bağlantı hatası**

   - `config/database.php` ayarlarını kontrol edin
   - MySQL servisinin çalıştığından emin olun

2. **API yanıt vermiyor**

   - PHP error log'larını kontrol edin
   - `.htaccess` dosyasını kontrol edin

3. **Dosya yükleme hatası**
   - Klasör izinlerini kontrol edin
   - `php.ini` dosyasındaki upload limitlerini kontrol edin

### Log Kontrolü

- PHP error log: `xampp/php/logs/php_error_log`
- Apache error log: `xampp/apache/logs/error.log`
- Browser console'da JavaScript hataları

## 📈 Performans Optimizasyonu

### Veritabanı

- İndeksler eklendi
- Prepared statements kullanımı
- Sorgu optimizasyonu

### API

- Response caching
- Pagination desteği
- Gzip compression

## 🔄 Güncellemeler

### v2.0.0 - Major Update

- Modüler JavaScript mimarisi
- Kapsamlı işlem kayıtları
- Gelişmiş kullanıcı yönetimi
- Deezer entegrasyonu

### v1.5.0 - Operation Logs

- İşlem kayıtları sistemi
- Kullanıcı aktivite takibi
- Audit trail

### v1.0.0 - Initial Release

- Temel CRUD işlemleri
- Basit admin panel
- Şarkı yönetimi

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add amazing feature'`)
4. Branch'e push yapın (`git push origin feature/amazing-feature`)
5. Pull Request açın

---

**Songle Backend API ile güçlü müzik yönetimi! 🎵✨**
