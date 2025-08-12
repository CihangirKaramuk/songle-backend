# Ayarlar (Settings) API Documentation ⚙️

Bu API, kullanıcı ayarlarını, sistem ayarlarını ve kullanıcı yönetimini yönetmek için kullanılır. Ayarlar veritabanında güvenli bir şekilde saklanır ve kullanıcı farklı cihazlardan giriş yaptığında korunur.

## ✨ Özellikler

- **Kullanıcı Ayarları**: Tema, sayfa boyutu, bildirim tercihleri
- **Sistem Ayarları**: Uygulama geneli ayarlar
- **Kullanıcı Yönetimi**: Yetkili kullanıcı CRUD işlemleri
- **İşlem Kayıtları**: Tüm admin işlemlerinin detaylı loglanması
- **Rol Tabanlı Erişim**: Admin, moderator ve user rolleri
- **Güvenlik**: Şifre hashleme ve oturum yönetimi

## 🗄️ Veritabanı Tabloları

### ayarlar Tablosu

```sql
CREATE TABLE ayarlar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kullanici_id INT,
    tema VARCHAR(20) DEFAULT 'dark',
    sayfa_boyutu INT DEFAULT 20,
    bildirim_sesi BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE
);
```

### kullanicilar Tablosu

```sql
CREATE TABLE kullanicilar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kullanici_adi VARCHAR(100) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    yetki ENUM('admin', 'moderator', 'user') DEFAULT 'user',
    email VARCHAR(255),
    son_giris TIMESTAMP NULL,
    aktif BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

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
    hedef_kullanici_id INT NULL,
    hedef_kullanici_adi VARCHAR(100) NULL,
    INDEX idx_islem_tipi (islem_tipi),
    INDEX idx_kaynak (kaynak),
    INDEX idx_tarih (tarih),
    INDEX idx_kullanici (kullanici_id)
);
```

## 🔌 API Endpoints

### 1. Ayarları Getir

**GET** `/api/ayarlar.php`

#### Parametreler:

- `kullanici_id` (required): Kullanıcı ID'si

#### Örnek İstek:

```
GET /api/ayarlar.php?kullanici_id=1
```

#### Başarılı Yanıt (200):

```json
{
  "success": true,
  "message": "Ayarlar başarıyla getirildi",
  "data": {
    "tema": "dark",
    "sayfa_boyutu": 20,
    "bildirim_sesi": true
  }
}
```

#### Hata Yanıtları:

- **400**: Kullanıcı ID'si eksik
- **404**: Kullanıcı bulunamadı

### 2. Ayarları Kaydet

**POST** `/api/ayarlar.php`

#### Request Body:

```json
{
  "kullanici_id": 1,
  "tema": "light",
  "sayfa_boyutu": 50,
  "bildirim_sesi": false
}
```

#### Parametreler:

- `kullanici_id` (required): Kullanıcı ID'si
- `tema` (optional): "dark" veya "light" (varsayılan: "dark")
- `sayfa_boyutu` (optional): 1-100 arası sayı (varsayılan: 20)
- `bildirim_sesi` (optional): true/false (varsayılan: true)

#### Başarılı Yanıt (200):

```json
{
  "success": true,
  "message": "Ayarlar başarıyla kaydedildi"
}
```

#### Hata Yanıtları:

- **400**: Geçersiz JSON, eksik kullanıcı ID, geçersiz tema değeri, geçersiz sayfa boyutu
- **404**: Kullanıcı bulunamadı

### 3. Kullanıcı Yönetimi

**POST** `/api/kullanicilar.php`

#### Request Body:

```json
{
  "op": "create",
  "kullanici_adi": "yeni_kullanici",
  "sifre": "güvenli_şifre123",
  "yetki": "moderator",
  "email": "kullanici@example.com"
}
```

#### Operasyon Tipleri:

