<?php

namespace App\Mail;

class InternshipStatusChanged extends BaseMail
{
    public $view = 'emails.internship_status_changed';
    public $subjectLine = 'Zmena stavu praxe - SISP';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}

