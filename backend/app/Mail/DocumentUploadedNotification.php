<?php

namespace App\Mail;

class DocumentUploadedNotification extends BaseMail
{
    public $view = 'emails.document_uploaded';
    public $subjectLine = 'Nový dokument na validáciu - SISP';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}

