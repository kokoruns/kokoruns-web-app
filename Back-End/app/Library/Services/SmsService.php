<?php

namespace App\Library\Services;

use AfricasTalking\SDK\AfricasTalking;


class SmsService
{
  public function sendOTP($user, $otp)
  {
    $message = "Use this code as your OTP, " . $otp . ". It expires in 20 minutes.";
    $this->sendSMS($user->phone, $message);
  }

  public function sendSMS($phone, $message)
  {
    $AT = new AfricasTalking(env('AFT_USERNAME'), env('AFT_API_KEY'));
    $sms = $AT->sms();
    return $sms->send([
      'to'      => $phone,
      'message' => $message,
    ]);
  }
}
