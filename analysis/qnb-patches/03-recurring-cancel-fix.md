# Patch 03 — recurringCancel() Implementation

> **Severity:** 🟠 HIGH (tüketici hakkı + yasal risk)
> **Risk:** Müşteri aboneliği iptal eder sanır, ama tahsil edilmeye devam eder
> **Affected:** `catalog/controller/extension/payment/qnbpay.php` → `recurringCancel()` (satır 13-17)

---

## Sorun

```php
public function recurringCancel()
{
    return "";
}
```

**Method tamamen BOŞ.** Müşteri "aboneliğimi iptal et" tıkladığında bu çağrılır ama:
- DB'de "iptal" işaretlenmez
- QNB API'ye iptal isteği gitmez
- Müşteriye onay mesajı gösterilmez

Sonuç: Tahsil edilmeye devam eder, müşteri şikayet eder, yasal sorun.

## Fix Stratejisi

⚠️ **Bu yama "iskelet" (skeleton)** — QNB Pay API'nin recurring cancel endpoint'inin tam spesifikasyonu lazım. Bu yamanın **tamamlanması için QNB API docs'unda spesifik endpoint adı + parametreleri** öğrenilmeli:
- Endpoint: `?route=ccpayment/api/cancelRecurring` veya `recurring/cancel` (TAHMİN — doğrula)
- Authentication: token + merchant_key + invoice_id
- Hash: invoice_id imzalı

İskelet 3 davranış sağlar:
1. ⚠️ **Şu anki davranıştan iyi:** Hata mesajı verir, müşteriye "kontrolden geçti, müşteri hizmetleri arayın" der
2. Audit log: kim hangi sipariş için iptal istedi (DB)
3. Email bildirimi: admin'e iptal talebi gelir

Tam fix için **QNB ile koordinasyon** gerekli.

## SONRASI (İskelet, manuel müdahale fallback)

```php
public function recurringCancel()
{
    // [PATCH 03] Skeleton — gerçek QNB API recurring cancel henüz entegre değil
    // TODO: QNB Pay'in recurring cancel endpoint'i tespit edilince güncelle

    $this->load->model('extension/payment/qnbpay');
    $this->language->load('extension/payment/qnbpay');

    if (!$this->customer->isLogged()) {
        $this->response->redirect($this->url->link('account/login', '', true));
        return;
    }

    $order_id = (int)($this->request->get['order_id'] ?? 0);
    if ($order_id <= 0) {
        $this->session->data['error'] = "Geçersiz sipariş.";
        $this->response->redirect($this->url->link('account/recurring', '', true));
        return;
    }

    // Sipariş gerçekten müşteriye ait mi?
    $this->load->model('account/recurring');
    $recurring_info = $this->model_account_recurring->getRecurring($order_id);
    if (!$recurring_info || $recurring_info['customer_id'] != $this->customer->getId()) {
        $this->session->data['error'] = "Bu siparişe erişim yetkiniz yok.";
        $this->response->redirect($this->url->link('account/recurring', '', true));
        return;
    }

    // 1. DB'de iptal talebini kaydet (manuel müdahale için)
    $this->db->query("INSERT INTO `" . DB_PREFIX . "qnbpay_recurring_cancel_requests` SET
        `order_id` = '" . (int)$order_id . "',
        `customer_id` = '" . (int)$this->customer->getId() . "',
        `requested_at` = NOW(),
        `status` = 'pending',
        `notes` = ''
    ");

    // 2. Admin'e mail at (otomatik iptal yapılana kadar)
    $this->load->library('mail');
    $mail = new Mail($this->config->get('config_mail_engine'));
    $mail->setTo($this->config->get('config_email'));
    $mail->setFrom($this->config->get('config_email'));
    $mail->setSender($this->config->get('config_name'));
    $mail->setSubject("Recurring Cancel Request: Order #$order_id");
    $mail->setText(
        "Müşteri (ID: " . $this->customer->getId() . ") sipariş #$order_id için recurring abonelik iptali talep etti.\n\n" .
        "Lütfen QNB Pay panelinden manuel olarak iptal edin ve qnbpay_recurring_cancel_requests tablosunda status'u güncelleyin.\n\n" .
        "Order ID: $order_id\n" .
        "Customer Email: " . $this->customer->getEmail() . "\n" .
        "Tarih: " . date('Y-m-d H:i:s')
    );
    try { $mail->send(); } catch (Throwable $e) { /* sessizce geç */ }

    // 3. Sipariş history'sine not ekle
    $this->load->model('checkout/order');
    $this->model_checkout_order->addOrderHistory(
        $order_id,
        $this->config->get('payment_qnbpay_order_status_id_cancel_requested') ?: 7, // "Cancel Pending" veya benzeri
        "Müşteri recurring abonelik iptali talep etti — admin manuel iptal edecek",
        true  // notify customer
    );

    // 4. Müşteriye onay
    $this->session->data['success'] = "Aboneliğiniz için iptal talebi alındı. 24 saat içinde işleme alınacaktır. Onay maili size iletilecektir.";
    $this->response->redirect($this->url->link('account/recurring', '', true));
}
```

