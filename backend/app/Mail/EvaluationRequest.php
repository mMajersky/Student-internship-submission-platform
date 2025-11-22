<?php

namespace App\Mail;

class EvaluationRequest extends BaseMail
{
    public $view = 'emails.evaluation_request';
    public $subjectLine = 'Žiadosť o výkaz o vykonanej odbornej praxi';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}

