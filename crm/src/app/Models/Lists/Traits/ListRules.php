<?php


namespace App\Models\Lists\Traits;


trait ListRules
{
    public function createdRules()
    {
        return [
            'name' => 'required',
            'type' => 'required|in:imported,dynamic',
            'subscribers' => 'required_if:segments,null',
            'segments' => 'required_if:subscribers,null'
        ];
    }
}
