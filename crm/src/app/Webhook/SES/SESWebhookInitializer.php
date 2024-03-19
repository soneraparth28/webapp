<?php


namespace App\Webhook\SES;


use App\Models\Email\EmailLog;
use App\Repositories\App\StatusRepository;
use App\Webhook\Traits\PrivacySettings;
use App\Webhook\Webhook;
use Illuminate\Http\Request;

class SESWebhookInitializer extends Webhook
{
    use PrivacySettings;

    public function initialize(Request $request)
    {
        $this->event = json_decode($request->getContent());
        $message_id = null;
        $message = json_decode(optional($this->event)->Message);

        if (isset($message->mail)) {
            $mail = (object)$message->mail;
            $message_id = $mail->messageId;
        }elseif (isset($this->event->mail) && isset(optional($this->event->mail)->messageId)) {
            $message_id = $this->event->mail->messageId;
        }

        if ($message_id) {
            $this->email = EmailLog::where('email_id', $message_id)
                ->first();
        }
        return $this;
    }

    public function whenItIsBounce()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailBounced()
        ];
    }

    public function whenItIsClick()
    {
        return [
            'click_count' => optional($this->settings())->track_clicks_in_your_campaigns ? $this->email->click_count + 1 : 0
        ];
    }

    public function whenItIsComplaint()
    {
        return [
            'is_marked_as_spam' => 1
        ];
    }

    public function whenItIsDelivery()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailDelivered()
        ];
    }

    public function whenItIsOpen()
    {
        return [
            'open_count' => optional($this->settings())->track_open_in_campaigns ? $this->email->open_count + 1 : 0
        ];
    }

    public function whenItIsReject()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailBounced()
        ];
    }

    public function whenItIsRenderingFailure()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailSoftBounced()
        ];
    }

    public function whenItIsRenderingSuccess()
    {

    }

    public function whenItIsSend()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailDelivered()
        ];
    }

    public function save()
    {
        if ((isset($this->event->mail) && isset($this->event->mail->messageId)) || optional($this->event)->Message) {
            $attribute = [];
            $event = optional($this->event)->eventType ? optional($this->event)->eventType : optional($this->event)->notificationType;

            $message = json_decode(optional($this->event)->Message);

            $event = $event ? $event : $message->eventType;

            $method = 'whenItIs'.str_replace(' ', '', $event);

            if (method_exists($this, $method)) {
                $attribute = $this->$method();
            }

            if ($this->email) {
                $this->email
                    ->update($attribute);
            }
            return [
                'status' => true,
                'message' => 'Success'
            ];
        }
        return true;
    }
}
