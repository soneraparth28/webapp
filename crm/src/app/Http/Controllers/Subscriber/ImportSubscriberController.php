<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\ImportSubscriberRequest as Request;
use App\Http\Requests\Subscriber\ImportPreviewRequest as PreviewRequest;
use App\Notifications\Subscriber\SubscriberImportNotification;
use App\Services\Core\Builder\Form\CustomFieldService;
use App\Services\Subscriber\SubscriberService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ImportSubscriberController extends Controller
{
    public function __construct(SubscriberService $service)
    {
        $this->service = $service;
    }

    public function index(PreviewRequest $request)
    {
        return DB::transaction(function () use($request) {
            $subscribers = $this->service->repository->previewImported(
                $request->file('subscribers')
            );

            if ($subscribers instanceof Response)
                return $request->invalidField($subscribers);

            if ($request->type && count($subscribers['sanitized']) > 2000)
                return $request->rowCountError();

            $count = 0;
            if(filled($subscribers['sanitized'])) {
                $count = $this->service
                    ->saveImported( $subscribers['sanitized'], $request->type );

                if ($count) {
                    notify()
                        ->on('subscribers_bulk_imported')
                        ->with(brand())
                        ->send(SubscriberImportNotification::class);
                }
            }
            unset($subscribers['sanitized']);

            return $request->finalResponse($count, $subscribers);
        });

    }

    public function create()
    {
        $customKeys = resolve(CustomFieldService::class)->subscriberFields()->toArray();
        return view('brands.subscribers.import', [
            'validKeys' => collect(array_merge(['first_name', 'last_name', 'email'], $customKeys))
        ]);
    }

    public function store(Request $request)
    {
        $count = $this->service->quickImport(
            $request->file('subscribers'), $request->type ?? 0
        );

        return response([
            'status' => true,
            'message' => "{$count} " . trans('default.imported_response', [
                'name' => trans("default.subscribers")
            ]),
        ]);
    }

    public function update(Request $request)
    {
        return  DB::transaction(function () use($request) {
            if ($request->type && (count($request->subscribers) > 2000)) {
                return response(['message' => trans('default.import_count_validation')], 422);
            }

            $count =  $this->service->saveImported(
                $request->subscribers, $request->type
            );

//            if ($count) {
//                notify()
//                    ->on('subscribers_bulk_imported')
//                    ->with(brand())
//                    ->send(SubscriberImportNotification::class);
//            }

            return response([
                'status' => true,
                'message' => "{$count} " . trans('default.imported_response', [
                    'name' => trans("default.subscribers")
                ]),
            ]);

        });



    }
}
