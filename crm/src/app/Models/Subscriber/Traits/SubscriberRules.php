<?php


namespace App\Models\Subscriber\Traits;


use Illuminate\Validation\Rule;

trait SubscriberRules
{
    public function createdRules()
    {
        return [
            'first_name' => 'nullable|min:2',
            'last_name' => 'nullable|min:2',
            'email' => [
                'required', 'email',
                Rule::unique('subscribers')->where(function ($query) {
                    return $query->where('brand_id', request('brand_id'));
                })
            ],
            'brand_id' => 'required|exists:brands,id',
            'status_id' => 'exists:statuses,id',
            'list' => 'array'
        ];
    }

    public function updatedRules()
    {
        return [
            'first_name' => 'nullable|min:2',
            'last_name' => 'nullable|min:2',
            'email' => [
                'required', "email",
                Rule::unique('subscribers')->where(function ($query) {
                    return $query->where('brand_id', request('brand_id'));
                })->ignore(request('id'))
            ],
            'brand_id' => 'required|exists:brands,id',
            'status_id' => 'exists:statuses,id',
            'list' => 'array'
        ];
    }

    public function blackListRules()
    {
        return [
            'subscribers' => 'required'
        ];
    }
    public function changeStatusRules()
    {
        return [
            'subscribers' => 'required|array',
            'subscribers.*' => 'exists:subscribers,id',
            'status' => 'required'
        ];
    }

    public function bulkImportRules()
    {
        return [
            'subscribers' => 'required'
        ];
    }

    public function APICreatedRules()
    {
        return array_merge(
            $this->createdRules(),
            ['api_key' => 'required|exists:api_keys,key'],
        );
    }

    public function APIUpdatedRules()
    {
        $rules = array_merge(
            $this->updatedRules(),
            ['api_key' => 'required|exists:api_keys,key']
        );
        unset($rules['email']);
        unset($rules['status_id']);
        unset($rules['list']);
        return $rules;
    }
}
