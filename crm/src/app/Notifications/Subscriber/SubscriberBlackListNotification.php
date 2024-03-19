<?php

namespace App\Notifications\Subscriber;

use App\Config\SetMailConfig;
use App\Mail\Tag\SubscriberTag;
use App\Models\Core\App\Brand\Brand;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriberBlackListNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $templates;

    protected $via;

    protected $brand;

    protected $auth;

    protected $databaseContent;

    protected $mailSubject;

    protected $mailSettings;

    public function __construct($templates, $via, Brand $brand)
    {
        $this->templates = $templates;
        $this->via = $via;
        $this->brand = $brand;
        $this->auth = auth()->user();
        if (in_array('mail', $via)) {
            $this->mailSettings = $brand->mailSettings();
        }

        $this->databaseContent = $this->template()->database()->parse([]);

        $this->mailSubject = $this->template()->mail()->parseSubject([]);
    }


    public function via($notifiable)
    {
        return $this->via;
    }

    public function toMail($notifiable)
    {
        $template = $this->template()->mail();

        (new SetMailConfig($this->mailSettings))
            ->clear()
            ->set();

        $content = $template->custom_content ?? $template->default_content;
        return (new MailMessage)
            ->view('notification.mail.subscriber.index', [
                'template' => strtr(
                    $content,
                    (new SubscriberTag($this->brand, $this->auth, $notifiable))->blackListed()
                )
            ])
            ->subject($this->mailSubject);
    }

    public function toDatabase($notifiable)
    {
        return [
            "message" => $this->databaseContent,
            "name" => $notifiable->name,
            "url" => route('tenant.subscribers-black-lists.lists', ['brand_dashboard' => $this->brand->id]),
            "notifier_id" => optional($this->auth)->id,
        ];

    }

    public function template()
    {
        return new NotificationTemplateHelper($this->templates);
    }
}
