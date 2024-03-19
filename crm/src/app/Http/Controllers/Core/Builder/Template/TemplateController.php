<?php

namespace App\Http\Controllers\Core\Builder\Template;

use App\Filters\Core\TemplateFIlter;
use App\Helpers\Core\Traits\FileHandler;
use App\Http\Controllers\Controller;
use App\Jobs\App\Template\ThumbnailGenerateJob;
use App\Models\Core\Builder\Template\Template;
use App\Services\Core\Builder\TemplateService;
use App\Http\Requests\Core\Builder\TemplateRequest as Request;

class TemplateController extends Controller
{
    use FileHandler;
    public function __construct(TemplateService $service, TemplateFIlter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->select('id', 'subject', 'default_content', 'custom_content', 'created_at')
            ->whereNull('brand_id')
            ->filters($this->filter)
            ->latest('id')
            ->paginate(\request('per_page', 10));
    }


    public function create()
    {
        return view('application.views.settings.template.create');
    }


    public function store(Request $request)
    {
        $template = $this->service
            ->save();
//        ThumbnailGenerateJob::dispatch(
//            \App\Models\Template\Template::find($template->id)
//        )->onQueue('high')
//            ->onConnection('sync');

        return created_responses('template', [
            'template' => $template
        ]);
    }

    public function show(Template $template)
    {
        return $template;
    }

    public function edit($id)
    {
        return view('application.views.settings.template.create', [
            'id' => $id,
            'action' => 'edit'
        ]);
    }

    public function update(Template $template, Request $request)
    {
        $template->update(
            array_merge($request->all(), [
                'updated_by' => auth()->id()
            ])
        );
//        ThumbnailGenerateJob::dispatch(
//            \App\Models\Template\Template::find($template->id)
//        )->onQueue('high')
//            ->onConnection('sync');

        return updated_responses('template', [
            'template' => $template
        ]);
    }


    public function destroy(\App\Models\Template\Template $template)
    {
        $this->deleteFile(optional($template->thumbnail)->path);
        $template->thumbnail()->delete();
        $template->delete();

        return  deleted_responses('template');
    }
}
