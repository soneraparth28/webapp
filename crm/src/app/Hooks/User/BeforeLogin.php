<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

class BeforeLogin extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        $status_id = resolve(StatusRepository::class)->brandInactive();
        $isExist = $this->model->roles()
            ->whereHas('brand', fn (Builder $builder) => $builder->where('status_id', $status_id))
            ->count();

        throw_if(
            $isExist,
            new GeneralException(__t('inactive_brand_action'))
        );

    }
}
