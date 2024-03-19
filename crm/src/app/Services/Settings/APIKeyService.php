<?php


namespace App\Services\Settings;


use App\Models\Subscriber\API;
use App\Services\AppService;
use Illuminate\Validation\ValidationException;

class APIKeyService extends AppService
{

    public function __construct(API $API)
    {
        $this->model = $API;
    }

    public function getAPI($brand_id = null, $api_key = null)
    {
        return $this->model::query()
            ->where('key', $api_key ?? request()->get('api_key'))
            ->where('brand_id', $brand_id ?? brand()->id)
            ->first();
    }

    public function checkAPIKey($brand_id, $api_key)
    {
        $api = $this->getAPI($brand_id, $api_key);

        throw_if(!$api, ValidationException::withMessages([
            'api_key' => trans('default.invalid_api_key')
        ]));

        return $api;
    }
}