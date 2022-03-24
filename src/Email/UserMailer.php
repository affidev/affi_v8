<?php
// App/Email/UserMailer.php

namespace App\Email;



use App\Entity\User;

class UserMailer
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;

  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  public function sendNewNotification(User $user)
  {
    $message = new \Swift_Message(
      'Bonjour',
      'Merci de vous êtes inscrit sur affichange.'
    );

    $message
        ->addTo($user->getEmail())
        ->addFrom('inscription@affichange.com')
    ;
    $this->mailer->send($message);
  }
}
