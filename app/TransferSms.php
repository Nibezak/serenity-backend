<?php


namespace App;


class TransferSms
{
    public function sendSMS($phone,$message){

        $data = array(
            "sender"=>'serenity',
            "recipients"=>'+25'.$phone,
            "message"=>$message
        ,);
        $url = "http://rslr.connectbind.com:8080/bulksms/bulksms";
        $data = http_build_query($data);
        $username="mtec-goldgate";
        $password="Admin@21";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($result) {
            return true;
        }else{
            return false;
        }

    }
}
