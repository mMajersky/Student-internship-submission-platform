<?php

namespace App\Mail;

class PasswordResetMail extends BaseMail
{
    public $view = 'emails.password_reset';
    public $subjectLine = 'Obnovenie hesla – SISP';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}
