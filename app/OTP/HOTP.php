<?php

namespace App\OTP {

    use Exception;

    /**
     * HOTP - One time password generator
     *
     * The HOTP class allow for the generation
     * and verification of one-time password using
     * the HOTP specified algorithm.
     *
     * This class is meant to be compatible with
     * Google Authenticator
     *
     * This class was originally ported from the rotp
     * ruby library available at https://github.com/mdp/rotp
     */
    class HOTP extends OTP
    {
        /**
         *  Get the password for a specific counter value
         * @param integer $count the counter which is used to
         *  seed the hmac hash function.
         * @return string the One Time Password
         * @throws Exception
         */
        public function generateHOTP(int $count): string
        {
            return $this->generateOTP($count);
        }

        /**
         * Verify if a password is valid for a specific counter value
         *
         * @param integer $otp the one-time password
         * @param integer $counter the counter value
         * @return  bool true if the counter is valid, false otherwise
         * @throws Exception
         */
        public function verifyHOTP(int $otp, int $counter): bool
        {
            return ($otp == $this->generateHOTP($counter));
        }
    }

}
