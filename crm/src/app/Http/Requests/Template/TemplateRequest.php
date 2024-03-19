<?php

namespace App\Http\Requests\Template;

use App\Http\Requests\AppRequest;
use App\Models\Template\Template;

class TemplateRequest extends AppRequest
{
    public function rules()
    {
        return $this->initRules(new Template());
    }
}
