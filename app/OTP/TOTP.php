<?php

namespace App\OTP {

    use Exception;

    /**
     * TOTP - One time password generator
     *
     * The TOTP class allow for the generation
     * and verification of one-time password using
     * the TOTP specified algorithm.
     *
     * This class is meant to be compatible with
     * Google Authenticator
     *
     * This class was originally ported from the rotp
     * ruby library available at https://github.com/mdp/rotp
     */
    class TOTP extends OTP
    {

        /**
         *  Get the password for a specific timestamp value
         *
         * @param integer $timestamp the timestamp which is timecoded and
         *  used to seed the hmac hash function.
         * @return integer the One Time Password
         * @throws Exception
         */
        public function generateTOTP(int $timestamp): int
        {
            return $this->generateOTP($timestamp);
        }

        /**
         * Verifys a user inputted key against the current timestamp. Checks $window
         * keys either side of the timestamp.
         *
         * @param string $otp
         * @param $secret
         * @param $timeStamp
         * @param int $quantidadeOfValidOtps
         * @return boolean
         *
         * @throws Exception
         */
        public function verifyTOTP(string $otp, $secret, $timeStamp, int $quantidadeOfValidOtps = 2): bool
        {
            $binarySeed = self::base32_decode($secret);
            for ($ts = $timeStamp - $quantidadeOfValidOtps; $ts <= $timeStamp + $quantidadeOfValidOtps; $ts++)
                if (self::oath_otp($binarySeed, $ts) === $otp)
                    return true;

            return false;
        }


    }

}
