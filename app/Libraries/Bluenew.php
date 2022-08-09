<?php
namespace App\Libraries;

class Bluenew
{
    private $apiUrl = [
        '0' => 'https://ccore.newebpay.com/MPG/mpg_gateway',
        '1' => 'https://core.newebpay.com/MPG/mpg_gateway'
    ];
    private $version = 1.5;
    private $MerchantID;
    private $HashKey;
    private $HashIV;

    //
    function __construct(
        $env,
        $MerchantID,
        $HashKey,
        $HashIV
    )
    {
        $this->env = $env;
        $this->MerchantID = $MerchantID;
        $this->HashKey = $HashKey;
        $this->HashIV = $HashIV;
    }

    //
    public function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    /*HashKey AES 加解密 */
    public function create_mpg_aes_encrypt ($parameter = "" , $key = "", $iv = "")
    {
        $return_str = '';
        if (!empty($parameter)) {
            //將參數經過 URL ENCODED QUERY STRING
            $return_str = http_build_query($parameter);
        }

        return trim(bin2hex(openssl_encrypt($this->addpadding($return_str), 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
    }

    /*HashKey AES 解密 */
    public function create_aes_decrypt($parameter = "", $key = "", $iv = "")
    {
        return $this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv));
    }

    //
    public function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    /*HashIV SHA256 加密*/
    public function SHA256($key="", $tradeinfo="", $iv="")
    {
        $HashIV_Key = "HashKey=".$key."&".$tradeinfo."&HashIV=".$iv;
        return $HashIV_Key;
    }

    // call bluenew web
    private function CheckOut($TradeInfo="", $SHA256="")
    {
    	$szHtml ='<form name="newebpay" id="newebpay" method="post" action="'.$this->apiUrl[$this->env].'">';
    	$szHtml .='<input type="text" name="MerchantID" value="'.$this->MerchantID.'">';
    	$szHtml .='<input type="text" name="TradeInfo" value="'.$TradeInfo.'">';
    	$szHtml .='<input type="text" name="TradeSha" value="'.$SHA256.'">';
    	$szHtml .='<input type="text" name="Version"  value="'.$this->version.'">';
    	$szHtml .='</form>';

    	return $szHtml;
    }

    // 傳送交易
    public function sendPay($info = [], $url = [])
    {
        $info = array_merge($info, [
            'MerchantID' => $this->MerchantID,
            'RespondType' => 'JSON',
            'Version' => $this->version,
            'ReturnURL' => $url['returnURL'] ?? '',
            'NotifyURL' => $url['notifyURL'] ?? '',
            'CustomerURL' => $url['customerURL'] ?? '',
            'ClientBackURL' => $url['clientBackURL'] ?? ''
        ]);

        $TradeInfo = $this->create_mpg_aes_encrypt($info, $this->HashKey, $this->HashIV);
        $SHA256 = strtoupper(hash("sha256", $this->SHA256($this->HashKey, $TradeInfo, $this->HashIV)));
        return $this->CheckOut($TradeInfo, $SHA256);
    }

    // callback
    public function receiveData($data)
    {
        return json_decode($this->create_aes_decrypt($data, $this->HashKey, $this->HashIV), true);
    }
}
