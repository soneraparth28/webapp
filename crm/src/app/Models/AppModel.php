<?php


namespace App\Models;


use Altek\Eventually\Eventually;
use App\Filters\FilterBuilder;
use App\Helpers\Traits\BrandInactiveTrait;
use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\AppModel
 *
 * @method static Builder|BaseModel filters(FilterBuilder $filter)
 * @method static Builder|AppModel newModelQuery()
 * @method static Builder|AppModel newQuery()
 * @method static Builder|AppModel query()
 * @mixin \Eloquent
 */
class AppModel extends BaseModel
{
    use Eventually, BrandInactiveTrait;
    public static function boot()
    {
        parent::boot();
        if (!app()->runningInConsole()) {

            $action = fn (AppModel $model) => (new static())->actionIfInactive();

            static::saving($action);
            static::attaching($action);
            static::detaching($action);
            static::syncing($action);
            static::deleting($action);
        }
    }

}
