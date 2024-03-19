<?php


namespace App\Config\Traits;


trait UpdateMailSettings
{
    public function setEmailName(array $data)
    {
        config()->set('mail.from.address', $data['from_email']);
        config()->set('mail.from.name', $data['from_name']);
    }

    public function setMailgun(array $data)
    {
        $this->setEmailName($data);
        config()->set('mail.driver', 'mailgun');
        config()->set('services.mailgun.domain', $data['domain_name']);
        config()->set('services.mailgun.secret', $data['api_key']);
    }

    public function setAmazonSes(array $data)
    {
        $this->setEmailName($data);
        config()->set('mail.driver', 'ses');
        config()->set('services.ses.key', $data['access_key_id']);
        config()->set('services.ses.secret', $data['secret_access_key']);
        config()->set('services.ses.region', $data['api_region']);
        config()->set('services.ses.configuration_set', isset($data['configuration_set']) ? $data['configuration_set'] : '');
    }
    public function setSmtp(array $settings)
    {
        $this->setEmailName($settings);
        config()->set('mail.driver', 'smtp');
        config()->set('mail.default', 'smtp');
        config()->set('mail.host', $settings['smtp_host']);
        config()->set('mail.port', $settings['smtp_port']);
        config()->set('mail.encryption', $settings['smtp_encryption']);
        config()->set('mail.username', $settings['smtp_user_name']);
        config()->set('mail.password', $settings['smtp_password']);
    }
}
