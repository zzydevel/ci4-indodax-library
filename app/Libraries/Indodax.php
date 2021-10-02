<?php
namespace App\Libraries;

class Indodax{
    private $signKey = "##indodax_secret_key##";
    private $apiKey = "##indodax_api_key##";
    // example amount
    public $amount;
    public $txid;
    // value should be stored in database for further request, increase number when ngentot again
    private $nonce ;
    private $ch;
    public $data;
    public function __construct()
    {
        $this->nonce = time();
    }
    public function run(){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, 'https://indodax.com/tapi');
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, "method=transHistory&nonce=".$this->nonce);
        
        $headers = array();
        $headers[] = 'Key: '. $this->apiKey;
        $headers[] = 'Sign: '. hash_hmac("sha512", "method=transHistory&nonce=".$this->nonce, $this->signKey);
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($this->ch);
        if (curl_errno($this->ch)) {
            echo 'Error:' . curl_error($this->ch);
        }
        curl_close($this->ch);
        
        $final_res = json_decode($result);

      
        if($final_res->success == 1){
            foreach($final_res->return->deposit->btc as $item){
                if($item->amount == $this->amount && $item->tx == $this->txid){
                    if($item->status == "success"){
                        $this->data = array(
                            "STATUS" => "PAID",
                            "BTC" => $item->btc,
                            "AMOUNT" => $item->amount,
                            "TX" => $item->tx,
                            "MESSAGE" => "SUCCESS"
                        );
                        break;
                    } else {
                        $this->data = array(
                            "STATUS" => strtoupper($item->status),
                            "BTC" => $item->btc,
                            "AMOUNT" => $item->amount,
                            "TX" => $item->tx,
                            "MESSAGE" => "PENDING"
                        );
                        break;
                    }
                } else {
                    $this->data = array(
                        "STATUS" => "UNPAID",
                        "AMOUNT" => 0,
                        "MESSAGE" => "TX / Hash ID Not Found"
                    );
                }
            }
        } else {
            print_r($result);
            exit;
        }
    }

    
}

