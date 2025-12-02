<?php

namespace App\Mail;

class CompanyRejected extends BaseMail
{
    public $view = 'emails.company_rejected';

    public function __construct(
        public string $companyName,
        public string $contactPersonName,
        public string $rejectionReason
    ) {
        $this->subject = 'Company Registration Request - Update';
    }
}
