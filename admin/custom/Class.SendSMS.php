<?php

class SendSMS{
    const API_KEY = 'ec3a7e46';
    const API_SECRET = '9gaZrfpD9Qi7Xyfs';
    private $telephone;
    private $text;
    private $cc_list;

    /**
     * @param $telephone : 14380001111
     * @param $text : content of message
     */
    public function __construct($telephone, $text){
    //    $telephone = '15145497660';
        $this->telephone = $telephone;
        $this->text = $text;
        $this->cc_list = array();
    }

    /**
     * @param $cc_tele_list : a list of telephone
     */
    public function cc_recipient($cc_tele_list){
        if (is_array($cc_tele_list)) {
            foreach ($cc_tele_list as $one) {
                array_push($this->cc_list, $one);
            }
        }
    }


    public function sendSMS(){
        $To = $this->telephone;
        $Message = $this->text;
        $this->sendOneSMS($To, $Message);

        if (sizeof($this->cc_list) > 0) {
            for ($i = 0; $i < sizeof($this->cc_list); $i++) {
                sleep(1);
                $To = $this->cc_list[$i];
                $this->sendOneSMS($To, $Message);
            }
        }
    }

    private function sendOneSMS($To, $Message){
        try {
            $DEBUG = true;
            $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                    [
                        'api_key' => self::API_KEY,
                        'api_secret' => self::API_SECRET,
                        'to' => $To,
                        'from' => '12046500678',
                        'text' => $Message
                    ]
                );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            if ($DEBUG) {
                $response = json_decode($response, true);
                $SentNumber = $response["message-count"];
                $NumberTo = $response['messages']['0']['to'];
                $StatusMessage = $response['messages']['0']['status'];
                $Error = isset($response['messages']['0']['error-text']) ? $response['messages']['0']['error-text'] : 'none';
                $Network = $response['messages']['0']['network'];
                $MessageID = $response['messages']['0']['message-id'];
                $Balance = $response['messages']['0']['remaining-balance'];
                $Price = $response['messages']['0']['message-price'];
                echo "<hr>You sent $SentNumber Messages <br>";
                echo "You sent Messages To: $NumberTo<br>";
                echo "Messages Status: $StatusMessage<br>";
                echo "Messages Error: $Error<br>";
                echo "Messages Network: $Network<br>";
                echo "Your Balance for $MessageID: $Balance  Price: $Price<br>";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
