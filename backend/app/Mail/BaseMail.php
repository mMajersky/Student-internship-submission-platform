<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

abstract class BaseMail extends Mailable
{

    public $view;
    public $subjectLine;
    public $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->subjectLine)
            ->view($this->view)
            ->with($this->data);
    }
}
