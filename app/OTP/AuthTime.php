<?php /** @noinspection PhpUnused */

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
class AuthTime
{
    private string $secret;
    private Secret $secretGenerator;

    /**
     * @param int $otpLength
     * @param string $qrCodeNameKey
     */
    public function __construct(
        private readonly int    $otpLength = 6,
        private readonly string $qrCodeNameKey = "Test QuanticHeart"
    )
    {
        $this->secretGenerator = new Secret();
    }

    /**
     * @param int|null $userID
     * @return array
     */
    public function otp(int $userID = null): array
    {
        try {
            if ($userID !== null) $userID = $userID . "O";
            $this->secret = $this->secretGenerator->generateSecretKey($userID);
            $builder = new OTP($this->secret, $this->otpLength);
            $otp = $builder->generateOTP(1);
            $validation = $builder->verifyOTP($otp, 1);

            $qrCode = new QRCode();
            $qrCode->otp($this->secret, $this->qrCodeNameKey);

            return [
                "status" => true,
                "otp" => $otp,
                "valid" => $validation,
                "secret" => $this->secret,
                "length" => $this->otpLength,
                "time" => self::getTimestamp(),
                "linkQRCode" => $qrCode->getQRCodeLink(),
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    /**
     * @param $otpCode
     * @param $secret
     * @return array
     */
    public function otpVerify($otpCode, $secret): array
    {
        try {
            $builder = new OTP($secret, $this->otpLength);
            $validation = $builder->verifyOTP($otpCode, 1);
            return [
                "status" => true,
                "otp" => $otpCode,
                "valid" => $validation,
                "secret" => $secret,
                "length" => $this->otpLength,
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    /**
     * @param int|null $userID
     * @param int $count
     * @return array
     */
    public function hotp(int $userID = null, int $count = 1): array
    {
        try {
            if ($userID !== null) $userID = $userID . "H";
            $this->secret = $this->secretGenerator->generateSecretKey($userID);
            $builder = new HOTP($this->secret, $this->otpLength);
            $hotp = $builder->generateHOTP($count);
            $validation = $builder->verifyHOTP($hotp, $count);

            $qrCode = new QRCode();
            $qrCode->hotp($this->secret, $count, $this->qrCodeNameKey);

            return [
                "status" => true,
                "otp" => $hotp,
                "valid" => $validation,
                "base32" => $this->secret,
                "length" => $this->otpLength,
                "time" => self::getTimestamp(),
                "linkQRCode" => $qrCode->getQRCodeLink(),
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }


    public function hotpVerify($otp, $secret, $count): array
    {
        try {
            $builder = new HOTP($secret, $this->otpLength);
            $validation = $builder->verifyHOTP($otp, $count);
            return [
                "otp" => $otp,
                "valid" => $validation,
                "length" => $this->otpLength,
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    public function totp(int $userID = null, $keyRegeneration = 30): array
    {
        try {
            if ($userID !== null) $userID = $userID . "T";
            $this->secret = $this->secretGenerator->generateSecretKey($userID);
            $builder = new TOTP($this->secret, $this->otpLength);
            $timestamp = self::getTimestamp($keyRegeneration);
            $hotp = $builder->generateTOTP($timestamp);
            $validation = $builder->verifyOTP($hotp, $timestamp);

            $qrCode = new QRCode();
            $qrCode->otp($this->secret, $this->qrCodeNameKey);

            return [
                "status" => true,
                "otp" => $hotp,
                "isValid" => $validation,
                "base32" => $this->secret,
                "length" => $this->otpLength,
                "time" => $timestamp,
                "linkQRCode" => $qrCode->getQRCodeLink(),
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    public function totpVerify($otp, $secret, $quantidadeOfValidOtps = 2, $keyRegeneration = 30): array
    {
        try {
            $builder = new TOTP($secret, $this->otpLength);
            $validation = $builder->verifyTOTP($otp, $secret, self::getTimestamp($keyRegeneration), $quantidadeOfValidOtps);
            return [
                "otp" => $otp,
                "valid" => $validation,
                "length" => $this->otpLength,
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    /**
     * Returns the current Unix Timestamp devided by the keyRegeneration
     * period.
     * @param int $keyRegeneration
     * @return integer
     */
    private function getTimestamp(int $keyRegeneration = 0): int
    {
        if ($keyRegeneration === 0)
            return floor(microtime(true));
        else
            return floor(microtime(true) / $keyRegeneration);
    }
}
