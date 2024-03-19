<?php


namespace App\Http\Requests\Subscriber;


use App\Helpers\Traits\BrandInactiveTrait;
use App\Http\Requests\AppRequest;

class ImportPreviewRequest extends AppRequest
{
    use BrandInactiveTrait;
    public function authorize()
    {
        return $this->actionIfInactive();
    }

    public function rules()
    {
        return [
            'subscribers' => 'file|mimes:csv,txt|required',
            'type' => 'required'
        ];
    }

    public function rowCountError()
    {
        return response(['errors' => ['files' => [trans('default.import_count_validation'),]]], 422);
    }

    public function invalidField($response)
    {
        $valid = $response->original['valid'] ? implode('", "',  $response->original['valid']) : '';
        return response(['errors' => ['subscribers' => [
            trans('default.import_fields_validation', [
                'fields' => "\"$valid\""
            ])
        ]]], 422);
    }

    public function finalResponse($count, $subscribers)
    {
        if ($count || count($subscribers['filtered'])) {
            return response([
                'status' => true,
                'message' =>"{$count} " . trans('default.imported_response', [
                        'name' => trans("default.subscribers")
                    ]),
                'subscribers' => $subscribers
            ], 200);
        }
        return response([
            'errors' =>  [
                'subscribers' => [trans('default.imported_exists', [
                    'name' => trans("default.subscribers")
                ])]
            ]
        ], 422);
    }
}
