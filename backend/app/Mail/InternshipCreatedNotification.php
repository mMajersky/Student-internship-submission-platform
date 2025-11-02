<?php

namespace App\Mail;

class InternshipCreatedNotification extends BaseMail
{
    public $view = 'emails.internship_created';
    public $subjectLine = 'Nová žiadosť o stáž z platformy SISP';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}
