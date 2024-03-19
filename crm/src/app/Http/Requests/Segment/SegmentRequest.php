<?php

namespace App\Http\Requests\Segment;

use App\Http\Requests\AppRequest;
use App\Models\Segment\Segment;

class SegmentRequest extends AppRequest
{
    public function rules()
    {
        return $this->initRules(new Segment());
    }
}
