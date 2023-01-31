<?php

class OneTouchRequest{
    private $production_url = "https://onetouch-api.astropay.com";
    private $sandbox_url = "https://onetouch-api-sandbox.astropay.com";

    private $deposit_init = "/merchant/v1/deposit/init";
    private $cashout_init_v1 = "/merchant/v1/cashout";
    private $cashout_init_v2 = "/merchant/v2/cashout/init";

    private $callback = "https://google.com";
    
    //Creds
    private $apikey = "eACwiw0Svs3AnbKC5oD5WeFs64bhkhXtMhTyYp3o2JfMZ7fQAXWSkFM15pNKFcyg";
    private $secret = "QJH7L3JS2BFNYEHD53QN3QH22G54YFC3";

    private $issandbox = true;
   
    public function __construct(){
        if ($this->issandbox){
            $this->deposit_init = "$this->sandbox_url$this->deposit_init";
            $this->cashout_init_v1 = "$this->sandbox_url$this->cashout_init_v1";
            $this->cashout_init_v2 = "$this->sandbox_url$this->cashout_init_v2";
        } else{
            $this->deposit_init = "$this->production_url$this->deposit_init";
            $this->cashout_init_v1 = "$this->production_url$this->cashout_init_v1";
            $this->cashout_init_v2 = "$this->production_url$this->cashout_init_v2";
        }
    }

    public function create_deposit($amount, $currency, $country, $internal_deposit_id, $internal_user_id, $pay_by){
        $data['amount'] = $amount;
        $data['currency'] = $currency;
        $data['country'] = $country;
        $data['merchant_deposit_id'] = uniqid('d-');
        $data['callback_url'] = $this->callback;
        $data['user'] = array(
            "merchant_user_id"=>$internal_user_id
        );
        $data['product'] = array(
            "mcc"=>"7995",
            "merchant_code"=>"tokens",
            "description"=>"online tokens"
        );
        $data['visual_info'] = array(
            "merchant_name"=>"SDK"
        );
        if ($pay_by){
            $data['payment_method_code'] = $pay_by;
        }

        $post_data = json_encode($data);
        $response = $this->send_curl($this->deposit_init, $post_data);
        $decoded_response = json_decode($response);

        return $decoded_response->url;
    }

    public function create_cashout_v1($amount, $currency, $country, $internal_cashout_id, $internal_user_id,  $phone){
        $data['amount'] = $amount;
        $data['currency'] = $currency;
        $data['country'] = $country;
        $data['merchant_cashout_id'] = $internal_cashout_id;
        $data['callback_url'] = $this->callback;
        $data['user'] = array(
            "merchant_user_id"=>$internal_user_id,
            "phone"=>$phone
        );

        $post_data = json_encode($data);
        $response = $this->send_curl($this->cashout_init_v1, $post_data);

        return $response;
    }

    public function send_curl($url, $array){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json', 
            'Merchant-Gateway-Api-Key:'.$this->apikey,
            'Signature:'.$this->signature_calculation($array)
        ));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    public function signature_calculation($data){
        $signature = hash_hmac('sha256', $data, $this->secret);
        return $signature;
    }


}

?>