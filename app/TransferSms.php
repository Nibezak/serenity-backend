<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransferSms
{
    protected $host       = null;
    protected $segment    = null;
    protected $username   = null;
    protected $password   = null;
    protected $type       = null;
    protected $dlr        = null;
    protected $sender     = null;

    public function __construct() {
        $this->host       = 'http://rslr.connectbind.com:8080';
        $this->segment    = '/bulksms/bulksms';
        $this->username   = 'mtec-goldgate';
        $this->password   = 'Admin@21';
        $this->type       = '0';
        $this->dlr        = '1';
        $this->sender     = 'Serenity';
    }

    public function sendSMS($phone, $message)
    {
        $response = Http::get($this->host.$this->segment, [
            "username"      => $this->username, 
            "password"      => $this->password, 
            "type"          => $this->type, 
            "dlr"           => $this->dlr, 
            "source"        => $this->sender, 
            "destination"   => '+25'.$phone,
            "message"       => $message,
        ])->body();

        Log::info($response);

        return true;
    }
}
