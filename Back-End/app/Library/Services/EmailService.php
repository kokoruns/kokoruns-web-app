<?php

namespace App\Library\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailable;
use App\Mail\SendgridEmail;
use Mailgun\Mailgun;
use Illuminate\Support\Facades\Http;

class EmailService
{
  public function emailVerification($user, $token)
  {
    $data = '<h3>Verify your email address</h3>
                    <p>Your Kokoruns verification token code:<br>
                      <center> ' . $token . ' </center>
                    </p>';
    $this->send("Email Verification", $data, $user->email);
  }

  public function passwordReset($user, $token)
  {
    $data =  '<h3>Reset your password</h3>
    <p>Your Kokoruns password reset token code:<br>
      <center> ' . $token . ' </center>
    </p>';
    $this->send("Password Reset", $data, $user->email);
  }

  // public function send($subject, $body, $email)
  // {
  //   $message = Swift_Message::newInstance()
  //     ->setSubject($subject)
  //     ->setFrom(array(env('MAIL_USERNAME') => 'Kokoruns'))
  //     ->setTo(array($email))
  //     ->setContentType("text/html")
  //     ->setBody('<html>' . $body . '</html>', 'text/html');

  //   $transport = Swift_SmtpTransport::newInstance(env('MAIL_HOST'), 465, 'ssl')
  //     ->setUsername(env('MAIL_USERNAME'))
  //     ->setPassword(env('MAIL_PASSWORD'));

  //   $mailer = Swift_Mailer::newInstance($transport);

  //   return $mailer->send($message);
  // }

  public function send($subject, $body, $email)
  {
    $mgClient = Mailgun::create(env('MAILGUN_API_KEY'));
    $domain = "sandboxffc64e42ea1a48ea85515201ad29147d.mailgun.org";
    $params = array(
      'from'    => 'Kokoruns <support@sandboxffc64e42ea1a48ea85515201ad29147d.mailgun.org>',
      'to'      => $email,
      'subject' => $subject,
      'html'    => '<html>' . $body . '</html>',
    );
    # Make the call to the client.
    return $mgClient->messages()->send($domain, $params);
  }

  // public function send($subject, $body, $email)
  // {
  //   $emailData = [
  //     "from" => ["email" => 'support@kokoruns.com', "name" => 'P2Vest'],
  //     "personalizations" => [["to" => [["email"=>$email]], "subject" => $subject]],
  //     "content" => [["type" => "text/html", "value" => $body]]
  //   ];

  //   return Http::withHeaders([
  //     'Authorization' => 'Bearer ',
  //     'Content-type' => 'application/json'
  //   ])->post('https://api.sendgrid.com/v3/mail/send', $emailData);
  // }
}
