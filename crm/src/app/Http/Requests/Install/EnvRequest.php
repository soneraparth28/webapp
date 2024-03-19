<?php

namespace App\Http\Requests\Install;

use App\Helpers\Core\Traits\PasswordMessageHelper;
use Illuminate\Foundation\Http\FormRequest;

class EnvRequest extends FormRequest
{
    use PasswordMessageHelper;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'database_connection' => 'required|in:mysql,pgsql,sqlsrv',
            'database_hostname' => 'required|min:3',
            'database_port' => 'required|min:3',
            'database_name' => 'required',
            'database_username' => 'required',
            'database_password' => 'required',
            'first_name' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i'],
            'name' => 'required|min:3',
            'short_name' => 'required',
            'code' => 'required|min:3'
        ];
    }
}
