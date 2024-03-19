<?php


namespace App\Http\Controllers\Template;


use App\Http\Controllers\Controller;
use App\Models\Template\Template;
use App\Services\Template\TemplateService;

class TemplateAPIController extends Controller
{
    public function __construct(TemplateService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->latest('id')
            ->get(['id', 'subject']);
    }

    public function copy($id)
    {
        $path = 'application.views.settings.template.create';
        if (brand()) {
            $path = 'brands.templates.create';
        }

        return view($path, [
            'id' => $id
        ]);
    }

    public function body(Template $template)
    {
        $body = $template->default_content;
        if ($template->custom_content){
            $body = $template->custom_content;
        }
        return [
            'body' => $body
        ];
    }
}
