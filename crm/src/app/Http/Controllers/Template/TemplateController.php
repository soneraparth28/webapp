<?php

namespace App\Http\Controllers\Template;

use App\Exceptions\GeneralException;
use App\Filters\Template\TemplateFilter;
use App\Helpers\Core\Traits\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Template\TemplateRequest;
use App\Jobs\App\Template\ThumbnailGenerateJob;
use App\Models\Template\Template;
use App\Notifications\Template\TemplateNotification;
use App\Services\Template\TemplateService;

class TemplateController extends Controller
{

    use FileHandler;
    public function __construct(TemplateService $service, TemplateFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
       return $this->service
           ->brandFilter()
           ->with('brand')
           ->filters($this->filter)
           ->paginate();
    }

    public function create()
    {
        return view('brands.templates.create');
    }

    public function store(TemplateRequest $request)
    {
        $template = $this->service
            ->setModel((new Template()))
            ->save();
//        ThumbnailGenerateJob::dispatch($template)
//            ->onQueue('high')
//            ->onConnection('sync');

        notify()
            ->on('template_created')
            ->with($template)
            ->send(TemplateNotification::class);

        return created_responses('template');
    }

    public function show($id)
    {
        return $this->service
            ->brandFilter()
            ->find($id);
    }

    public function edit(Template $template)
    {
        if ($template->brand_id == request()->brand_id) {
            return view('brands.templates.create', [
                'id' => $template->id,
                'action' => 'edit'
            ]);
        }

        throw new GeneralException(
            trans('default.action_not_allowed').'
            '.trans('default.this_template_can_be_only_edited_from_app_side').' '.trans('default.You can duplicate it if you want')
        );
    }
    public function update(Template $template, TemplateRequest $request)
    {
        if ($template->brand_id == request()->brand_id) {
            $template->fill(
                array_merge($request->all(), [
                    'updated_by' => auth()->id()
                ])
            );
            if ($template->isDirty('custom_content')) {
//                ThumbnailGenerateJob::dispatch($template)
//                    ->onQueue('high')
//                    ->onConnection('sync');

                notify()
                    ->on('template_updated')
                    ->with($template)
                    ->send(TemplateNotification::class);
            }

            $template->save();
        }

        return updated_responses('template');
    }

    public function destroy(Template $template)
    {
        if ($template->brand_id == request()->brand_id) {
            $this->deleteFile(optional($template->thumbnail)->path);
            $template->thumbnail()->delete();
            $template->delete();

            notify()
                ->on('template_deleted')
                ->with((object)$template->toArray())
                ->send(TemplateNotification::class);

            return deleted_responses('template');
        }
        throw new GeneralException(trans('default.action_not_allowed'));
    }




}
