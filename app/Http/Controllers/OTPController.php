<?php

namespace App\Http\Controllers;

use App\OTP\AuthTime;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OTPController
{

    /**
     *
     * {
     * "status": true,
     * "otp": "346373",
     * "valid": true,
     * "secret": "MZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ",
     * "length": 6,
     * "time": 1708898527,
     * "linkQRCode": "https://chart.apis.google.com/chart?chs=150x150&cht=qr&chld=L|0&chl=otpauth%3A%2F%2Ftotp%2FTest%2BQuanticHeart%3Fsecret%3DMZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ"
     * }
     * @return JsonResponse
     */
    function generateOTP(): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->otp();
        return response()->json($data);
    }

    /**
     *
     * {
     * "status": true,
     * "otp": "346373",
     * "valid": true,
     * "secret": "MZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ",
     * "length": 6
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    function validateOTP(Request $request): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->otpVerify($request->code, "MZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ");
        return response()->json($data);
    }

    function generateHOTP(): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->hotp("1");
        return response()->json($data);
    }

    function validateHOTP(Request $request): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->hotpVerify($request->code, "MZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ", 1);
        return response()->json($data);
    }

    function generateTOTP(): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->totp("1");
        return response()->json($data);
    }

    function validateTOTP(Request $request): JsonResponse
    {
        $authGen = new AuthTime();
        $data = $authGen->totpVerify($request->code, "MZTDEMRSGFTDKLLBG44TMLJVGY4TELJZGI3DCLJYMZTDKNBTMEZDEMJZG54DEMZNMRSWMZLOMQ");
        return response()->json($data);
    }
}
