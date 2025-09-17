# İşlem Kayıtları Sistemi 📊

Bu sistem, Songle uygulamasında yapılan tüm işlemleri kayıt altına alır ve takip eder. Admin panelindeki tüm aktiviteler detaylı olarak loglanır.

## ✨ Özellikler

- **Kapsamlı Loglama**: Tüm admin işlemleri kayıt altına alınır
- **Filtreleme**: İşlem tipi, kaynak ve tarih bazında filtreleme
- **Sayfalama**: Büyük veri setleri için sayfalama desteği
- **Arama**: Detaylı arama ve filtreleme seçenekleri
- **Audit Trail**: Tam işlem geçmişi ve kullanıcı aktivite takibi
- **Grup Filtreleme**: Ana kategorilere göre işlem gruplandırma

## 🗄️ Veritabanı Tablosu

### islem_kayitlari Tablosu

```sql
CREATE TABLE islem_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    islem_tipi ENUM('sarki_ekleme', 'sarki_silme', 'sarki_degistirme',
                    'kategori_ekleme', 'kategori_silme', 'kategori_degistirme',
                    'yetkili_ekleme', 'yetkili_silme', 'yetkili_guncelleme',
                    'sifre_sifirlama', 'rol_degistirme') NOT NULL,
    kaynak ENUM('deezer', 'mp3', 'manuel', 'admin_panel') NOT NULL,
    kullanici_id INT NOT NULL,
    kullanici_adi VARCHAR(100) NOT NULL,
    tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detay TEXT NOT NULL,
    sarki_adi VARCHAR(255) NULL,
    sanatci VARCHAR(255) NULL,
    kategori VARCHAR(255) NULL,
    kategori_adi VARCHAR(255) NULL,
    eski_deger TEXT NULL,
    yeni_deger TEXT NULL,
    hedef_kullanici_id INT NULL,
    hedef_kullanici_adi VARCHAR(100) NULL,
    INDEX idx_islem_tipi (islem_tipi),
    INDEX idx_kaynak (kaynak),
    INDEX idx_tarih (tarih),
    INDEX idx_kullanici (kullanici_id),
    INDEX idx_hedef_kullanici (hedef_kullanici_id)
);
```

## 🔌 API Endpoints

### 1. İşlem Kayıtlarını Listeleme

**GET** `/api/islem-kayitlari.php`

**Parametreler:**

- `islem_tipi` (opsiyonel): Filtreleme için işlem tipi (virgülle ayrılmış değerler desteklenir)
- `kaynak` (opsiyonel): Filtreleme için kaynak
- `sayfa` (opsiyonel): Sayfa numarası (varsayılan: 1)
- `limit` (opsiyonel): Sayfa başına kayıt sayısı (varsayılan: 10)

**Örnekler:**

```
# Tek işlem tipi
GET /api/islem-kayitlari.php?islem_tipi=sarki_ekleme&sayfa=1&limit=20

# Çoklu işlem tipi (yeni özellik)
GET /api/islem-kayitlari.php?islem_tipi=sarki_ekleme,sarki_silme,sarki_degistirme&sayfa=1&limit=20

# Yetkili işlemleri
GET /api/islem-kayitlari.php?islem_tipi=yetkili_ekleme,yetkili_silme,yetkili_guncelleme,sifre_sifirlama,rol_degistirme
```

