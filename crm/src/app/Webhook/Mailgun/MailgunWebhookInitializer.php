<?php


namespace App\Webhook\Mailgun;


use App\Models\Core\App\Brand\Brand;
use App\Models\Email\EmailLog;
use App\Repositories\App\StatusRepository;
use App\Webhook\Traits\PrivacySettings;
use App\Webhook\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MailgunWebhookInitializer extends Webhook
{
    use PrivacySettings;

    //Failure level: permanent/temporary
    protected $severity;

    //signature -> required to verify the webhook
    protected $signature;

    public function initialize(Request $request)
    {
        $this->event = (object)request()->get('event-data');
        $this->signature = (object)$request->get('signature');
        $this->email = EmailLog::where('email_id', $this->event->message['headers']['message-id'])->first();
        $this->severity = optional($this->event)->severity;
        return $this;
    }

    public function verify()
    {
        $brand = Brand::find(optional($this->email->campaign)->brand_id);
        $settings = optional($brand)->mailSettings();

        $signingKey = (is_array($settings) && isset($settings['webhook_key'])) ? $settings['webhook_key'] : '';

        $token = $this->signature->token;
        $timestamp = $this->signature->timestamp;
        $signature = $this->signature->signature;

        if (abs(time() - $timestamp) > 15) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $timestamp . $token, $signingKey), $signature);
    }

    public function logLocation()
    {
        if (optional($this->settings())->track_location_in_your_campaigns) {
            return [
                'location' => implode(', ', array_reverse($this->event->geolocation))
            ];
        }
        return [];
    }

    public function whenItIsClicked()
    {
        return [
            'click_count' => optional($this->settings())->track_clicks_in_your_campaigns ? $this->email->click_count + 1 : 0
        ];
    }

    public function whenItIsDelivered()
    {
        return [
            'status_id' => resolve(StatusRepository::class)
                ->emailDelivered()
        ];
    }

    public function whenItIsOpened()
    {
        return [
            'open_count' => optional($this->settings())->track_open_in_campaigns ? $this->email->open_count + 1 : 0
        ];
    }

    public function whenItIsFailed()
    {
        $method = $this->severity == 'permanent' ? 'emailBounced' : 'emailSoftBounced';
        return [
            'status_id' => resolve(StatusRepository::class)
                ->$method()
        ];
    }

    public function whenItIsComplained()
    {
        return [
            'is_marked_as_spam' => 1
        ];
    }

    public function save()
    {
        if (!$this->email) {
            return  true;
        }
        if (!$this->verify())
            return ['status' => false, 'message' => 'Webhook is not valid'];

        $event = $this->event->event;
        if ($event != 'unsubscribed') {
            $method = 'whenItIs'.Str::studly($event);
            $attributes = array_merge($this->logLocation(), $this->$method());
            return $this->email->update($attributes);
        }
    }

}
