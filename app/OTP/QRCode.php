<?php /** @noinspection PhpUnused */

namespace App\OTP;

class QRCode
{
    private string $data;

    /**
     * creating code with link metadata
     * @param $url
     */
    public function link($url): void
    {
        if (preg_match('/^http:\/\//', $url) || preg_match('/^https:\/\//', $url)) {
            $this->data = $url;
        } else {
            $this->data = "https://" . $url;
        }
    }

    /**
     * creating code with bookmark metadata
     * @param $title
     * @param $url
     */
    public function bookmark($title, $url): void
    {
        $this->data = "MEBKM:TITLE:" . $title . ";URL:" . $url . ";;";
    }

    /**
     * creating text qr code
     * @param $text
     */
    public function setText($text): void
    {
        $this->data = $text;
    }

    /**
     * creating text qr code
     * @param string $base32Key
     * @param string $name
     */
    public function otp(string $base32Key, string $name = "OTP Validation"): void
    {
        $this->data = "otpauth://totp/" . urlencode($name) . "?secret=" . $base32Key;
    }

    /**
     * Returns the uri for a specific secret for hotp method.
     * Can be encoded as an image for simple configuration in
     * Google Authenticator.
     *
     * @param string $base32Key
     * @param integer $initial_count the initial counter
     * @param string $name the name of the account / profile
     */
    public function hotp(string $base32Key, int $initial_count, string $name = "HOTP Validation"): void
    {
        $this->data = "otpauth://hotp/" . urlencode($name) . "?secret=" . $base32Key . "&counter=" . $initial_count;
    }

    /**
     * creating code with sms metadata
     * @param $phone
     * @param $text
     */
    public function sms($phone, $text): void
    {
        $this->data = "SMSTO:" . $phone . ":" . $text;
    }

    /**
     * creating code with phone
     * @param $phone
     */
    public function phone_number($phone): void
    {
        $this->data = "TEL:" . $phone;
    }

    /**
     * creating code with mecard metadata
     * @param $name
     * @param $address
     * @param $phone
     * @param $email
     */
    public function contact_info($name, $address, $phone, $email): void
    {
        $this->data = "MECARD:N:" . $name . ";ADR:" . $address . ";TEL:" . $phone . ";EMAIL:" . $email . ";;";
    }

    /**
     * creating code wth email metadata
     * @param $email
     * @param $subject
     * @param $message
     */
    public function email($email, $subject, $message): void
    {
        $this->data = "MATMSG:TO:" . $email . ";SUB:" . $subject . ";BODY:" . $message . ";;";
    }

    /**
     * creating code with geolocation metadata
     * @param $lat
     * @param $lon
     * @param $height
     */
    public function geo($lat, $lon, $height): void
    {
        $this->data = "GEO:" . $lat . "," . $lon . "," . $height;
    }

    /**
     * creating code with Wi-Fi configuration metadata
     * @param $type
     * @param $ssid
     * @param $pass
     */
    public function wifi($type, $ssid, $pass): void
    {
        $this->data = "WIFI:T:" . $type . ";S:" . $ssid . ";P:" . $pass . ";;";
    }

    /**
     * creating code with i-appli activating metadata
     * @param $adf
     * @param $cmd
     * @param $param
     */
    public function iappli($adf, $cmd, $param): void
    {
        $param_str = "";
        foreach ($param as $val) {
            $param_str .= "PARAM:" . $val["name"] . "," . $val["value"] . ";";
        }
        $this->data = "LAPL:ADFURL:" . $adf . ";CMD:" . $cmd . ";" . $param_str . ";";
    }

    /**
     * creating code with gif or jpg image, or smf or MFi of ToruCa files as content
     * @param $type
     * @param $size
     * @param $content
     */
    public function content($type, $size, $content): void
    {
        $this->data = "CNTS:TYPE:" . $type . ";LNG:" . $size . ";BODY:" . $content . ";;";
    }

    /**
     * getting image
     * @param int $size
     * @param string $EC_level
     * @param string $margin
     * @return bool|string
     */
    public function get_image(int $size = 150, string $EC_level = 'L', string $margin = '0'): bool|string
    {
        $ch = curl_init();
        $this->data = urlencode($this->data);
        curl_setopt($ch, CURLOPT_URL, 'https://chart.apis.google.com/chart');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'chs=' . $size . 'x' . $size . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $this->data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * getting link for image
     * @param int $size
     * @param string $EC_level
     * @param string $margin
     * @return string
     */
    public function getQRCodeLink(int $size = 150, string $EC_level = 'L', string $margin = '0'): string
    {
        $this->data = urlencode($this->data);
        return 'https://chart.apis.google.com/chart?chs=' . $size . 'x' . $size . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $this->data;
    }

    /**
     * forcing image download
     * @param $file
     */
    public function download_image($file): void
    {
        header('Content-Disposition: attachment; filename=QRcode.png');
        header('Content-Type: image/png');
        echo $file;
    }

    /**
     * save image to server
     * @param $file
     * @param string $path
     */
    public function save_image($file, string $path = "./QRcode.png"): void
    {
        file_put_contents($path, $file);
    }
}