- **`op: 'create'`**: Yeni kullanıcı ekle
- **`op: 'update'`**: Kullanıcı bilgilerini güncelle
- **`op: 'delete'`**: Kullanıcı sil
- **`op: 'list'`**: Tüm kullanıcıları listele
- **`op: 'change_password'`**: Şifre değiştir
- **`op: 'change_role'`**: Rol değiştir

#### Örnek Yanıtlar:

**Kullanıcı Ekleme:**

```json
{
  "success": true,
  "message": "Kullanıcı başarıyla eklendi",
  "user_id": 123
}
```

**Kullanıcı Listesi:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "kullanici_adi": "admin",
      "yetki": "admin",
      "email": "admin@example.com",
      "son_giris": "2024-01-15 10:30:00",
      "aktif": true,
      "created_at": "2024-01-01 00:00:00"
    }
  ]
}
```

### 4. İşlem Kayıtları

**GET** `/api/islem-kayitlari.php`

#### Parametreler:

- `islem_tipi` (optional): Filtreleme için işlem tipi
- `kaynak` (optional): Filtreleme için kaynak
- `sayfa` (optional): Sayfa numarası (varsayılan: 1)
- `limit` (optional): Sayfa başına kayıt sayısı (varsayılan: 10)

#### Örnek İstek:

```
GET /api/islem-kayitlari.php?islem_tipi=yetkili_ekleme&sayfa=1&limit=20
```

#### Yanıt:

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "islem_tipi": "yetkili_ekleme",
      "kaynak": "admin_panel",
      "kullanici_id": 1,
      "kullanici_adi": "admin",
      "tarih": "2024-01-15 10:30:00",
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

## 🔐 Güvenlik Özellikleri

### Şifre Yönetimi

- **Hashleme**: Şifreler `password_hash()` ile hashlenir
- **Doğrulama**: `password_verify()` ile şifre doğrulanır
- **Güçlü Şifre**: Minimum 8 karakter, büyük/küçük harf, sayı, özel karakter

### Oturum Yönetimi

- **Session**: PHP session kullanımı
- **Timeout**: Oturum zaman aşımı kontrolü
- **Güvenli Çıkış**: Session verilerini temizleme

### Yetkilendirme

- **Rol Tabanlı**: Admin, moderator, user rolleri
- **İşlem Kontrolü**: Her işlem için yetki kontrolü
- **Audit Trail**: Tüm işlemler loglanır

## 📱 Frontend Entegrasyonu

### Kullanıcı Yönetimi

```javascript
// Yeni kullanıcı ekle
async function addUser(userData) {
  try {
    const response = await fetch('/api/kullanicilar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        op: 'create',
        kullanici_adi: userData.username,
        sifre: userData.password,
        yetki: userData.role,
        email: userData.email,
      }),
    })

    const result = await response.json()
    if (result.success) {
      showToast('Kullanıcı başarıyla eklendi', 'success')
      loadUsers() // Kullanıcı listesini yenile
    } else {
      showToast(result.error, 'error')
    }
  } catch (error) {
    showToast('Bir hata oluştu', 'error')
  }
}

// Kullanıcı rolünü değiştir
async function changeUserRole(userId, newRole) {
  try {
    const response = await fetch('/api/kullanicilar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        op: 'update',
        kullanici_id: userId,
        yetki: newRole,
      }),
    })

    const result = await response.json()
    if (result.success) {
      showToast('Rol başarıyla değiştirildi', 'success')
      loadUsers()
    } else {
      showToast(result.error, 'error')
    }
  } catch (error) {
    showToast('Bir hata oluştu', 'error')
  }
}
```

### İşlem Kayıtları

```javascript
// İşlem kayıtlarını yükle
async function loadOperationLogs(filters = {}) {
  try {
    const params = new URLSearchParams()

    if (filters.islem_tipi) params.append('islem_tipi', filters.islem_tipi)
    if (filters.sayfa) params.append('sayfa', filters.sayfa)
    if (filters.limit) params.append('limit', filters.limit)

    const response = await fetch(
      `/api/islem-kayitlari.php?${params.toString()}`
    )
    const result = await response.json()

    if (result.success) {
      renderOperationLogs(result.data)
      updatePagination(result.pagination)
    } else {
      showToast(result.error, 'error')
    }
  } catch (error) {
    showToast('İşlem kayıtları yüklenirken hata oluştu', 'error')
  }
}

