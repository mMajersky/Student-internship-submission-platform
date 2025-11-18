<?php

namespace App\Mail;

class InternshipCreatedForGarant extends BaseMail
{
    public $view = 'emails.internship_created_for_garant';
    public $subjectLine = 'Nová prax v systéme - SISP';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}