**Yanıt:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "islem_tipi": "sarki_ekleme",
      "kaynak": "manuel",
      "kullanici_id": 1,
      "kullanici_adi": "Admin",
      "tarih": "2024-01-15 10:30:00",
      "detay": "Bohemian Rhapsody - Queen şarkısı eklendi",
      "sarki_adi": "Bohemian Rhapsody",
      "sanatci": "Queen",
      "kategori": "Rock",
      "hedef_kullanici_id": null,
      "hedef_kullanici_adi": null
    },
    {
      "id": 2,
      "islem_tipi": "yetkili_ekleme",
      "kaynak": "admin_panel",
      "kullanici_id": 1,
      "kullanici_adi": "Admin",
      "tarih": "2024-01-15 11:00:00",
      "detay": "yeni_kullanici kullanıcısı yetkili olarak eklendi",
      "hedef_kullanici_id": 2,
      "hedef_kullanici_adi": "yeni_kullanici"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_records": 100,
    "limit": 20
  }
}
```

### 2. Yeni İşlem Kaydı Ekleme

**POST** `/api/islem-kayit-ekle.php`

**Body:**

```json
{
  "islem_tipi": "sarki_ekleme",
  "kaynak": "manuel",
  "kullanici_id": 1,
  "kullanici_adi": "Admin",
  "detay": "Bohemian Rhapsody - Queen şarkısı eklendi",
  "sarki_adi": "Bohemian Rhapsody",
  "sanatci": "Queen",
  "kategori": "Rock",
  "hedef_kullanici_id": null,
  "hedef_kullanici_adi": null
}
```

**Yanıt:**

```json
{
  "success": true,
  "message": "İşlem kaydı başarıyla eklendi",
  "id": 123
}
```

### 3. İşlem Kayıtlarını Silme

**POST** `/api/islem-kayitlari-sil.php`

**Body:**

```json
{
  "kayit_ids": [1, 2, 3]
}
```

**Yanıt:**

```json
{
  "success": true,
  "message": "3 kayıt başarıyla silindi",
  "deleted_count": 3
}
```

## 🎯 İşlem Tipleri

### Şarkı İşlemleri

- **`sarki_ekleme`**: Yeni şarkı ekleme
- **`sarki_silme`**: Şarkı silme
- **`sarki_degistirme`**: Şarkı bilgilerini güncelleme

### Kategori İşlemleri

- **`kategori_ekleme`**: Yeni kategori ekleme
- **`kategori_silme`**: Kategori silme
- **`kategori_degistirme`**: Kategori bilgilerini güncelleme

### Yetkili İşlemleri

- **`yetkili_ekleme`**: Yeni yetkili kullanıcı ekleme
- **`yetkili_silme`**: Yetkili kullanıcı silme
- **`yetkili_guncelleme`**: Yetkili kullanıcı bilgilerini güncelleme
- **`sifre_sifirlama`**: Kullanıcı şifresi sıfırlama
- **`rol_degistirme`**: Kullanıcı rolü değiştirme

## 🔍 Filtreleme Sistemi

### Ana Kategori Filtreleri

Frontend'de 3 ana kategori filtresi bulunur:

1. **Şarkı İşlemleri**: `sarki_ekleme,sarki_silme,sarki_degistirme`
2. **Kategori İşlemleri**: `kategori_ekleme,kategori_silme,kategori_degistirme`
3. **Yetkili İşlemleri**: `yetkili_ekleme,yetkili_silme,yetkili_guncelleme,sifre_sifirlama,rol_degistirme`

### Gelişmiş Filtreleme

```php
// Virgülle ayrılmış işlem tiplerini destekler
if (strpos($islem_tipi, ',') !== false) {
    $islem_tipleri = explode(',', $islem_tipi);
    $placeholders = str_repeat('?,', count($islem_tipleri) - 1) . '?';
    $where_conditions[] = "islem_tipi IN ($placeholders)";
}
```

## 📱 Frontend Entegrasyonu

### Filtre Seçenekleri

```html
<select id="islemTipiFiltre" class="filtre-select">
  <option value="">Tüm İşlemler</option>
  <option value="sarki_islemleri">Şarkı İşlemleri</option>
  <option value="kategori_islemleri">Kategori İşlemleri</option>
  <option value="yetkili_islemleri">Yetkili İşlemleri</option>
