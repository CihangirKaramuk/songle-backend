# Songle Backend API

Songle, şarkı tahmin oyunu için basit bir PHP backend API'sidir. Bu API, şarkı bilgilerini yönetmek için CRUD işlemleri sağlar.

## Özellikler

- Şarkı listeleme
- Kategoriye göre şarkı filtreleme
- Yeni şarkı ekleme
- Mevcut şarkıyı güncelleme
- Şarkı silme

## Kurulum

1. XAMPP veya benzeri bir web sunucusu kurulu olduğundan emin olun.
2. Bu projeyi `htdocs` klasörüne kopyalayın:
   ```
   cd c:\xampp\htdocs
   git clone [repo-url] songle-backend
   ```
3. Tarayıcıda şu adresi açın:
   ```
   http://localhost/songle-backend/
   ```
4. Veritabanı otomatik olarak oluşturulacak ve örnek veri eklenecektir.

## API Dokümantasyonu

Tüm API endpoint'leri ve kullanım örnekleri için tarayıcınızda şu adresi ziyaret edin:

```
http://localhost/songle-backend/
```

### Temel Endpoint'ler

- `GET /api/songs.php` - Tüm şarkıları listeler
- `GET /api/songs.php?kategori=turkce-rock` - Belirli bir kategorideki şarkıları listeler
- `POST /api/songs.php` - Yeni şarkı ekler
- `PUT /api/songs.php` - Mevcut bir şarkıyı günceller
- `DELETE /api/songs.php?id=1` - ID'si verilen şarkıyı siler

## Veritabanı Yapısı

```sql
CREATE TABLE IF NOT EXISTS sarkilar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(100) NOT NULL,
    cevap VARCHAR(255) NOT NULL,
    sarki TEXT NOT NULL,
    dosya TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Geliştirme

### Örnek Veri Ekleme

Örnek veri eklemek için tarayıcıda şu adresi ziyaret edin:

```
http://localhost/songle-backend/add_example_data.php
```

Veya komut satırından:

```
php add_example_data.php
```

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.
