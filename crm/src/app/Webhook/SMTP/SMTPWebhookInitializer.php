<?php


namespace App\Webhook\SMTP;


use App\Exceptions\GeneralException;
use App\Models\Email\EmailLog;
use App\Webhook\Traits\PrivacySettings;
use App\Webhook\Webhook;
use Gainhq\Installer\App\Managers\StorageManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SMTPWebhookInitializer extends Webhook
{
    use PrivacySettings;

    private $redirect_to;

    public function whenItIsClicked()
    {
        if (optional($this->settings())->track_clicks_in_your_campaigns) {
            $this->email->click_count = 1;
            $this->email->save();
        }

        $isValidUrl = filter_var($this->redirect_to, FILTER_VALIDATE_URL);

        return redirect($isValidUrl ? $this->redirect_to: url('/'));
    }

    public function whenItIsOpened(): BinaryFileResponse
    {

        if (optional($this->settings())->track_open_in_campaigns) {
            $this->email->open_count = 1;
            $this->email->save();
        }

        return response()->file(
            $this->public_path(config('settings.smtp-tracker-thumbnail'))
        );
    }

    public function public_path($path = '/'): string
    {
        if ($this->basePathContainsSrc()) {
            $coreBasePath = str_replace('src', '', base_path());
            $realPath = implode(
                DIRECTORY_SEPARATOR,
                array_filter(explode(DIRECTORY_SEPARATOR, $coreBasePath .  $path))
            );
            return DIRECTORY_SEPARATOR . $realPath;
        }
        return public_path($path);
    }

    private function basePathContainsSrc(): bool
    {
        $exploded_path = explode('/', base_path());
        return end($exploded_path) === 'src';
    }

    public function init($tracker_id, $redirect_to = null): SMTPWebhookInitializer
    {
        throw_if(!in_array($this->event, ['clicked', 'opened']), new GeneralException);


        $this->email = EmailLog::query()
            ->where('tracker_id', $tracker_id)
            ->firstOrFail();


        $this->redirect_to = $redirect_to;

        return $this;
    }

    public function handle()
    {
        $method = "whenItIs" . ucfirst($this->event);

        return $this->$method();
    }


}