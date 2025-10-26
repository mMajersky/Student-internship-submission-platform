<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable implements ShouldQueue

{
    use Queueable, SerializesModels;

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
