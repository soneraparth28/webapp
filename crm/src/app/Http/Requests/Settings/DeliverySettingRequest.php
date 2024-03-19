<?php

namespace App\Http\Requests\Settings;

use App\Helpers\Traits\BrandInactiveTrait;
use App\Http\Requests\Core\Setting\DeliverySettingRequest as CoreDeliverySettingRequest;

class DeliverySettingRequest extends CoreDeliverySettingRequest
{
    use BrandInactiveTrait;
    public function authorize()
    {
        return $this->actionIfInactive();
    }
}
