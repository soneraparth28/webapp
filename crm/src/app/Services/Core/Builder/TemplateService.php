<?php


namespace App\Services\Core\Builder;


use App\Services\Core\BaseService;

class TemplateService extends BaseService
{
    public function __construct(\App\Models\Template\Template $template)
    {
        $this->model = $template;
    }
}
