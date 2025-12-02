<?php

namespace App\Mail;

class CompanyCredentials extends BaseMail
{
    public $view = 'emails.company_credentials';

    public function __construct(
        public string $companyName,
        public string $contactPersonName,
        public string $username,
        public string $password
    ) {
        $this->subject = 'Your Company Account Credentials - Internship System';
    }
}