## Yeni DB Tablosu Gerekli

```sql
CREATE TABLE IF NOT EXISTS `oc_qnbpay_recurring_cancel_requests` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `order_id` INT NOT NULL,
    `customer_id` INT NOT NULL,
    `requested_at` DATETIME NOT NULL,
    `processed_at` DATETIME NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending',  -- pending, processed, failed
    `notes` TEXT,
    `processed_by` INT NULL,  -- admin user_id
    PRIMARY KEY (`id`),
    INDEX `idx_order` (`order_id`),
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Test Adımları

### Pozitif test
1. Login
2. Hesabım → Recurring Subscriptions
3. Bir aboneliğin "İptal Et" butonu
4. Tıkla
5. ✅ Success mesajı: "İptal talebi alındı"
6. Email admin'e ulaşır
7. DB'de `qnbpay_recurring_cancel_requests` row eklenmiş
8. Order history'de not var

### Negatif test 1 (login değil)
- Direct URL ziyaret
- Beklenen: login sayfasına yönlendirir

### Negatif test 2 (başkasının order_id)
- A user login
- B user'ın `order_id`'sini deneyerek `?order_id=<B_id>`
- Beklenen: "Bu siparişe erişim yetkiniz yok"

### Negatif test 3 (geçersiz order_id)
- `?order_id=999999999`
- Beklenen: "Geçersiz sipariş" hatası

## Faz 2: Otomatik İptal

İskelet ile manual müdahale çalışırken, **QNB API recurring cancel endpoint** ile entegrasyon:

```php
// İlerideki tam fix
$qnbpay = new qnbpay(...);
$qnbpay->getToken();

$result = $qnbpay->cancelRecurring($order_id);  // <-- library'e eklenecek method

if ($result['status'] === 'success') {
    // DB'de status='processed'
    // Order status'u iptal'e geçir
    // Müşteriye email "abonelik iptal edildi"
} else {
    // DB'de status='failed' + error
    // Admin'e email
}
```

**QNB API endpoint öğrenilmeli:** apidocs.qnbpay.com.tr/recurring/cancel veya QNB destek'ten sorulmalı.

## Risk

### Uygulama Riski
- Yeni DB tablosu — `qnbpay_recurring_cancel_requests` CREATE TABLE çalıştırılmalı
- Order status ID `payment_qnbpay_order_status_id_cancel_requested` admin'den eklenmeli (yoksa fallback=7)
- Email config doğru olmalı (yoksa try/catch ile sessizce geçer)

### Geri Dönüş
- Method'u tekrar `return "";` yap — eski davranışa döner

### Customer Experience
- ✅ **Büyük iyileşme:** Şu an müşteri "iptal ettim" sanırken hiçbir şey olmuyor — bu en azından admin'e bilgi gönderiyor
- Manuel iptal süreci doğmuş oluyor

## Dependency

- Yeni DB tablosu (CREATE TABLE)
- (Opsiyonel) Admin için yeni order_status_id

## Bekleyen Sorular (bolkarco veya QNB'ye)

- [ ] QNB Pay'in recurring cancel API endpoint'i nedir?
- [ ] Manuel iptal: QNB merchant panelinden mi yapılır?
- [ ] Bu OpenCart store'un recurring kullanan müşterisi var mı şu an? (Risk önceliği değişir)
