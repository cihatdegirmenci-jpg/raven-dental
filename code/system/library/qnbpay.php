<?php

class qnbpay
{
    public $debug = false;
    public $env = 'test';
    public $env_domain = array(
        'test' => 'https://test.qnbpay.com.tr',
        'prod' => 'https://portal.qnbpay.com.tr'
    );
    public $items;
    public $customer;
    public $card;
    private $credentials;
    public $is_3d;
    public $order;
    private $app_id;
    private $app_secret;
    private $merchant_key;
    public $token;
    public $paymentid;
    public $response;
    public $webhookurl;
    public $return_url;
    public $sale_web_hook_key;
    public $recurring_web_hook_key;
    public $cancel_url;
    public $billing;

    public function __construct($app_id, $app_secret, $merchant_key, $sale_web_hook_key, $recurring_web_hook_key, $env = 'test', $debug = false)
    {
        $this->debug = $debug;
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->merchant_key = $merchant_key;
        $this->sale_web_hook_key = $sale_web_hook_key;
        $this->recurring_web_hook_key = $recurring_web_hook_key;
        
        // Environment normalizasyonu - 'production' -> 'prod'
        if ($env == 'production') {
            $env = 'prod';
        }
        $this->env = $env;
    }

    /**
     * @param $total
     * @param $installment
     * @param $currency_code
     * @param $merchant_key
     * @param $invoice_id
     * @param $app_secret
     * @return string
     */
    private function generateHashKey($parts, $app_secret): string
    {
        $data = implode("|", $parts);

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            "$data",
            'aes-256-cbc',
            "$saltWithPassword",
            null,
            $iv
        );
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
        return $msg_encrypted_bundle;
    }

    /**
     * @param $endpoint
     * @return string
     */
    public function getUrl($endpoint): string
    {
        // Environment kontrolü - 'prod' veya 'production' için aynı URL
        $env_key = $this->env;
        if ($env_key == 'production') {
            $env_key = 'prod';
        }
        
        // Eğer environment key yoksa, varsayılan olarak 'prod' kullan
        if (!isset($this->env_domain[$env_key])) {
            $env_key = 'prod';
        }
        
        return $this->env_domain[$env_key] . $endpoint;
    }

    public function getHeader()
    {

        $return = array('Accept: application/json', 'Content-Type: application/json');

        if (isset($this->token) and $this->token != "")
            $return[] = 'Authorization: ' . $this->token;

        return $return;
    }

    public function checkStatus($invoice_id)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $requestParams = [
            'invoice_id' => $invoice_id,
            'merchant_key' => $this->merchant_key,
            'hash_key' => $this->generateHashKey([$invoice_id, $this->merchant_key], $this->app_secret),

        ];

        $this->response = $this->makeRequest("/ccpayment/api/checkstatus", $requestParams);

        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            return [
                "status" => "success",
                "message" => "",
                "data" => $this->response->data
            ];
        } else {
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Unknown Error',
            ];
        }
    }

    public function getToken()
    {
        $requestParams = [
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret
        ];

        $this->response = $this->makeRequest("/ccpayment/api/token", $requestParams);
        
        // Response kontrolü - false veya null olabilir
        if ($this->response === false || $this->response === null) {
            return [
                "status" => "error",
                "message" => "API bağlantı hatası. Lütfen API URL'lerini ve bağlantı ayarlarını kontrol edin.",
            ];
        }
        
        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            $this->token = 'Bearer ' . $this->response->data->token;
            $this->is_3d = isset($this->response->data->is_3d) ? $this->response->data->is_3d : 1;
            return [
                "status" => "success",
                "message" => "",
            ];
        } else {
            $error_message = 'Token Error';
            if (isset($this->response->status_description)) {
                $error_message = $this->response->status_description;
            } elseif (isset($this->response->message)) {
                $error_message = $this->response->message;
            }
            
            return [
                "status" => "error",
                "message" => $error_message,
            ];
        }
    }

    public function makeRequest($endpoint, $data, $method = 'POST')
    {
        $url = $this->getUrl($endpoint);

        if ($method == 'GET')
            $url .= "?" . http_build_query($data);

        $headers = $this->getHeader();

        $ch = curl_init($url);

        if ($method == 'POST')
            curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($method == 'POST')
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // SSL ayarları
        if ($this->env == 'test') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        
        $output1 = curl_exec($ch);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // CURL hata kontrolü
        if ($curl_errno !== 0) {
            if ($this->debug) {
                dump([
                    "endpoint" => $endpoint,
                    "url" => $url,
                    "curl_error" => $curl_error,
                    "curl_errno" => $curl_errno,
                    "http_code" => $http_code,
                ]);
            }
            return false;
        }

        // HTTP status code kontrolü
        if ($http_code < 200 || $http_code >= 300) {
            if ($this->debug) {
                dump([
                    "endpoint" => $endpoint,
                    "url" => $url,
                    "http_code" => $http_code,
                    "response" => substr($output1, 0, 500), // İlk 500 karakter
                ]);
            }
            return false;
        }

        // JSON kontrolü - önce kontrol et, sonra decode et
        if (!isJson($output1)) {
            if ($this->debug) {
                dump([
                    "endpoint" => $endpoint,
                    "url" => $url,
                    "output1" => substr($output1, 0, 500), // İlk 500 karakter
                    "is_json" => false,
                ]);
            }
            return false;
        }

        $output2 = json_decode($output1);
        
        // JSON decode hatası kontrolü
        if (json_last_error() !== JSON_ERROR_NONE) {
            if ($this->debug) {
                dump([
                    "endpoint" => $endpoint,
                    "url" => $url,
                    "json_error" => json_last_error_msg(),
                    "output1" => substr($output1, 0, 500),
                ]);
            }
            return false;
        }

        if ($this->debug) {
            dump([
                "endpoint" => $endpoint,
                "header" => $this->getHeader(),
                "data" => $data,
                "url" => $url,
                "http_code" => $http_code,
                "output1" => $output1,
                "output2" => $output2,
            ]);
        }

        return $output2;
    }

    public function getInstallments($amount, $curr, $cc_no, $interest_customer = 0)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if (!is_array($status) || !isset($status["status"]) || $status["status"] != "success") {
                $error_message = isset($status["message"]) ? $status["message"] : "Token alınamadı";
                return [
                    "status" => "error",
                    "message" => $error_message,
                    "data" => []
                ];
            }
        }

        $requestParams = [
            'credit_card' => $cc_no,
            'amount' => $amount,
            'currency_code' => $curr,
            'merchant_key' => $this->merchant_key,
            'is_comission_from_user' => $interest_customer,
            'is_single_payment_allowed' => true
        ];

        $this->response = $this->makeRequest("/ccpayment/api/getpos", $requestParams);

        // Response kontrolü - false veya null olabilir
        if ($this->response === false || $this->response === null) {
            return [
                "status" => "error",
                "message" => "API bağlantı hatası. Taksit bilgileri alınamadı.",
                "data" => []
            ];
        }

        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            return [
                "status" => "success",
                "message" => "",
                "req" => $requestParams,
                "data" => isset($this->response->data) ? $this->response->data : []
            ];
        } else {
            $error_message = 'Installment Error';
            if (isset($this->response->status_description)) {
                $error_message = $this->response->status_description;
            } elseif (isset($this->response->message)) {
                $error_message = $this->response->message;
            }
            
            return [
                "status" => "error",
                "message" => $error_message,
                "data" => []
            ];
        }
    }

    public function getStoreInstallments()
    {
        ///api/installments
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                return [
                    "status" => "error",
                    "message" => isset($this->response->status_description) ? $this->response->status_description : '',
                ];
        }

        $requestParams = [
            'merchant_key' => $this->merchant_key,
        ];

        $this->response = $this->makeRequest("/ccpayment/api/installments", $requestParams);

        $inst = [];
        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            foreach ($this->response->installments as $key => $installment) {
                $inst[$key + 1] = $installment;
            }
            return [
                "status" => "success",
                "message" => "",
                "data" => $inst
            ];
        } else {
            return [
                "status" => "error",
                "message" => (isset($this->response->status_description) ? $this->response->status_description : $this->response->message),
            ];
        }
    }

    public function generateSavedCardForm($savedCardToken)
    {
        $invoice = $this->_generateFormFields($savedCardToken);
        return [
            "status" => "success",
            "form" =>
            [
                "url" => $this->getUrl("/ccpayment/api/payByCardToken"),
                "inputs" => $invoice,
            ]
        ];
    }

    public function generate3DForm($isCommissionFromUser = 0)
    {
        $invoice = $this->_generateFormFields(false, true, $isCommissionFromUser);
        
        return [
            "status" => "success",
            "form" =>
            [
                "url" => $this->getUrl("/ccpayment/api/paySmart3D"),
                "inputs" => $invoice,
            ]
        ];
    }

    public function generate2DForm()
    {
        // API ile ödeme yapmaya çalış dönen mesajı direk validation a form olarak gönder
        $fields = $this->_generateFormFields(false, false);

        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $this->response = $this->makeRequest("/ccpayment/api/paySmart2D", $fields);

        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            $returnForm = (array) $this->response + (array) $this->response->data;
            unset($returnForm['data']);

            $redirecti = $this->return_url;
            if (strstr($this->return_url, '?'))
                $redirecti .= '&';
            else
                $redirecti .= '?';
            $redirecti .= http_build_query($returnForm);

            return [
                "status" => "success",
                "message" => "",
                "data" => $this->response->data,
                "redirect" => $redirecti,
                "form2" => [
                    "url" => $this->return_url,
                    "inputs" => $returnForm,
                ],
            ];
        } else {
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : '2D Error',
            ];
        }
    }



    private function _generateFormFields($useCard = false, $encodeItems = true, $isCommissionFromUser = 0)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $items = json_encode($this->items);
        if ($this->debug) {
            echo "<hr>";
            var_dump($items);
        }
        $items = urlencode($items);

        if ($this->debug) {
            echo "<hr>";
            var_dump($items);
        }


        $invoice_id = $this->paymentid;
        $currency_code = isset($this->order["currency"]) ? $this->order["currency"] : "TRY"; 

        // Item'ların toplamını hesapla
        $items_total = 0;
        $cart = [];
        if(is_array($this->items)){
            foreach ($this->items as $item) {
                $cartItem = [
                    'name' => $item["product_name"],
                    'price' => floatval($item["product_price"]),
                    'quantity' => intval($item["product_quantity"]),
                    'description' => "",
                ];
    
                $cart[] = $cartItem;
                $productPrice = $cartItem['price'] * $cartItem['quantity'];
                $items_total = $items_total + $productPrice;
                if (isset($item["recurring"]))
                    $recurring = $item["recurring"];
            }
        }

        // Taksit komisyonu varsa ekle
        $installment_fee = 0;
        if (isset($this->order["total"]) && isset($this->order["sub_total"]) && $this->order["total"] != $this->order["sub_total"]) {
            $installment_fee = abs($this->order["total"] - $this->order["sub_total"]);
            $cartItem = [
                'name' => "Taksit Komisyonu",
                'price' => number_format($installment_fee, 2, '.', ''),
                'quantity' => 1,
                'description' => "",
            ];
            $cart[] = $cartItem;
            $items_total = $items_total + $installment_fee;
        }

        $item_js = isset($cart) ? $cart : [];
        if ($encodeItems)
            $item_js = json_encode($item_js);

        $name = isset($this->customer["firstname"]) ? $this->customer["firstname"] : "";
        $surname = isset($this->customer["lastname"]) ? $this->customer["lastname"] : "";
        
        // Taksit sayısı kontrolü - 0 veya boş ise 1 yap (peşin)
        $installment = isset($this->order["installment"]) ? trim($this->order["installment"]) : '1';
        
        // Taksit sayısı 0, boş veya geçersiz ise 1 yap
        if ($installment == '' || $installment == '0' || $installment == 0 || !is_numeric($installment) || intval($installment) < 1) {
            $installment = '1';
        }
        
        // Tamsayıya çevir
        $installment = intval($installment);

        // Total'ı belirle: API item'ların toplamını kontrol ediyor, bu yüzden total'ı item'ların toplamına eşitle
        // Eğer order total ile item total farklıysa, farkı bir item olarak ekle
        $order_total = isset($this->order["total"]) ? floatval($this->order["total"]) : $items_total;
        
        // Fark varsa, farkı bir item olarak ekle (indirim veya ek ücret)
        if (abs($items_total - $order_total) > 0.01) {
            $difference = $order_total - $items_total;
            $adjustmentItem = [
                'name' => $difference > 0 ? "Ek Ücret" : "İndirim",
                'price' => number_format($difference, 2, '.', ''),
                'quantity' => 1,
                'description' => "",
            ];
            $cart[] = $adjustmentItem;
            $items_total = $items_total + $difference;
            $item_js = $encodeItems ? json_encode($cart) : $cart;
        }
        
        // Total'ı item'ların toplamına eşitle (API item'ların toplamını kontrol ediyor)
        $total = $items_total;
        
        $hash_key = $this->generateHashKey(
            [
                $total,
                $installment,
                $currency_code,
                $this->merchant_key,
                $invoice_id,
            ],
            $this->app_secret
        );
        
        $invoice = [
            'merchant_key' => $this->merchant_key,
            'invoice_id' => $invoice_id,
            'total' => $total,
            'items' => $item_js,
            'currency_code' => $currency_code,
            // 'installments_number' => $installment, // BURASI SILINDI
            'cancel_url' => $this->cancel_url,
            'return_url' => $this->return_url,
            'hash_key' => $hash_key,
            'name' => $name,
            'surname' => $surname,
            'is_comission_from_user' => $isCommissionFromUser
        ];

        // Taksit sayısını her zaman gönder (API her zaman bekliyor)
        $invoice['installments_number'] = $installment;

        if (!$useCard) {
            $cardParams = [
                'cc_holder_name' => isset($this->card["owner"]) ? $this->card["owner"] : "",
                'cc_no' => isset($this->card["pan"]) ? $this->card["pan"] : "",
                'expiry_month' => isset($this->card["month"]) ? $this->card["month"] : "",
                'expiry_year' => isset($this->card["year"]) ? $this->card["year"] : "",
                'cvv' => isset($this->card["cvc"]) ? $this->card["cvc"] : "",
            ];
            $invoice = array_merge($invoice, $cardParams);
        } else {
            $cardParams = [
                "card_token" => $useCard,
                "customer_number" => isset($this->customer["id"]) ? $this->customer["id"] : "",
                "customer_email" => isset($this->customer["email"]) ? $this->customer["email"] : "",
                "customer_phone" => isset($this->customer["phone"]) ? $this->customer["phone"] : "",
                "customer_name" => isset($this->customer["name"]) ? $this->customer["name"] : "",
            ];
            $invoice = array_merge($invoice, $cardParams);
        }

        // DÜZELTME 2: (8 Satırlık Hatanın Çözümü) Billing verisi boşsa hata vermemesi için kontrol eklendi.
        if (!is_array($this->billing)) {
            $this->billing = [
                "address1" => "",
                "address2" => "",
                "city" => "",
                "postcode" => "",
                "state" => "",
                "country" => "",
                "phone" => "",
                "email" => ""
            ];
        }

        $invoice['bill_address1'] = isset($this->billing["address1"]) ? $this->billing["address1"] : "";
        $invoice['bill_address2'] = isset($this->billing["address2"]) ? $this->billing["address2"] : "";
        $invoice['bill_city'] = isset($this->billing["city"]) ? $this->billing["city"] : "";
        $invoice['bill_postcode'] = isset($this->billing["postcode"]) ? $this->billing["postcode"] : "";
        $invoice['bill_state'] = isset($this->billing["state"]) ? $this->billing["state"] : "";
        $invoice['bill_country'] = isset($this->billing["country"]) ? $this->billing["country"] : "";
        $invoice['bill_phone'] = isset($this->billing["phone"]) ? $this->billing["phone"] : "";
        $invoice['bill_email'] = isset($this->billing["email"]) ? $this->billing["email"] : "";

        if (isset($this->order["discount"]) and $this->order["discount"] > 0) {
            $invoice['discount'] = $this->order["discount"];
            $invoice['coupon'] = 'COUPON';
        }

        if (isset($this->order["transaction_type"]) and $this->order["transaction_type"] != "")
            $invoice["transaction_type"] = $this->order["transaction_type"];

        if (isset($this->order["recurring"]) and $this->order["recurring"] == 1) {
            $invoice["order_type"] = "1";
            $invoice["recurring_payment_number"] = $this->order["recurring_payment_number"];
            $invoice["recurring_payment_cycle"] = $this->order["recurring_payment_cycle"];
            $invoice["recurring_payment_interval"] = $this->order["recurring_payment_interval"];
            $invoice["recurring_web_hook_key"] = $this->recurring_web_hook_key;
        } else {
            $invoice['sale_web_hook_key'] = $this->sale_web_hook_key;
        }
        $invoice['response_method'] = 'POST';

        return $invoice;
    }

    public function generatePaymentLink()
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }


        $items = json_encode($this->items);
        if ($this->debug) {
            echo "<hr>";
            var_dump($items);
        }
        $items = urlencode($items);


        if ($this->debug) {
            echo "<hr>";
            var_dump($items);
        }


        $invoice_id = $this->paymentid;
        $currency_code = $this->order["currency"]; //Merchant currency code e.g(TRY,USD,EUR)

        $total = 0;
        if(is_array($this->items)){
            foreach ($this->items as $item) {
                $cartItem = [
                    'name' => $item["product_name"],
                    'price' => $item["product_price"],
                    'quantity' => $item["product_quantity"],
                    'description' => "",
                ];
                $cart[] = $cartItem;
                $productPrice = $cartItem['price'] * $cartItem['quantity'];
                $total = $total + $productPrice;
                if (isset($item["recurring"]))
                    $recurring = $item["recurring"];
            }
        }
        
        $item_js = isset($cart) ? $cart : [];
        $name = isset($this->customer["firstname"]) ? $this->customer["firstname"] : "";
        $surname = isset($this->customer["lastname"]) ? $this->customer["lastname"] : "";
        $sale_web_hook = isset($this->order["key"]) ? $this->order["key"] : ""; 
        
        // Taksit sayısı kontrolü - 0 veya boş ise 1 yap (peşin)
        $installment = isset($this->order["installment"]) ? trim($this->order["installment"]) : '1';
        
        // Taksit sayısı 0, boş veya geçersiz ise 1 yap
        if ($installment == '' || $installment == '0' || $installment == 0 || !is_numeric($installment) || intval($installment) < 1) {
            $installment = '1';
        }
        
        // Tamsayıya çevir
        $installment = intval($installment);

        if (isset($this->order['discount']) and $this->order['discount'] > 0) {
            $total -= $this->order['discount'];
        }

        $hash_key = $this->generateHashKey(
            [
                $total,
                $installment,
                $currency_code,
                $this->merchant_key,
                $invoice_id,
            ],

            $this->app_secret
        );

        $invoice = [
            'invoice_description' => 'asd',
            'invoice_id' => $invoice_id,
            'total' => $total,
            'items' => ($item_js),

            'max_installment' => $installment,
            'installments_number' => $installment, // Payment Link için de ekle
            'cancel_url' => $this->cancel_url,
            'return_url' => $this->return_url,
            //'hash_key' => $hash_key,

        ];
        
        // Billing kontrolü
        if (!is_array($this->billing)) {
            $this->billing = [
                "address1" => "", "address2" => "", "city" => "",
                "postcode" => "", "state" => "", "country" => "", "phone" => "", "email" => ""
            ];
        }

        //billing info
        $invoice['bill_address1'] = isset($this->billing["address1"]) ? $this->billing["address1"] : "";
        $invoice['bill_address2'] = isset($this->billing["address2"]) ? $this->billing["address2"] : "";
        $invoice['bill_city'] = isset($this->billing["city"]) ? $this->billing["city"] : "";
        $invoice['bill_postcode'] = isset($this->billing["postcode"]) ? $this->billing["postcode"] : "";
        $invoice['bill_state'] = isset($this->billing["state"]) ? $this->billing["state"] : "";
        $invoice['bill_country'] = isset($this->billing["country"]) ? $this->billing["country"] : "";
        $invoice['bill_phone'] = isset($this->billing["phone"]) ? $this->billing["phone"] : "";
        $invoice['bill_email'] = isset($this->billing["email"]) ? $this->billing["email"] : "";

        if (isset($this->order["discount"]) and $this->order["discount"] > 0) {
            $invoice['discount'] = $this->order["discount"];
            $invoice['coupon'] = 'COUPON';
        }

        if (isset($this->order["transaction_type"]) and $this->order["transaction_type"] != "")
            $invoice["transaction_type"] = $this->order["transaction_type"];

        if (isset($this->order["recurring"]) and $this->order["recurring"] == 1) {
            $invoice["order_type"] = "1";
            $invoice["recurring_payment_number"] = $this->order["recurring_payment_number"];
            $invoice["recurring_payment_cycle"] = $this->order["recurring_payment_cycle"];
            $invoice["recurring_payment_interval"] = $this->order["recurring_payment_interval"];
            $invoice["recurring_web_hook_key"] = $this->recurring_web_hook_key;
        } else {
            $invoice['sale_web_hook_key'] = $this->sale_web_hook_key;
        }
        $invoice['response_method'] = 'POST';

        $postdata = [
            'merchant_key' => $this->merchant_key,
            'invoice' => json_encode($invoice),
            'currency_code' =>  $currency_code,
            'name' => $name,
            'surname' => $surname,
        ];
        $this->response = $this->makeRequest("/ccpayment/purchase/link", $postdata);

        if (isset($this->response->status_code) && $this->response->status_code == 100) {
            return [
                "status" => "success",
                "redirect" => $this->response->link,
                "inputs" => $invoice,
            ];
        } else {
            return [
                "status" => "error",
                "message" => isset($this->response->success_message) ?  $this->response->success_message :  (isset($this->response->status_description) ? $this->response->status_description : 'Link Error'),
            ];
        }
    }

    public function getStoredCards($customerNumber)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if (!is_array($status) || !isset($status["status"]) || $status["status"] != "success") {
                $error_message = isset($status["message"]) ? $status["message"] : "Token alınamadı";
                return [
                    "status" => "error",
                    "message" => $error_message,
                    "data" => [],
                    "cards" => []
                ];
            }
        }

        // Token'ı her zaman yeniden al (getCardTokens için güvenli olması için)
        $status = $this->getToken();
        if (!is_array($status) || !isset($status["status"]) || $status["status"] != "success") {
            $error_message = isset($status["message"]) ? $status["message"] : "Token alınamadı";
            return [
                "status" => "error",
                "message" => $error_message,
                "data" => [],
                "cards" => []
            ];
        }

        $requestParams = [
            'merchant_key' => $this->merchant_key,
            'customer_number' => $customerNumber,
        ];

        if (!$this->response = $this->makeRequest("/ccpayment/api/getCardTokens", $requestParams, 'GET')) {
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Error',
                "data" => [],
                "cards" => []
            ];
        }

        $cards = [];
        if (isset($this->response->status_code) and $this->response->status_code == 100) {
            if(isset($this->response->data) && is_array($this->response->data)){
                foreach ($this->response->data as $customerCard) {
                    $cards[$customerCard->card_token] = $customerCard;
                }
            }

            return [
                "status" => "success",
                "message" => "",
                "data"  => $this->response->data,
                "cards" => $cards
            ];
        } else {
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Error',
                "data" => [],
                "cards" => []
            ];
        }
    }

    public function updateStoredCard($card_token, $customer_number, $expiry_month, $expiry_year, $card_holder_name)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $requestParams = [
            'merchant_key' => $this->merchant_key,
            'card_token' => $card_token,
            'customer_number' => $customer_number,
            'expiry_month' => $expiry_month,
            'expiry_year' => $expiry_year,
            'hash_key' => $this->generateHashKey([$this->merchant_key, $customer_number, $card_token], $this->app_secret),
            'card_holder_name' => $card_holder_name,
        ];

        if (!$this->response = $this->makeRequest("/ccpayment/api/editCard", $requestParams, 'POST'))
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Error',
                "data" => [],
                "cards" => []
            ];

        $cards = [];
        if (isset($this->response->status_code) and $this->response->status_code == 100) {
            return [
                "status" => "success",
                "message" => "",
            ];
        } else {
            return [
                "status" => "error",
                "message" => $this->response->status_description,
            ];
        }
    }

    public function deleteStoredCard($card_token, $customer_number)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $requestParams = [
            'merchant_key' => $this->merchant_key,
            'card_token' => $card_token,
            'customer_number' => $customer_number,
            'hash_key' => $this->generateHashKey([$this->merchant_key, $customer_number, $card_token], $this->app_secret),
        ];

        if (!$this->response = $this->makeRequest("/ccpayment/api/deleteCard", $requestParams, 'POST'))
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Error',
                "data" => [],
                "cards" => []
            ];

        $cards = [];
        if (isset($this->response->status_code) and $this->response->status_code == 100) {

            if(isset($this->response->data) && is_array($this->response->data)){
                foreach ($this->response->data as $customerCard) {
                    $cards[$customerCard->card_token] = $customerCard;
                }
            }

            return [
                "status" => "success",
                "message" => "",
                "data"  => $this->response->data,
                "cards" => $cards
            ];
        } else {
            return [
                "status" => "error",
                "message" => $this->response->status_description,
            ];
        }
    }

    public function storeCard($customer_number, $card_holder, $card_number, $expiry_month, $expiry_year, $customer_name, $customer_phone)
    {
        if ($this->token == "") {
            $status = $this->getToken();
            if ($status["status"] != "success")
                die($status["message"]);
        }

        $requestParams = [
            'merchant_key' => $this->merchant_key,
            "card_holder_name" => $card_holder,
            "card_number" => $card_number,
            "expiry_month" => $expiry_month,
            "expiry_year" => $expiry_year,
            "customer_number" => $customer_number,
            "hash_key" => $this->generateHashKey([$this->merchant_key, $customer_number, $card_holder, $card_number, $expiry_month, $expiry_year], $this->app_secret),
            "customer_name" => $customer_name,
            "customer_phone" => $customer_phone,
        ];

        if (!$this->response = $this->makeRequest("/ccpayment/api/saveCard", $requestParams))
            return [
                "status" => "error",
                "message" => "Sistemsel bir hata oluştu",
            ];;

        if (isset($this->response->status_code) and $this->response->status_code == 100) {
            return [
                "status" => "success",
                "message" => "",
                "data" => $this->response->card_token
            ];
        } else {
            return [
                "status" => "error",
                "message" => isset($this->response->status_description) ? $this->response->status_description : 'Save Card Error',
            ];
        }
    }

    public static function validateHashKey($hashKey, $secretKey)
    {
        $status = $currencyCode = "";
        $total = $invoiceId = $orderId = 0;

        if (!empty($hashKey)) {
            $hashKey = str_replace('_', '/', $hashKey);
            $password = sha1($secretKey);

            $components = explode(':', $hashKey);
            if (count($components) > 2) {
                $iv = isset($components[0]) ? $components[0] : "";
                $salt = isset($components[1]) ? $components[1] : "";
                $salt = hash('sha256', $password . $salt);
                $encryptedMsg = isset($components[2]) ? $components[2] : "";

                $decryptedMsg = openssl_decrypt($encryptedMsg, 'aes-256-cbc', $salt, null, $iv);

                if ($decryptedMsg && strpos($decryptedMsg, '|') !== false) {
                    $array = explode('|', $decryptedMsg);
                    $status = isset($array[0]) ? $array[0] : 0;
                    $total = isset($array[1]) ? $array[1] : 0;
                    $invoiceId = isset($array[2]) ? $array[2] : '0';
                    $orderId = isset($array[3]) ? $array[3] : 0;
                    $currencyCode = isset($array[4]) ? $array[4] : '';
                }
            }
        }

        return [$status, $total, $invoiceId, $orderId, $currencyCode];
    }
}

if (!function_exists('dump')) {
    function dump($data)
    {
        echo '<pre>' . var_export($data, true) . '</pre>';
        /* highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>"); */
    }
}

if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}