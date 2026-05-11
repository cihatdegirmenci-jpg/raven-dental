<?php
// [PATCH C3] Global dump() function KALDIRILDI — runtime namespace kirliliği
// + payment data leak riski (yorum satırı yanlışlıkla açılırsa). Debug için
// OpenCart'ın log servisi kullanılmalı: $this->log->write(...)

class Controllerextensionpaymentqnbpay extends Controller
{

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
            `notes` = ''");

        // 2. Admin'e mail at
        try {
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
            $mail->send();
        } catch (Throwable $e) { /* sessizce geç */ }

        // 3. Sipariş history'sine not ekle
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
            $order_id,
            $this->config->get('payment_qnbpay_order_status_id_cancel_requested') ?: 7,
            "Müşteri recurring abonelik iptali talep etti — admin manuel iptal edecek",
            true
        );

        // 4. Müşteriye onay
        $this->session->data['success'] = "Aboneliğiniz için iptal talebi alındı. 24 saat içinde işleme alınacaktır.";
        $this->response->redirect($this->url->link('account/recurring', '', true));
    }

    public function index()
    {
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/qnbpay');
        $this->load->model('extension/payment/qnbpay');

        $data['text_credit_card']          = $this->language->get('text_credit_card');
        $data['text_use_stored_card']      = $this->language->get('text_use_stored_card');
        $data['text_wait']                 = $this->language->get('text_wait');
        $data['entry_cc_owner']            = $this->language->get('entry_cc_owner');
        $data['entry_cc_number']           = $this->language->get('entry_cc_number');
        $data['entry_cc_expire_date']      = $this->language->get('entry_cc_expire_date');
        $data['entry_cc_cvv2']             = $this->language->get('entry_cc_cvv2');
        $data['entry_cc_cvv2_desc']        = $this->language->get('entry_cc_cvv2_desc');
        $data['entry_cc_cvv2_desc2']       = $this->language->get('entry_cc_cvv2_desc2');
        $data['make_payment']              = $this->language->get('make_payment');
        $data['text_no_have_stored_cards'] = $this->language->get('text_no_have_stored_cards');
        $data['vade_text']                 = $this->language->get('vade_text');
        $data['aylik_text']                = $this->language->get('aylik_text');
        $data['toplam_text']               = $this->language->get('toplam_text');
        $data['pesin_text']                = $this->language->get('pesin_text');
        $data['text_use_3d']               = $this->language->get('text_use_3d');
        $data['text_wait']                 = $this->language->get('text_wait');
        $data['button_confirm']            = $this->language->get('button_confirm');
        $data['button_back']               = $this->language->get('button_back');

        $data['no_use_saved_card'] = $this->language->get('no_use_saved_card');
        $data['button_back']       = $this->language->get('button_back');

        $data['entry_delete']     = $this->language->get('entry_delete');
        $data['entry_delete_url'] = $this->url->link('extension/payment/qnbpay/deletemycard', '', true);


        $this->qnbpay = new qnbpay(
            $this->config->get('payment_qnbpay_app_key'),
            $this->config->get('payment_qnbpay_app_secret'),
            $this->config->get('payment_qnbpay_merchant_key'),
            $this->config->get('payment_qnbpay_sale_web_hook_key'),
            $this->config->get('payment_qnbpay_recurring_web_hook_key'),
            $this->config->get('payment_qnbpay_environment'),
            $this->config->get('payment_qnbpay_debug')
        );

        // Token alma işlemi - hata kontrolü eklendi
        $tokenResult = $this->qnbpay->getToken();
        
        // Token alma başarısız olursa hata göster ama formu da göster
        if (is_array($tokenResult) && isset($tokenResult['status']) && $tokenResult['status'] == 'error') {
            $error_message = isset($tokenResult['message']) ? $tokenResult['message'] : 'Token Error';
            
            // Kullanıcıya uyarı mesajı göster (ama formu da göster)
            $data['qnbpay_warning'] = 'Ödeme sistemi bağlantı uyarısı: ' . $error_message . '. Lütfen tekrar deneyin.';
        }
        
        // Token yoksa veya hata varsa, is_3d varsayılan değerini set et
        // Formu her zaman göster (kullanıcı deneyebilir)
        if (!isset($this->qnbpay->is_3d) || $this->qnbpay->is_3d === null) {
            $this->qnbpay->is_3d = 1; // Varsayılan olarak 3D form göster
        }
        
        $returnurl     = $this->url->link('extension/payment/qnbpay/process', '', true);
        $order_info    = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $grandTotal    = $order_info['total'];
        $data['total'] = $this->cart->getTotal(); // + shipping
        $data['total'] = $grandTotal;

        if ($order_info["currency_code"] != "") {
            $currency_code = $order_info["currency_code"];
        } else {
            $currency_code = "TRY";
        }

        $currency_rate = $order_info["currency_value"]; //1.000 değilse farklı kur

        $order_total = number_format($order_info["total"] * $currency_rate, 2, '.', '');

        // Token hatası olsa bile formu göster
        // Sadece token başarılıysa ve is_3d 4 veya 8 ise redirect yap
        if (isset($tokenResult) && is_array($tokenResult) && $tokenResult['status'] == 'success' && ($this->qnbpay->is_3d == 4 or $this->qnbpay->is_3d == 8)) {
            // redirect
            $data["mode"]          = "redirect";
            $data["redirect_url2"] = $this->url->link('extension/payment/qnbpay/process', '', true);
        } else {
            // Token hatası olsa bile formu göster
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[] = sprintf("%02d", $i);
            }
            $years = [];
            for ($i = 0; $i <= 10; $i++) {
                $years[] = date('Y', strtotime('+' . $i . ' years'));
            }

            $savedCards      = [];
            // DÜZELTME 1: Misafir kontrolü eklendi
            $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
            
            // Token başarılıysa kayıtlı kartları getir
            if (isset($tokenResult) && is_array($tokenResult) && $tokenResult['status'] == 'success') {
                $qnbpaySavedCards = $this->qnbpay->getStoredCards($customer_id);
                
                if (isset($qnbpaySavedCards["status"]) && $qnbpaySavedCards["status"] == "success" && isset($qnbpaySavedCards["cards"]) && count($qnbpaySavedCards["cards"])) {
                    $cards = $qnbpaySavedCards["cards"];
                    foreach ($cards as $card)
                        $savedCards[$card->card_token] = $card->card_number;
                }
            }

            // form
            $data['card_store_feature'] = 0;
            if ($this->customer->isLogged()) {
                $data['card_store_feature'] = 1;
            }

            $data["qnbpay_show_3d_option"] = $this->qnbpay->is_3d == 1;
            $data["months"]               = $months;
            $data["years"]                = $years;
            $data["savedCards"]           = $savedCards;
            $data["mode"]                 = "form";
            $data["form_url"]             = $this->url->link('extension/payment/qnbpay/process', '', true);
        }

        //$this->document->addScript('view/javascript/qnbpay.js');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/qnbpay.twig')) {
            $this->template = $this->config->get('config_template') . '/template/payment/qnbpay.twig';
        } else {
            $this->template = 'default/template/payment/qnbpay.twig';
        }
        return $this->load->view('extension/payment/qnbpay', $data);
    }

    public function ajax()
    {
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/qnbpay');
        $this->load->model('extension/payment/qnbpay');
        // bin installment list
        $data['toplam_text'] = $this->language->get('toplam_text');
        $order_info          = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $currency_rate       = $order_info["currency_value"]; //1.000 değilse farklı kur
        $cart_total          = $this->cart->getTotal() * $currency_rate;
        $cart_products       = $this->cart->getProducts();

        $grandTotal    = $order_info['total'];
        $data['total'] = $this->cart->getTotal(); // + shipping
        $total         = $grandTotal;

        if (isset($this->request->get['getInstallments'])) {
            $card_number = $this->request->post['card'];
            $card_number = str_replace(' ', '', $card_number);

            if (strlen($card_number) > 6)
                $card_number = substr($card_number, 0, 6);

            $qnbpay = new qnbpay(
                $this->config->get('payment_qnbpay_app_key'),
                $this->config->get('payment_qnbpay_app_secret'),
                $this->config->get('payment_qnbpay_merchant_key'),
                $this->config->get('payment_qnbpay_sale_web_hook_key'),
                $this->config->get('payment_qnbpay_recurring_web_hook_key'),
                $this->config->get('payment_qnbpay_environment'),
                $this->config->get('payment_qnbpay_debug')
            );

            $interest_customer = $this->config->get('payment_qnbpay_interest_customer');

            $qnbpay->debug = false;
            
            // Token kontrolü - önce token al
            $tokenResult = $qnbpay->getToken();
            
            if (!is_array($tokenResult) || !isset($tokenResult['status']) || $tokenResult['status'] != 'success') {
                // Token alınamadı - sadece peşin seçeneğini göster
                $response_data = [
                    "status" => "success", 
                    "message" => "Token alınamadı, sadece peşin ödeme seçeneği gösteriliyor",
                    "taksitler" => [
                        "1" => [
                            "taksit" => 1,
                            "aylik" => number_format($total, 2, '.', ''),
                            "toplam" => number_format($total, 2, '.', ''),
                            "text" => "1 x " . number_format($total, 2, '.', '') . ", " . $data['toplam_text'] . " : " . number_format($total, 2, '.', '')
                        ]
                    ]
                ];
                $this->response->addHeader('Content-Type: application/json; charset=utf-8');
                $this->response->setOutput(json_encode($response_data, JSON_UNESCAPED_UNICODE));
                return;
            }
            
            $rates = $qnbpay->getInstallments($total, $order_info["currency_code"], $card_number, $interest_customer);
            
            // Hata kontrolü - false veya array değilse
            if ($rates === false || !is_array($rates)) {
                // API hatası - sadece peşin seçeneğini göster
                $response_data = [
                    "status" => "success", 
                    "message" => "Taksit bilgileri alınamadı, sadece peşin ödeme seçeneği gösteriliyor",
                    "taksitler" => [
                        "1" => [
                            "taksit" => 1,
                            "aylik" => number_format($total, 2, '.', ''),
                            "toplam" => number_format($total, 2, '.', ''),
                            "text" => "1 x " . number_format($total, 2, '.', '') . ", " . $data['toplam_text'] . " : " . number_format($total, 2, '.', '')
                        ]
                    ]
                ];
                $this->response->addHeader('Content-Type: application/json; charset=utf-8');
                $this->response->setOutput(json_encode($response_data, JSON_UNESCAPED_UNICODE));
                return;
            }
            
            // Array kontrolü ve status kontrolü
            if (!isset($rates["status"]) || $rates["status"] == "error") {
                $error_message = isset($rates["message"]) ? $rates["message"] : "Taksit bilgileri alınamadı";
                // Hata durumunda sadece peşin seçeneğini göster
                $response_data = [
                    "status" => "success", 
                    "message" => $error_message . " - Sadece peşin ödeme seçeneği gösteriliyor",
                    "taksitler" => [
                        "1" => [
                            "taksit" => 1,
                            "aylik" => number_format($total, 2, '.', ''),
                            "toplam" => number_format($total, 2, '.', ''),
                            "text" => "1 x " . number_format($total, 2, '.', '') . ", " . $data['toplam_text'] . " : " . number_format($total, 2, '.', '')
                        ]
                    ]
                ];
                $this->response->addHeader('Content-Type: application/json; charset=utf-8');
                $this->response->setOutput(json_encode($response_data, JSON_UNESCAPED_UNICODE));
                return;
            }
            
            $qnbpay_installments_config = $this->config->get('payment_qnbpay_installments');
            $qnbpay_installments = [];
            
            // Config'den taksit seçeneklerini al
            if (!empty($qnbpay_installments_config)) {
                $qnbpay_installments = explode(",", $qnbpay_installments_config);
                // String'leri integer'a çevir
                $qnbpay_installments = array_map('intval', $qnbpay_installments);
            }
            
            // Eğer config boşsa, tüm taksitleri göster (1-12)
            if (empty($qnbpay_installments)) {
                $qnbpay_installments = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            }
            
            // Her zaman 1 taksit (peşin) ekle
            if (!in_array(1, $qnbpay_installments)) {
                $qnbpay_installments[] = 1;
            }
            
            $taksitler = [];

            if (isset($rates["status"]) && $rates["status"] != "error" && isset($rates["data"]) && count($rates["data"])) {
                foreach ($rates["data"] as $bankTaksit) {
                    $vade = intval($bankTaksit->installments_number); // Integer'a çevir
                    
                    // Taksit seçeneği filtrelenmiş mi kontrol et
                    if (!in_array($vade, $qnbpay_installments, true)) {
                        continue;
                    }
                    
                    $toplam           = floatval($bankTaksit->amount_to_be_paid);
                    $aylik            = number_format($toplam / $vade, 2, '.', '');
                    $taksitler[$vade] = [
                        'taksit' => $vade,
                        'aylik'  => $aylik,
                        'toplam' => number_format($toplam, 2, '.', ''),
                        'text'   => $vade . " x " . $aylik . ", " . $data['toplam_text'] . " : " . number_format($toplam, 2, '.', '')
                    ];
                }
            }
            $this->response->addHeader('Content-Type: application/json; charset=utf-8');
            $this->response->setOutput(json_encode(["status" => "success", "taksitler" => $taksitler], JSON_UNESCAPED_UNICODE));
            return;
        }
    }

    public function process()
    {
        // Session ve sepet kontrolü
        if (!isset($this->session->data['order_id']) || empty($this->session->data['order_id'])) {
            $this->session->data['error'] = "Sipariş bulunamadı. Lütfen tekrar deneyin.";
            $this->response->redirect($this->url->link('checkout/cart', '', true));
            return;
        }
        
        // Sepet kontrolü
        if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
            $this->session->data['error'] = "Sepetiniz boş!";
            $this->response->redirect($this->url->link('checkout/cart', '', true));
            return;
        }
        
        $this->load->model('extension/total/coupon');
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/qnbpay');
        $this->load->model('extension/payment/qnbpay');
        
        // bin installment list
        $order_info    = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
        // Order info kontrolü
        if (!$order_info) {
            $this->session->data['error'] = "Sipariş bilgileri bulunamadı. Lütfen tekrar deneyin.";
            $this->response->redirect($this->url->link('checkout/cart', '', true));
            return;
        }
        $currency_rate = $order_info["currency_value"]; //1.000 değilse farklı kur
        $cart_total    = $this->cart->getTotal() * $currency_rate;
        $cart_products = $this->cart->getProducts();
        $grandTotal    = $order_info['total'];
        $data['total'] = $this->cart->getTotal(); // + shipping
        $total         = $grandTotal;

        if ($order_info["currency_code"] != "") {
            $currency_code = $order_info["currency_code"];
        } else {
            $currency_code = "TRY";
        }

        $qnbpay = new qnbpay(
            $this->config->get('payment_qnbpay_app_key'),
            $this->config->get('payment_qnbpay_app_secret'),
            $this->config->get('payment_qnbpay_merchant_key'),
            $this->config->get('payment_qnbpay_sale_web_hook_key'),
            $this->config->get('payment_qnbpay_recurring_web_hook_key'),
            $this->config->get('payment_qnbpay_environment'),
            $this->config->get('payment_qnbpay_debug')
        );

        $qnbpay->getToken();
        $qnbpay->return_url = $this->url->link('extension/payment/qnbpay/validation', '', true);
        $qnbpay->cancel_url = $this->url->link('extension/payment/qnbpay/validation', '', true);

        $items             = [];
        $productRecurrings = [];
        $itemTotal         = 0;
        foreach ($cart_products as $product) {

            $x          = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $currency_rate;
            $itemAmount = floatval(number_format($x, 2, '.', ''));

            if (isset($product["recurring"]) and $productRecurring = $product["recurring"])
                $productRecurrings[] = $productRecurring;

            $qnbpay->items[] = array(
                "code"             => ($product["product_id"]),
                //"name"=> $product["name"],
                "product_name"     => substr($product["name"], 0, 60),  //"name"=> substr($product["name"] , 0 , 60),
                "description"      => "",
                "product_quantity" => intval($product["quantity"]),
                "product_price"    => $itemAmount,
                //"recurring" => $productRecurring
            );
            $itemTotal      += $itemAmount * intval($product["quantity"]);
        }
        $recurring_payment = false;
        $isSame            = true;
        if (count($productRecurrings) > 0) {

            $recurring_payment = true;
            $types             = array_map('gettype', $productRecurrings);
            if (!$this->same($types)) {
                $isSame = false;
            }
            if ($isSame) {
                foreach ($productRecurrings as $productRecurring) {
                    if ($productRecurring != $productRecurrings[0]) {
                        $isSame = false;
                    }
                }
            }
        }

        if (!$isSame) {
            $this->session->data['error'] = "Sipariş edilen ürünlerin hepsi aynı tekrarlı ödeme değerine sahip olması gerekir";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        if ($recurring_payment) {
            if ($productRecurrings[0]["frequency"] == 'day')
                $cycle = 'D';
            elseif ($productRecurrings[0]["frequency"] == 'week')
                $cycle = 'W';
            elseif ($productRecurrings[0]["frequency"] == 'month')
                $cycle = 'M';
            elseif ($productRecurrings[0]["frequency"] == 'year')
                $cycle = 'Y';

            $qnbpay->order["recurring"]                  = 1;
            $qnbpay->order["recurring_payment_number"]   = $productRecurrings[0]["duration"];
            $qnbpay->order["recurring_payment_cycle"]    = $cycle;
            $qnbpay->order["recurring_payment_interval"] = $productRecurrings[0]["cycle"];
            $qnbpay->order["recurring_web_hook_key"]     = $this->config->get('payment_qnbpay_recurring_web_hook_key');

            // store sub payments for purchase if not created before
            /* @TODO
             * if (!qnbpayRecurringOrder::orderExist($cart->id)) {
             * for ($i = 1; $i <= $qnbpay->order["recurring_payment_number"]; $i++) {
             * $model = new qnbpayRecurringOrder();
             * $model->id_order = $cart->id;
             * $model->payment_number = $i;
             * $model->save();
             * }
             * }
             */
        }

        if (isset($this->session->data['shipping_method'])) {
            $shippingTotal = $this->session->data['shipping_method']['cost'];
            if ($shippingTotal > 0) {
                $qnbpay->items[] = array(
                    "code"             => "9191",
                    "product_name"     => "Kargo Ücreti",
                    "description"      => "Kargo",
                    "product_quantity" => 1,
                    "product_price"    => floatval(number_format(abs($shippingTotal), 2, '.', ''))
                );
                $itemTotal      += floatval(number_format(abs($shippingTotal), 2, '.', ''));
            }
        }


        $qnbpay->order["key"]       = $this->session->data['order_id'];
        $qnbpay->order["sub_total"] = number_format($grandTotal, 2, '.', '');
        $qnbpay->order["total"]     = number_format($this->request->post['payment_qnbpay_total'], 2, '.', ''); //$this->request->post['$grandTotal'];
        $coupon_info               = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon'] ?? null);
        $discount = 0;
        if ($qnbpay->order['total'] < $itemTotal)
            $discount = abs($itemTotal - $qnbpay->order['total']);


        $qnbpay->order["discount"]    = $discount; // @TODO number_format($order_info->getOrderTotal(true, Cart::ONLY_DISCOUNTS), 2, '.', '');
        
        // Taksit sayısı kontrolü - 0 veya boş ise 1 yap (peşin)
        $installmentCount = isset($this->request->post['installmentCount']) ? trim($this->request->post['installmentCount']) : '1';
        
        // Taksit sayısı 0, boş veya geçersiz ise 1 yap
        if ($installmentCount == '' || $installmentCount == '0' || $installmentCount == 0 || !is_numeric($installmentCount) || intval($installmentCount) < 1) {
            $installmentCount = '1';
        }
        
        // Tamsayıya çevir ve tekrar string yap
        $installmentCount = strval(intval($installmentCount));
        
        $qnbpay->order["installment"] = $installmentCount;

        $qnbpay->order["currency"]         = $currency_code; //KUR
        $qnbpay->order["transaction_type"] = $this->config->get("payment_qnbpay_provision");

        $qnbpay->paymentid                       = $this->session->data['order_id'] . "-" . date('dmY') . "-" . rand(100, 999);
        $this->session->data['qnbpay_paymentid'] = $qnbpay->paymentid;
        $phone                                  = $order_info["telephone"];
        
        // DÜZELTME 2: (customer_id Log Hatasının Çözümü)
        $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
        
        $qnbpay->customer                        = array(
            'id'        => $customer_id,
            'name'      => $order_info["firstname"] . " " . $order_info["lastname"],
            'firstname' => $order_info["firstname"],
            'lastname'  => $order_info["lastname"],
            'email'     => (string)$order_info["email"],
            'phone'     => $order_info["telephone"],
        );
        $pan                                    = $new_str = str_replace(' ', '', $this->request->post['pan']);
        list($ay, $yil) = explode('/', $this->request->post['expirationdate']);
        $qnbpay->card = [
            "owner" => $this->request->post['cardOwner'],
            "pan"   => $pan,
            "month" => $ay,
            "year"  => $yil,
            "cvc"   => $this->request->post['cvv']
        ];
        /*
        $qnbpay->billing  = array(
            'email'    => (string)$order_info["email"],
            'address'  => $order_info["payment_address_1"] . " " . $order_info["payment_address_2"],
            'address1' => $order_info["payment_address_1"],
            'address2' => $order_info["payment_address_2"],
            'city'     => $order_info["payment_city"],
            'country'  => $order_info["payment_country"],
            'state'    => $order_info["payment_zone"],
            'postcode' => $order_info["payment_postcode"],
            'phone'    => $order_info["telephone"],
        );*/
        $qnbpay->shipping = array(
            'address' => $order_info["shipping_address_1"] . " " . $order_info["shipping_address_2"],
            'city'    => $order_info["shipping_city"],
            'country' => $order_info["shipping_country"],
            'zip'     => $order_info["shipping_postcode"],
            'phone'   => $order_info["telephone"],
        );

        // save card ?
        $pan = $new_str = str_replace(' ', '', $this->request->post['pan']);
        list($ay, $yil) = explode('/', $this->request->post['expirationdate']);
        if (isset($this->request->post['saveCard'])) {
            $actionStoreCard = $qnbpay->storeCard(
                $customer_id, // Burası da düzeltildi
                $this->request->post['cardOwner'],
                $pan,
                $ay,
                $yil,
                $order_info["firstname"] . " " . $order_info["lastname"],
                $order_info["telephone"]
            );
            if ($actionStoreCard["status"] != "success") {
                if ($qnbpay->debug == '1') {
                }
            }
        }

        if (!empty($qnbpay->is_3d) && $qnbpay->is_3d == 4 or !empty($this->qnbpay) && $this->qnbpay->is_3d == 8) {
            $qnbpayForm = $qnbpay->generatePaymentLink();
            $mode      = "redirect";
        } else {
            $formmethod = 'POST';
            $mode       = "httppost";
            if (isset($this->request->post['useCard']) and $this->request->post['useCard'] != "0")
                $qnbpayForm = $qnbpay->generateSavedCardForm($this->request->post['useCard']);
            elseif ($qnbpay->is_3d == 2 or ($qnbpay->is_3d == 1 and isset($this->request->post['use3d'])))
                $qnbpayForm = $qnbpay->generate3DForm($this->config->get('payment_qnbpay_interest_customer'));
            else {
                $mode      = "redirect";
                $qnbpayForm = $qnbpay->generate2DForm();
            }
        }

        if ($qnbpayForm["status"] == "success") {
            if ($mode == "httppost") {
                $httpForm               = $qnbpayForm["form"];
                $data["form"]           = $httpForm;
                $data["form"]["method"] = $formmethod;
                $data["debug"]          = $qnbpay->debug;

                return $this->response->setOutput(
                    $this->load->view('extension/payment/qnbpay_execution', $data)
                );
            } elseif ($mode == "redirect") {
                if ($qnbpay->debug) {
                    $asi          = $qnbpayForm["redirect"];
                    $redirectHtml = '<script>window.setTimeout(function () {
        location.href = "' . $asi . '";
    }, 5000);</script>';

                    echo $redirectHtml;
                }
                $this->response->redirect($qnbpayForm["redirect"]);
                return;
            }
        } else {
            $this->session->data['error'] = $qnbpayForm["message"];
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }

    public function validation()
    {
        $this->load->model('checkout/order');
        $this->language->load('extension/payment/qnbpay');
        $this->load->model('extension/payment/qnbpay');

        $qnbpay = new qnbpay(
            $this->config->get('payment_qnbpay_app_key'),
            $this->config->get('payment_qnbpay_app_secret'),
            $this->config->get('payment_qnbpay_merchant_key'),
            $this->config->get('payment_qnbpay_sale_web_hook_key'),
            $this->config->get('payment_qnbpay_recurring_web_hook_key'),
            $this->config->get('payment_qnbpay_environment'),
            $this->config->get('payment_qnbpay_debug')
        );

        // [PATCH 01] GET ve POST'tan oku — QNB callback hem GET hem POST gönderebilir
        $hashKey            = $this->request->get['hash_key']           ?? $this->request->post['hash_key']           ?? '';
        $qnbpayStatus       = $this->request->get['qnbpay_status']      ?? $this->request->post['qnbpay_status']      ?? '';
        $invoice_id         = $this->request->get['invoice_id']         ?? $this->request->post['invoice_id']         ?? '';
        $status_code        = $this->request->get['status_code']        ?? $this->request->post['status_code']        ?? '';
        $status_description = $this->request->get['status_description'] ?? $this->request->post['status_description'] ?? '';

        // [PATCH 01] Hash key kontrolü — ZORUNLU (eski $hashControl = 0 KALDIRILDI)
        if (empty($hashKey)) {
            $this->session->data['error'] = "Ödeme doğrulanamadı: hash key eksik.";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        $hashParts = qnbpay::validateHashKey($hashKey, $this->config->get('payment_qnbpay_app_secret'));
        // validateHashKey döner: [status, total, invoiceId, orderId, currencyCode]

        if (empty($hashParts[2]) || $hashParts[2] != $invoice_id) {
            $this->session->data['error'] = "Ödeme doğrulanamadı: invoice eşleşmedi.";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        if (!empty($qnbpayStatus) && $hashParts[0] != $qnbpayStatus) {
            $this->session->data['error'] = "Ödeme doğrulanamadı: status eşleşmedi.";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        // Status code kontrolü
        if ($status_code != '100') {
            $this->session->data['error'] = "Ödeme İşlemi Tamamlanamadı. (" . htmlspecialchars($status_code) . " : " . htmlspecialchars($status_description) . ")";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        // Invoice ID'den order_id'yi çıkar
        $order_id = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : 0;
        if ($order_id == 0 && $invoice_id != '') {
            $explode = explode('_', $invoice_id);
            $order_id = isset($explode[0]) ? (int)$explode[0] : 0;
        }

        if ($order_id == 0) {
            $this->session->data['error'] = "Sipariş ID bulunamadı. Lütfen müşteri hizmetleri ile iletişime geçin.";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        // [PATCH 01] DEFENSE IN DEPTH: QNB API'ye tekrar sor (webhook'taki pattern aynısı)
        $qnbpay->getToken();
        $statusResult = $qnbpay->checkStatus($order_id);
        $apiStatus = is_array($statusResult)
            ? ($statusResult['status_code'] ?? null)
            : (isset($statusResult->status_code) ? $statusResult->status_code : null);

        if ($apiStatus != '100') {
            $this->session->data['error'] = "Ödeme QNB tarafında onaylanamadı. Sipariş tamamlanmadı.";
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
            return;
        }

        // Tüm kontroller geçti — sipariş başarılı
        $this->session->data['order_id'] = $order_id;

        $message = "Kredi Kartı Ödeme Başarılı";
        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_qnbpay_order_status_id'), $message, false);

        // Transaction kaydı oluştur
        $object                = new stdClass();
        $object->order_id      = $order_id;
        $object->status        = 'success';
        $object->amount        = isset($this->request->post['amount']) ? $this->request->post['amount'] : '';
        $object->currency      = isset($this->request->post['currency']) ? $this->request->post['currency'] : '';
        $object->type          = 'sale';
        $object->reference     = isset($this->session->data['qnbpay_paymentid']) ? $this->session->data['qnbpay_paymentid'] : '';
        $object->operation     = '';
        $object->transactionId = isset($this->session->data['qnbpay_paymentid']) ? $this->session->data['qnbpay_paymentid'] : '';
        $object->message       = $message;
        $object->code          = $status_code;
        $object->purchase_url  = '';
        $this->model_extension_payment_qnbpay->appendi((array)$object);

        // Success sayfasına yönlendir - order_id session'da olduğu için sepet temizlenecek
        $this->response->redirect($this->url->link('checkout/success', '', true));
    }

    public function webhook()
    {

        $this->load->model('checkout/order');
        $this->language->load('extension/payment/qnbpay');
        $this->load->model('extension/payment/qnbpay');

        $p = $this->request->get + $this->request->post;

        // Token kontrolü
        if (!isset($p['token']) || trim($this->config->get('payment_qnbpay_token')) != trim($p['token'])) {
            http_response_code(401);
            die("401 Unauthorized");
        }

        // Invoice ID'den order_id'yi çıkar
        $order_id = 0;
        if (isset($p['invoice_id'])) {
            $explode = explode('_', $p['invoice_id']);
            $order_id = isset($explode[0]) ? (int)$explode[0] : 0;
        }

        if ($order_id == 0) {
            http_response_code(400);
            die("400 Bad Request - Invalid invoice_id");
        }

        $qnbpay = new qnbpay(
            $this->config->get('payment_qnbpay_app_key'),
            $this->config->get('payment_qnbpay_app_secret'),
            $this->config->get('payment_qnbpay_merchant_key'),
            $this->config->get('payment_qnbpay_sale_web_hook_key'),
            $this->config->get('payment_qnbpay_recurring_web_hook_key'),
            $this->config->get('payment_qnbpay_environment'),
            $this->config->get('payment_qnbpay_debug')
        );

        $statusResult = $qnbpay->checkStatus($order_id);
        $p = is_array($statusResult) ? $statusResult : (array)$statusResult;

        // Webhook işlem tipine göre sipariş durumunu güncelle
        if ($this->request->get['do'] == 'sale') {
            // Satış webhook - sipariş durumunu güncelle
            if (isset($p['status_code']) && $p['status_code'] == '100') {
                $this->model_checkout_order->addOrderHistory(
                    $order_id,
                    $this->config->get('payment_qnbpay_order_status_id'),
                    'QNB Pay Webhook: Ödeme başarılı',
                    false
                );
            }
        } elseif ($this->request->get['do'] == 'refund') {
            // İade webhook
            $this->model_checkout_order->addOrderHistory(
                $order_id,
                11, // İade durumu
                'QNB Pay Webhook: İade işlemi',
                false
            );
        } elseif ($this->request->get['do'] == 'recurring') {
            // Tekrarlı ödeme webhook
            if (isset($p['status_code']) && $p['status_code'] == '100') {
                $this->model_checkout_order->addOrderHistory(
                    $order_id,
                    $this->config->get('payment_qnbpay_order_status_id'),
                    'QNB Pay Webhook: Tekrarlı ödeme başarılı',
                    false
                );
            }
        }

        // Transaction kaydı oluştur
        $object                = new stdClass();
        $object->order_id      = $order_id;
        $object->status        = isset($p['status']) ? $p['status'] : '';
        $object->amount        = isset($p['amount']) ? $p['amount'] : '';
        $object->currency      = isset($p['currency']) ? $p['currency'] : '';
        $object->type          = isset($this->request->get['do']) ? $this->request->get['do'] : '';
        $object->reference     = isset($p['invoice_id']) ? $p['invoice_id'] : '';
        $object->operation     = '';
        $object->transactionId = isset($p['invoice_id']) ? $p['invoice_id'] : '';
        $object->message       = isset($p['error']) ? $p['error'] : (isset($p['message']) ? $p['message'] : '');
        $object->code          = isset($p['status_code']) ? $p['status_code'] : '';
        $object->purchase_url  = '';
        $this->model_extension_payment_qnbpay->appendi((array)$object);
        
        // Webhook yanıtı
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Webhook processed']);
        exit;
    }

    public function deletemycard()
    {
        // [PATCH 02] Login zorunlu
        if (!$this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/login', '', true));
            return;
        }

        // [PATCH 02] card_token validation
        $cardToken = $this->request->get['card_token'] ?? '';
        if (empty($cardToken) || !preg_match('/^[a-zA-Z0-9\-_]{8,128}$/', $cardToken)) {
            $this->response->redirect($this->url->link('account/account', '', true));
            return;
        }

        $this->qnbpay = new qnbpay(
            $this->config->get('payment_qnbpay_app_key'),
            $this->config->get('payment_qnbpay_app_secret'),
            $this->config->get('payment_qnbpay_merchant_key'),
            $this->config->get('payment_qnbpay_sale_web_hook_key'),
            $this->config->get('payment_qnbpay_recurring_web_hook_key'),
            $this->config->get('payment_qnbpay_environment'),
            $this->config->get('payment_qnbpay_debug')
        );

        $customer_id = $this->customer->getId();
        $this->qnbpay->deleteStoredCard($cardToken, $customer_id);

        // [PATCH 02] Güvenli redirect — sadece same-origin referer kabul edilir
        $referer = $this->request->server['HTTP_REFERER'] ?? '';
        $host = $this->request->server['HTTP_HOST'] ?? '';
        $allowedReferer = '';
        if (!empty($referer) && !empty($host)) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            if ($refererHost && $refererHost === $host) {
                $allowedReferer = $referer;
            }
        }

        if (!empty($allowedReferer)) {
            $this->response->redirect($allowedReferer);
        } else {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
    }

    public function same($arr)
    {
        return $arr === array_filter($arr, function ($element) use ($arr) {
            return ($element === $arr[0]);
        });
    }
}