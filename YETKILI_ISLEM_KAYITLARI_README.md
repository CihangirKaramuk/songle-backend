# Yetkili İşlem Kayıtları Sistemi

Bu dokümantasyon, admin panelindeki yetkili ekleme/silme/güncelleme işlemlerinin işlem kayıtları (log) sistemine entegrasyonunu açıklar.

## Eklenen Özellikler

### 1. Modüler JavaScript Mimarisi (v2.2.0)

- **ES6 Modül Sistemi**: Tamamen yeniden yapılandırılmış admin panel
- **Merkezi Durum Yönetimi**: Global değişkenlerin merkezi yönetimi
- **Modüler Yapı**: Her modül kendi sorumluluğuna odaklanır
- **Performans Optimizasyonu**: Lazy loading ve bundle optimization
- **Gelişmiş Hata Yönetimi**: Modül bazlı hata yakalama ve raporlama

### 2. Veritabanı Güncellemeleri

#### `islem_kayitlari` Tablosu

- **Yeni İşlem Tipleri:**

  - `yetkili_ekleme` - Yeni yetkili kullanıcı ekleme
  - `yetkili_silme` - Yetkili kullanıcı silme
  - `yetkili_guncelleme` - Yetkili kullanıcı bilgilerini güncelleme
  - `sifre_sifirlama` - Kullanıcı şifresi sıfırlama
  - `rol_degistirme` - Kullanıcı rolü değiştirme

- **Yeni Kaynak:**

  - `admin_panel` - Admin panel üzerinden yapılan işlemler

- **Yeni Alanlar:**
  - `hedef_kullanici_id` - İşlem yapılan kullanıcının ID'si
  - `hedef_kullanici_adi` - İşlem yapılan kullanıcının adı

### 2. Gelişmiş Özellikler

#### Modal Sistemi

- **Şifre Sıfırlama Modalı**: Güvenli şifre sıfırlama işlemi
- **Rol Değiştirme Modalı**: Kullanıcı rolü değiştirme işlemi
- **Kullanıcı Silme Onayı**: Güvenli kullanıcı silme işlemi

#### Batch Operations

- **Toplu Seçim**: Çoklu kullanıcı seçimi ve işlemi
- **Toplu Silme**: Seçilen kullanıcıları toplu silme
- **Gelişmiş UI**: Modern kullanıcı arayüzü ve etkileşim

#### Güvenlik Özellikleri

- **Password Hashleme**: Güvenli şifre hashleme sistemi
- **Session Yönetimi**: Güvenli oturum kontrolü
- **Yetki Kontrolü**: Rol tabanlı erişim kontrolü

### 2. Backend Güncellemeleri

#### `api/kullanicilar.php`

- **Kullanıcı Ekleme:** `yetkili_ekleme` işlem kaydı eklenir
- **Kullanıcı Güncelleme:** `yetkili_guncelleme` işlem kaydı eklenir
- **Kullanıcı Silme:** `yetkili_silme` işlem kaydı eklenir
- **Şifre Sıfırlama:** `sifre_sifirlama` işlem kaydı eklenir
- **Rol Değiştirme:** `rol_degistirme` işlem kaydı eklenir

#### `api/islem-kayit-ekle.php`

- Yeni alanlar (`hedef_kullanici_id`, `hedef_kullanici_adi`) desteklenir

### 3. Frontend Güncellemeleri

#### `admin/modules/settings.js`

- Yeni işlem tipleri için Türkçe metinler eklendi
- İşlem kayıtları render fonksiyonu güncellendi
- Hedef kullanıcı bilgileri gösterimi eklendi

#### `admin/api-panel.html`

- İşlem tipi filtresine yeni seçenekler eklendi

#### `admin/panel-style.css`

- Hedef kullanıcı alanı için CSS stilleri eklendi
- Light/dark tema desteği

## Kurulum

### 1. Veritabanı Güncellemesi

```sql
-- update_islem_kayitlari.sql dosyasını çalıştırın
source update_islem_kayitlari.sql;
```

### 2. Dosya Güncellemeleri

Tüm güncellenmiş dosyaların kopyalandığından emin olun:

- `api/kullanicilar.php`
- `api/islem-kayit-ekle.php`
- `config/database.php`
- `admin/modules/settings.js`
- `admin/modules/global-variables.js`
- `admin/modules/utils.js`
- `admin/api-panel.html`
- `admin/panel-style.css`

### 3. Modüler Yapı Güncellemesi

Yeni modüler JavaScript mimarisi için:

- `admin/modules/` klasöründeki tüm modül dosyalarını güncelleyin
- ES6 modül sistemi ile uyumlu hale getirin
- Global değişken yönetimini merkezi hale getirin

## Kullanım

### İşlem Kayıtları Görüntüleme

1. Admin panelinde "İşlem Kayıtları" sekmesine gidin
2. İşlem tipi filtresinden yetkili işlemlerini seçebilirsiniz
3. Her işlem kaydında:
   - İşlemi yapan kullanıcı
   - Hedef kullanıcı (varsa)
   - İşlem detayı
   - Tarih ve saat

### Filtreleme

- **Tüm İşlemler:** Tüm işlem kayıtlarını gösterir
- **Yetkili Ekleme:** Sadece yeni yetkili ekleme işlemlerini gösterir
- **Yetkili Silme:** Sadece yetkili silme işlemlerini gösterir
- **Yetkili Güncelleme:** Sadece yetkili güncelleme işlemlerini gösterir
- **Şifre Sıfırlama:** Sadece şifre sıfırlama işlemlerini gösterir
- **Rol Değiştirme:** Sadece rol değiştirme işlemlerini gösterir

## Örnek İşlem Kayıtları

### Yetkili Ekleme

```
İşlem Tipi: Yetkili Ekleme
Kaynak: admin_panel
İşlemi Yapan: admin
Hedef Kullanıcı: yeni_kullanici
Detay: 'yeni_kullanici' kullanıcısı yetkili olarak eklendi
```

### Şifre Sıfırlama

```
İşlem Tipi: Şifre Sıfırlama
Kaynak: admin_panel
İşlemi Yapan: admin
Hedef Kullanıcı: mevcut_kullanici
Detay: 'mevcut_kullanici' kullanıcısının şifresi sıfırlandı
```

### Rol Değiştirme

```
İşlem Tipi: Rol Değiştirme
Kaynak: admin_panel
İşlemi Yapan: admin
Hedef Kullanıcı: kullanici_adi
Detay: 'kullanici_adi' kullanıcısının rolü 'Admin' olarak değiştirildi
```

## Teknik Detaylar

### Veritabanı Şeması

```sql
CREATE TABLE `islem_kayitlari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `islem_tipi` enum('sarki_ekleme','sarki_silme','sarki_degistirme','kategori_ekleme','kategori_silme','kategori_degistirme','yetkili_ekleme','yetkili_silme','yetkili_guncelleme','sifre_sifirlama','rol_degistirme') NOT NULL,
  `kaynak` enum('deezer','mp3','manuel','admin_panel') NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `kullanici_adi` varchar(100) NOT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `detay` text NOT NULL,
  `sarki_adi` varchar(255) DEFAULT NULL,
  `sanatci` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `kategori_adi` varchar(255) DEFAULT NULL,
  `eski_deger` text DEFAULT NULL,
  `yeni_deger` text DEFAULT NULL,
  `hedef_kullanici_id` int(11) DEFAULT NULL,
  `hedef_kullanici_adi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_islem_tipi` (`islem_tipi`),
  KEY `idx_kaynak` (`kaynak`),
  KEY `idx_tarih` (`tarih`),
  KEY `idx_kullanici` (`kullanici_id`)
);
```

### API Endpoint'leri

- **POST** `/api/kullanicilar.php` - Kullanıcı CRUD işlemleri
- **GET** `/api/islem-kayitlari.php` - İşlem kayıtlarını listele
- **POST** `/api/islem-kayit-ekle.php` - İşlem kaydı ekle

## Test Etme

1. **Yetkili Ekleme:** Yeni bir yetkili kullanıcı ekleyin
2. **Şifre Sıfırlama:** Mevcut bir kullanıcının şifresini sıfırlayın
3. **Rol Değiştirme:** Bir kullanıcının rolünü değiştirin
4. **İşlem Kayıtları:** İşlem kayıtları sekmesinde bu işlemlerin görünüp görünmediğini kontrol edin

## Sorun Giderme

### Yaygın Sorunlar

1. **Veritabanı hatası:** `update_islem_kayitlari.sql` scriptini çalıştırdığınızdan emin olun
2. **İşlem kayıtları görünmüyor:** Backend dosyalarının güncellendiğinden emin olun
3. **CSS stilleri çalışmıyor:** `panel-style.css` dosyasının güncellendiğinden emin olun

### Log Kontrolü

- PHP error log'larını kontrol edin
- Browser console'da JavaScript hatalarını kontrol edin
- Network sekmesinde API çağrılarını kontrol edin
