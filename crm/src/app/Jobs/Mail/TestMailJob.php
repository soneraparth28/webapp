<?php

namespace App\Jobs\Mail;

use App\Config\SetMailConfig;
use App\Mail\TestMail;
use App\Models\Core\App\Brand\Brand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $subject;
    private $message;
    protected $mailSettings = [];


    public function __construct($email, $subject, $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        if (request()->has('brand_id')) {
            $this->mailSettings = Brand::find(request()->get('brand_id'))->mailSettings();
        }
    }


    public function handle()
    {
        Mail::to($this->email)
            ->send(new TestMail($this->subject, $this->message));
    }

    private function setConfig() : array
    {
        (new SetMailConfig($this->mailSettings))
            ->clear()
            ->set();

        return $this->mailSettings;
    }
    public function middleware() : array
    {
        $this->setConfig();

        return [];
    }
}
