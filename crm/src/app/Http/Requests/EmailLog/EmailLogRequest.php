<?php

namespace App\Http\Requests\EmailLog;

use App\Models\Email\EmailLog;
use Illuminate\Foundation\Http\FormRequest;

class EmailLogRequest extends FormRequest
{
    public function rules()
    {
        return $this->initRules(new EmailLog());
    }
}
