# Ayarlar (Settings) API Documentation

Bu API, kullanıcı ayarlarını yönetmek için kullanılır. Ayarlar veritabanında güvenli bir şekilde saklanır ve kullanıcı farklı cihazlardan giriş yaptığında korunur.

## Veritabanı Tablosu

```sql
CREATE TABLE ayarlar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kullanici_id INT,
    tema VARCHAR(20) DEFAULT 'dark',
    sayfa_boyutu INT DEFAULT 20,
    bildirim_sesi BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## API Endpoints

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

## Kullanım Örnekleri

### JavaScript/Frontend Kullanımı:

```javascript
// Ayarları getir
async function getAyarlar(kullaniciId) {
  try {
    const response = await fetch(`/api/ayarlar.php?kullanici_id=${kullaniciId}`)
    const data = await response.json()

    if (data.success) {
      return data.data
    } else {
      throw new Error(data.error)
    }
  } catch (error) {
    console.error('Ayarlar getirilemedi:', error)
    // Varsayılan ayarları döndür
    return {
      tema: 'dark',
      sayfa_boyutu: 20,
      bildirim_sesi: true,
    }
  }
}

// Ayarları kaydet
async function saveAyarlar(kullaniciId, ayarlar) {
  try {
    const response = await fetch('/api/ayarlar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        kullanici_id: kullaniciId,
        tema: ayarlar.tema,
        sayfa_boyutu: ayarlar.sayfa_boyutu,
        bildirim_sesi: ayarlar.bildirim_sesi,
      }),
    })

    const data = await response.json()

    if (data.success) {
      console.log('Ayarlar kaydedildi')
      return true
    } else {
      throw new Error(data.error)
    }
  } catch (error) {
    console.error('Ayarlar kaydedilemedi:', error)
    return false
  }
}

// Kullanım örneği
const kullaniciId = 1

// Ayarları getir
const ayarlar = await getAyarlar(kullaniciId)
console.log('Mevcut ayarlar:', ayarlar)

// Ayarları güncelle
ayarlar.tema = 'light'
ayarlar.sayfa_boyutu = 50
ayarlar.bildirim_sesi = false

// Yeni ayarları kaydet
await saveAyarlar(kullaniciId, ayarlar)
```

### PHP Kullanımı:

```php
// Ayarları getir
function getAyarlar($kullanici_id) {
    $url = "http://localhost/songle-backend/api/ayarlar.php?kullanici_id=$kullanici_id";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Ayarları kaydet
function saveAyarlar($kullanici_id, $ayarlar) {
    $data = [
        'kullanici_id' => $kullanici_id,
        'tema' => $ayarlar['tema'],
        'sayfa_boyutu' => $ayarlar['sayfa_boyutu'],
        'bildirim_sesi' => $ayarlar['bildirim_sesi']
    ];

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data)
        ]
    ]);

    $response = file_get_contents("http://localhost/songle-backend/api/ayarlar.php", false, $context);
    return json_decode($response, true);
}

// Kullanım örneği
$kullanici_id = 1;

// Ayarları getir
$ayarlar = getAyarlar($kullanici_id);
echo "Mevcut ayarlar: " . print_r($ayarlar, true);

// Ayarları güncelle
$ayarlar['data']['tema'] = 'light';
$ayarlar['data']['sayfa_boyutu'] = 50;
$ayarlar['data']['bildirim_sesi'] = false;

// Yeni ayarları kaydet
$result = saveAyarlar($kullanici_id, $ayarlar['data']);
echo "Kaydetme sonucu: " . print_r($result, true);
```

## Güvenlik ve Validasyon

1. **Kullanıcı Doğrulama**: Her istek için kullanıcının veritabanında var olup olmadığı kontrol edilir
2. **Tema Validasyonu**: Sadece "dark" ve "light" değerleri kabul edilir
3. **Sayfa Boyutu Validasyonu**: 1-100 arası değerler kabul edilir
4. **SQL Injection Koruması**: Prepared statements kullanılır
5. **XSS Koruması**: JSON response'ları güvenli şekilde encode edilir

## Varsayılan Değerler

Eğer kullanıcının henüz ayarları yoksa, aşağıdaki varsayılan değerler döndürülür:

```json
{
  "tema": "dark",
  "sayfa_boyutu": 20,
  "bildirim_sesi": true
}
```

## Hata Kodları

- **200**: Başarılı
- **400**: Bad Request (geçersiz parametreler)
- **404**: Not Found (kullanıcı bulunamadı)
- **405**: Method Not Allowed (desteklenmeyen HTTP metodu)
- **500**: Internal Server Error (sunucu hatası)
