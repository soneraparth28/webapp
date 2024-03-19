<?php


namespace App\Webhook;


use App\Models\Email\EmailLog;

class Webhook
{
    /**
     * @var EmailLog $email
     */
    public $email;

    //sending message id from our side
    protected $event;


    public function setEvent($event): self
    {
        $this->event = $event;
        return $this;
    }
}
