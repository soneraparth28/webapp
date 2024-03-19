<?php

namespace App\Http\Requests\Core\Brand;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Traits\MailRules;
use Illuminate\Support\Str;

class BrandSettingRequest extends BaseRequest
{
    use MailRules;
    /*
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $supported_mail_services = array_keys(config('settings.supported_mail_services'));
        $context_rule = [
            'delivery.context' => "required|in:" . implode(',', $supported_mail_services)
        ];

        if ($this->has('delivery.context')) {
            foreach ($supported_mail_services as $provider) {
                if ($provider == $this->delivery['context'])
                    return array_merge($context_rule, $this->providerRules());
            }
        }
        return $context_rule;
    }

    private function providerRules()
    {
        $context = Str::camel($this->delivery['context']) . 'Rules';

        return collect(array_keys($this->{$context}()))->reduce(function ($carry, $key)use($context) {
            $carry['delivery.'.$key] = $this->{$context}()[$key];
            return $carry;
        }, []);
    }
}
