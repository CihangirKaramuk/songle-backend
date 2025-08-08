# İşlem Kayıtları Sistemi

Bu sistem, Songle uygulamasında yapılan tüm işlemleri kayıt altına alır ve takip eder.

## Veritabanı Tablosu

### islem_kayitlari Tablosu

```sql
CREATE TABLE islem_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    islem_tipi ENUM('sarki_ekleme', 'sarki_silme', 'sarki_degistirme', 'kategori_ekleme', 'kategori_silme', 'kategori_degistirme') NOT NULL,
    kaynak ENUM('deezer', 'mp3', 'manuel') NOT NULL,
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
    INDEX idx_islem_tipi (islem_tipi),
    INDEX idx_kaynak (kaynak),
    INDEX idx_tarih (tarih),
    INDEX idx_kullanici (kullanici_id)
);
```

## API Endpoints

### 1. İşlem Kayıtlarını Listeleme

**GET** `/api/islem-kayitlari.php`

**Parametreler:**

- `islem_tipi` (opsiyonel): Filtreleme için işlem tipi
- `kaynak` (opsiyonel): Filtreleme için kaynak
- `sayfa` (opsiyonel): Sayfa numarası (varsayılan: 1)
- `limit` (opsiyonel): Sayfa başına kayıt sayısı (varsayılan: 10)

**Örnek:**

```
GET /api/islem-kayitlari.php?islem_tipi=sarki_ekleme&sayfa=1&limit=20
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
      "kategori": "Rock"
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

### 2. İşlem Kayıtlarını Silme

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

### 3. Yeni İşlem Kaydı Ekleme

**POST** `/api/islem-kayit-ekle.php`

**Body:**

```json
{
  "islem_tipi": "sarki_ekleme",
  "kaynak": "deezer",
  "kullanici_id": 1,
  "kullanici_adi": "Admin",
  "detay": "Bohemian Rhapsody - Queen şarkısı eklendi",
  "sarki_adi": "Bohemian Rhapsody",
  "sanatci": "Queen",
  "kategori": "Rock"
}
```

**Yanıt:**

```json
{
  "success": true,
  "message": "İşlem kaydı başarıyla eklendi",
  "kayit_id": 123
}
```

## Otomatik Kayıt Entegrasyonu

### Şarkı İşlemleri

#### Şarkı Ekleme

Şarkı eklendiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `sarki_ekleme`
- `kaynak`: `deezer`, `mp3` veya `manuel`
- `detay`: Şarkı adı ve sanatçı bilgisi

#### Şarkı Silme

Şarkı silindiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `sarki_silme`
- `detay`: Silinen şarkının bilgileri

#### Şarkı Güncelleme

Şarkı güncellendiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `sarki_degistirme`
- `eski_deger`: Güncellenmeden önceki değerler
- `yeni_deger`: Güncellenmiş değerler

### Kategori İşlemleri

#### Kategori Ekleme

Kategori eklendiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `kategori_ekleme`
- `detay`: Kategori adı

#### Kategori Silme

Kategori silindiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `kategori_silme`
- `detay`: Silinen kategorinin bilgileri

#### Kategori Güncelleme

Kategori güncellendiğinde otomatik olarak kayıt oluşturulur:

- `islem_tipi`: `kategori_degistirme`
- `eski_deger`: Güncellenmeden önceki değerler
- `yeni_deger`: Güncellenmiş değerler

## İşlem Tipleri

- `sarki_ekleme`: Yeni şarkı ekleme
- `sarki_silme`: Şarkı silme
- `sarki_degistirme`: Şarkı güncelleme
- `kategori_ekleme`: Yeni kategori ekleme
- `kategori_silme`: Kategori silme
- `kategori_degistirme`: Kategori güncelleme

## Kaynak Tipleri

- `deezer`: Deezer API'sinden eklenen şarkılar
- `mp3`: Manuel MP3 dosyası yükleme
- `manuel`: Manuel giriş

## Kullanım Örnekleri

### Frontend'den İşlem Kayıtlarını Listeleme

```javascript
// Tüm kayıtları getir
fetch('/api/islem-kayitlari.php')
  .then((response) => response.json())
  .then((data) => console.log(data))

// Sadece şarkı ekleme işlemlerini getir
fetch('/api/islem-kayitlari.php?islem_tipi=sarki_ekleme')
  .then((response) => response.json())
  .then((data) => console.log(data))

// Sayfalama ile getir
fetch('/api/islem-kayitlari.php?sayfa=2&limit=10')
  .then((response) => response.json())
  .then((data) => console.log(data))
```

### Kayıt Silme

```javascript
fetch('/api/islem-kayitlari-sil.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    kayit_ids: [1, 2, 3],
  }),
})
  .then((response) => response.json())
  .then((data) => console.log(data))
```

### Manuel Kayıt Ekleme

```javascript
fetch('/api/islem-kayit-ekle.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    islem_tipi: 'sarki_ekleme',
    kaynak: 'manuel',
    kullanici_id: 1,
    kullanici_adi: 'Admin',
    detay: 'Test şarkısı eklendi',
    sarki_adi: 'Test Şarkısı',
    sanatci: 'Test Sanatçı',
    kategori: 'Test Kategori',
  }),
})
  .then((response) => response.json())
  .then((data) => console.log(data))
```

## Güvenlik

- Tüm API endpoint'leri CORS destekler
- SQL injection koruması için prepared statements kullanılır
- Giriş parametreleri doğrulanır ve temizlenir

## Performans

- İndeksler sayesinde hızlı sorgu performansı
- Sayfalama ile büyük veri setlerinde performans optimizasyonu
- Filtreleme özellikleri ile hedefli sorgular
