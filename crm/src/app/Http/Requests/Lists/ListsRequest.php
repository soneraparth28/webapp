<?php

namespace App\Http\Requests\Lists;

use App\Http\Requests\AppRequest;
use App\Models\Lists\Lists;

class ListsRequest extends AppRequest
{

    public function rules()
    {
        return (new Lists())->createdRules();
    }


}