</select>
```

### JavaScript Filtreleme

```javascript
// Ana kategori filtrelerini işlem tipi filtrelerine çevir
let islemTipiParam = ''
if (islemTipiFiltre === 'sarki_islemleri') {
  islemTipiParam = 'sarki_ekleme,sarki_silme,sarki_degistirme'
} else if (islemTipiFiltre === 'kategori_islemleri') {
  islemTipiParam = 'kategori_ekleme,kategori_silme,kategori_degistirme'
} else if (islemTipiFiltre === 'yetkili_islemleri') {
  islemTipiParam =
    'yetkili_ekleme,yetkili_silme,yetkili_guncelleme,sifre_sifirlama,rol_degistirme'
}
```

## 🔧 Teknik Detaylar

### Sayfalama

- **Server-side pagination** desteklenir
- **Items per page**: Varsayılan 10, maksimum 100
- **Page navigation**: Önceki/sonraki butonları
- **Page indicators**: Nokta animasyonları

### Performans

- **Database indexes** tüm filtreleme alanlarında
- **Prepared statements** SQL injection koruması
- **Query optimization** büyük veri setleri için

### Güvenlik

- **Input validation** tüm parametreler için
- **SQL injection protection** prepared statements ile
- **XSS protection** HTML escape ile

## 📊 Örnek Kullanım Senaryoları

### 1. Şarkı Yönetimi

```javascript
// Şarkı ekleme sonrası log
await fetch('/api/islem-kayit-ekle.php', {
  method: 'POST',
  body: JSON.stringify({
    islem_tipi: 'sarki_ekleme',
    kaynak: 'deezer',
    kullanici_id: currentUser.id,
    kullanici_adi: currentUser.username,
    detay: `${songName} - ${artist} şarkısı Deezer'dan eklendi`,
    sarki_adi: songName,
    sanatci: artist,
    kategori: category,
  }),
})
```

### 2. Kullanıcı Yönetimi

```javascript
// Kullanıcı ekleme sonrası log
await fetch('/api/islem-kayit-ekle.php', {
  method: 'POST',
  body: JSON.stringify({
    islem_tipi: 'yetkili_ekleme',
    kaynak: 'admin_panel',
    kullanici_id: currentUser.id,
    kullanici_adi: currentUser.username,
    detay: `${newUsername} kullanıcısı yetkili olarak eklendi`,
    hedef_kullanici_id: newUserId,
    hedef_kullanici_adi: newUsername,
  }),
})
```

## 🚨 Hata Yönetimi

### API Hataları

```json
{
  "success": false,
  "error": "Hata mesajı",
  "error_code": "ERROR_CODE"
}
```

### Yaygın Hata Kodları

- **`INVALID_PARAMETERS`**: Geçersiz parametreler
- **`DATABASE_ERROR`**: Veritabanı hatası
- **`PERMISSION_DENIED`**: Yetki hatası
- **`RECORD_NOT_FOUND`**: Kayıt bulunamadı

## 🔄 Güncellemeler

### v2.2.0 - Modüler Admin Panel Entegrasyonu

- **Modüler JavaScript Mimarisi**: ES6 modülleri ile tamamen yeniden yapılandırılmış admin panel
- **Gelişmiş Batch Operations**: Toplu işlem kaydı silme ve gelişmiş seçim sistemi
- **Real-time Statistics**: Anlık sistem istatistikleri ve performans metrikleri
- **Tema Desteği**: Dark/Light mode ile kullanıcı tercihi yönetimi
- **Gelişmiş Modal Sistemi**: Detaylı işlem kaydı görüntüleme ve yönetim modalleri
- **Sistem İzleme**: Kapsamlı audit trail ve kullanıcı aktivite takibi
- **API Optimizasyonu**: Gelişmiş performans ve hata yönetimi

### v2.1.0 - Enhanced Operation Logs

- **Batch Operations**: Toplu işlem kaydı silme ve seçim sistemi
- **Gelişmiş Filtreleme**: Çoklu işlem tipi filtreleme ve arama
- **Modal Sistemi**: Detaylı işlem kaydı görüntüleme modalleri
- **Real-time Updates**: Anlık işlem kaydı güncellemeleri
- **Gelişmiş UI**: Modern kullanıcı arayüzü ve etkileşim

### v2.0.0 - Major Update

- Yetkili işlem kayıtları eklendi
- Grup filtreleme sistemi
- Hedef kullanıcı bilgileri
- Gelişmiş sayfalama
- Audit trail sistemi

### v1.5.0 - Enhanced Logging

- Çoklu işlem tipi filtreleme
- Server-side pagination
- Performans optimizasyonları
- Kullanıcı aktivite takibi

### v1.0.0 - Initial Release

- Temel işlem kayıtları
- Şarkı ve kategori işlemleri
- Basit filtreleme
- Temel loglama sistemi

## 📝 Not

Bu sistem, admin panelindeki tüm işlemleri otomatik olarak loglar. Manuel log ekleme sadece özel durumlar için gereklidir. Sistem, güvenlik ve uyumluluk için tasarlanmıştır.

---

**Kapsamlı işlem takibi ile güvenli yönetim! 🔒✨**
