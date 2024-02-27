<?php /** @noinspection PhpUnused */

namespace App\OTP;

class Secret
{

    private Base32 $base32;

    public function __construct(private $complementSecret = "x23-defend")
    {
        $this->base32 = new Base32();
    }

    /**
     * Generates a 16 digit secret key in base32 format
     * @param string|null $userID
     * @return string
     */
    public function generateSecretKey(string $userID = null): string
    {
        $userID = $this->uuid($userID);
        return str_replace("=", "", $this->base32->encode($userID . $this->complementSecret));
    }

    /**
     * Generates a 16 digit secret key in base32 format
     * @param int $length
     * @return string
     */
    public function randomKey(int $length = 16): string
    {
        $b32 = "234567QWERTYUIOPASDFGHJKLZXCVBNM";
        $s = "";
        for ($i = 0; $i < $length; $i++)
            $s .= $b32[rand(0, 31)];
        return $s;
    }

    /**
     * @param string $input
     * @return string
     */
    public function uuid(string $input = ""): string
    {
        return self::uuidV5(self::sha1($input), $input);
    }

    /**
     * @param $string
     * @return string
     */
    public function sha1($string): string
    {
        return sha1($string);
    }

    /**
     * @param $name_space
     * @param $string
     * @return string
     */
    private function uuidV5($name_space, $string): string
    {
        $n_hex = str_replace(array('-', '{', '}'), '', $name_space); // Getting hexadecimal components of namespace
        $binaryStr = ''; // Binary value string
        //Namespace UUID to bits conversion
        for ($i = 0; $i < strlen($n_hex); $i += 2) {
            $binaryStr .= chr(hexdec($n_hex[$i] . $n_hex[$i + 1]));
        }
        //hash value
        $hashing = sha1($binaryStr . $string . now() . rand(0, 99));

        return sprintf('%08s-%04s-%04x-%04x-%12s',
            // 32 bits for the time_low
            substr($hashing, 0, 8),
            // 16 bits for the time_mid
            substr($hashing, 8, 4),
            // 16 bits for the time_hi,
            (hexdec(substr($hashing, 12, 4)) & 0x0fff) | 0x5000,
            // 8 bits and 16 bits for the clk_seq_hi_res,
            // 8 bits for the clk_seq_low,
            (hexdec(substr($hashing, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for the node
            substr($hashing, 20, 12)
        );
    }
}
