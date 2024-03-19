<?php


namespace App\Services\Template;

use App\Models\Template\Template;
use App\Services\AppService;
use Illuminate\Database\Eloquent\Builder;

class TemplateService extends AppService
{

    public function __construct(Template $template)
    {
        $this->model = $template;
    }

    public function brandFilter()
    {
        return $this->model->where(function (Builder $builder) {
            $builder->where('brand_id', request()->brand_id)
                ->orWhereNull('brand_id');
        });
    }
}