// Filtreleme
function applyFilters() {
  const islemTipi = document.getElementById('islemTipiFiltre').value
  const filters = {}

  if (islemTipi === 'sarki_islemleri') {
    filters.islem_tipi = 'sarki_ekleme,sarki_silme,sarki_degistirme'
  } else if (islemTipi === 'kategori_islemleri') {
    filters.islem_tipi = 'kategori_ekleme,kategori_silme,kategori_degistirme'
  } else if (islemTipi === 'yetkili_islemleri') {
    filters.islem_tipi =
      'yetkili_ekleme,yetkili_silme,yetkili_guncelleme,sifre_sifirlama,rol_degistirme'
  }

  loadOperationLogs(filters)
}
```

## 🎯 Kullanım Senaryoları

### 1. Yeni Yetkili Ekleme

1. Admin panelinde "Ayarlar" sekmesine git
2. "Kullanıcı Yönetimi" bölümünde "Yeni Kullanıcı" butonuna tıkla
3. Kullanıcı bilgilerini gir (kullanıcı adı, şifre, rol)
4. "Ekle" butonuna tıkla
5. İşlem kayıtlarında "yetkili_ekleme" kaydı görünür

### 2. Kullanıcı Rolü Değiştirme

1. Kullanıcı listesinde ilgili kullanıcının "Düzenle" butonuna tıkla
2. Rol dropdown'ından yeni rolü seç
3. "Güncelle" butonuna tıkla
4. İşlem kayıtlarında "rol_degistirme" kaydı görünür

### 3. Şifre Sıfırlama

1. Kullanıcı listesinde ilgili kullanıcının "Şifre Sıfırla" butonuna tıkla
2. Yeni şifreyi gir ve onayla
3. İşlem kayıtlarında "sifre_sifirlama" kaydı görünür

## 🚨 Hata Yönetimi

### API Hata Kodları

- **`INVALID_OPERATION`**: Geçersiz operasyon
- **`USER_NOT_FOUND`**: Kullanıcı bulunamadı
- **`DUPLICATE_USERNAME`**: Kullanıcı adı zaten mevcut
- **`INVALID_ROLE`**: Geçersiz rol
- **`INSUFFICIENT_PERMISSIONS`**: Yetersiz yetki
- **`INVALID_PASSWORD`**: Geçersiz şifre formatı

### Hata Yanıt Formatı

```json
{
  "success": false,
  "error": "Hata mesajı",
  "error_code": "ERROR_CODE",
  "details": "Detaylı hata açıklaması"
}
```

## 📊 Performans Optimizasyonu

### Veritabanı

- **İndeksler**: Tüm arama alanlarında index'ler
- **Prepared Statements**: SQL injection koruması
- **Query Optimization**: Büyük veri setleri için optimize edilmiş sorgular

### API

- **Pagination**: Server-side pagination desteği
- **Caching**: Sık kullanılan veriler için cache
- **Response Compression**: Gzip compression

## 🔄 Güncellemeler

### v2.0.0 - Major Update

- Kapsamlı kullanıcı yönetimi sistemi
- İşlem kayıtları entegrasyonu
- Rol tabanlı erişim kontrolü
- Gelişmiş güvenlik özellikleri

### v1.5.0 - Enhanced Settings

- Tema yönetimi
- Sayfa boyutu ayarları
- Bildirim tercihleri

### v1.0.0 - Initial Release

- Temel ayar yönetimi
- Basit kullanıcı sistemi

## 📝 Not

Bu API, admin panelindeki tüm kullanıcı yönetimi işlemlerini destekler. Tüm işlemler otomatik olarak loglanır ve güvenlik için gerekli kontroller yapılır. API kullanımı için admin yetkisi gereklidir.

---

**Güvenli ve kapsamlı kullanıcı yönetimi! 🔐✨**
