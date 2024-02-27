<?php /** @noinspection PhpUnusedLocalVariableInspection */

/** @noinspection PhpUnused */

namespace App\OTP;

use Exception;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP Google two-factor authentication module.
 *
 * See https://www.idontplaydarts.com/2011/07/google-totp-two-factor-authentication-for-php/
 * for more details
 *
 * https://github.com/lelag/otphp
 *
 **/
class OTP
{

    /**
     * @param string $secret
     * The base32 encoded secret key
     * @param string $digest
     * the algorithm used for the hmac hash function
     * Google Authenticator only support sha1. Defaults to sha1
     * The algorithm used for the hmac hash function
     * @param int $digits
     * the number of digits in the one time password
     * Currently Google Authenticator only support 6. Defaults to 6.
     * The number of digits in the one-time password
     *
     */
    public function __construct(private readonly string $secret,
                                private readonly int    $digits = 6,
                                private readonly string $digest = 'sha1')
    {
    }

    /**
     * Generate a one-time password
     *
     * @param $input : number used to seed the hmac hash function.
     * This number is usually a counter (HOTP) or calculated based on the current
     * timestamp (see TOTP class).
     * Decodes a base32 string into a binary string.
     * @return string
     * @throws Exception
     */
    public function generateOTP($input): string
    {
        return $this->oath_otp($this->base32_decode($this->secret), $input);
    }

    /**
     * Takes the secret key and the timestamp and returns the one time
     * password.
     *
     * @param $key - Secret key in binary form.
     * @param $counter - Timestamp as returned by get_timestamp.
     * @return string
     *
     * @throws Exception
     */
    public function oath_otp($key, $counter): string
    {
        if (strlen($key) < 8)
            throw new Exception('Secret key is too short. Must be at least 16 base 32 characters');

        $bin_counter = pack('N*', 0) . pack('N*', $counter);        // Counter must be 64-bit int
        $hash = hash_hmac($this->digest, $bin_counter, $key, true);

        $d = $this->oath_truncate($this->digits, $hash);
        return str_pad($d, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * Decodes a base32 string into a binary string.
     * @param string $b32
     * @return string
     * @throws Exception
     */
    public function base32_decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        if (!preg_match('/^[ABCDEFGHIJKLMNOPQRSTUVWXYZ234567]+$/', $b32, $match))
            throw new Exception('Invalid characters in the base32 string.');

        $l = strlen($b32);
        $n = 0;
        $j = 0;
        $binary = "";
        $lut = $this->lut();

        for ($i = 0; $i < $l; $i++) {
            $n = $n << 5;                // Move buffer left by 5 to make room
            $n = $n + $lut[$b32[$i]];    // Add value into buffer
            $j = $j + 5;                // Keep track of number of bits in buffer

            if ($j >= 8) {
                $j = $j - 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }

        return $binary;
    }

    /**
     * @return int[]
     */
    private function lut(): array
    {   // Lookup needed for Base32 encoding
        return [
            "A" => 0, "B" => 1,
            "C" => 2, "D" => 3,
            "E" => 4, "F" => 5,
            "G" => 6, "H" => 7,
            "I" => 8, "J" => 9,
            "K" => 10, "L" => 11,
            "M" => 12, "N" => 13,
            "O" => 14, "P" => 15,
            "Q" => 16, "R" => 17,
            "S" => 18, "T" => 19,
            "U" => 20, "V" => 21,
            "W" => 22, "X" => 23,
            "Y" => 24, "Z" => 25,
            "2" => 26, "3" => 27,
            "4" => 28, "5" => 29,
            "6" => 30, "7" => 31
        ];
    }

    /**
     * Extracts the OTP from the SHA1 hash.
     * @param string $hash
     * @param $otpLength
     * @return integer
     */
    private function oath_truncate($otpLength, string $hash): int
    {
        $offset = ord($hash[19]) & 0xf;
        return (
                ((ord($hash[$offset]) & 0x7f) << 24) |
                ((ord($hash[$offset + 1]) & 0xff) << 16) |
                ((ord($hash[$offset + 2]) & 0xff) << 8) |
                (ord($hash[$offset + 3]) & 0xff)
            ) % pow(10, $otpLength);
    }

    /**
     * Verifys a user inputted key against the current timestamp. Checks $window
     * keys either side of the timestamp.
     *
     * @param $otp
     * @param $input
     * @return boolean
     *
     * @throws Exception
     */
    public function verifyOTP($otp, $input): bool
    {
        return self::generateOTP($input) === $otp;
    }

}
