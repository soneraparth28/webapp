<?php


namespace App\Services\Core\Brand;

use App\Helpers\Core\Traits\Helpers;
use App\Models\Core\App\Brand\Brand;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Auth\UserService;
use App\Services\Core\BaseService;
/**
 * App\Models\Core\App\Brand\Brand
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\BaseModel filters(\App\Filters\FilterBuilder $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Core\App\Brand\Brand query()

 * @mixin \Eloquent
 */
class BrandService extends BaseService
{
    use Helpers;
    public function __construct(Brand $brand)
    {
        $this->model = $brand;
    }

    /**
     * @return array
     */
    public function generateShortNames() : array
    {
        $slugged_name = \Str::slug(request()->name, '-');
        if (strlen(request()->name) <= 3) {
            return [];
        }
        $short_names =  $this->model->similarShortNames($slugged_name);
        $generated_short_names = [];

        if (!count($short_names)) {
            $generated_short_names[] = $slugged_name;
        }
        $name_parts = explode('-', $slugged_name);
        if (count($name_parts) > 1) {
            $name = array_reduce($name_parts, function ($carry, $name) {
                $carry .= $name[0];
                return $carry;
            }, '');
            if ($short_names->whereIn('short_name', $name)->isEmpty()) {
                $generated_short_names[] = "$name-" . rand(1, 999);
            }
        }

        while (count($generated_short_names) < 5) {
            $random_name =  "$slugged_name-" . rand(1, 999);
            if ($short_names->whereIn('short_name', $random_name)->isEmpty()) {
                $generated_short_names[] = $random_name;
            }
        }

        return $generated_short_names;
    }

    public function cloneGlobalSettings()
    {
        $settings = resolve(SettingRepository::class)->settings(
            config('settings.brand_default_prefix')
        );
        $this->model->settings()
            ->saveMany($settings);
        return true;
    }

    public function attachUser(Brand $brand)
    {
        $users = resolve(UserService::class)
            ->find($this->checkMakeArray(request('users')));

        foreach ($users as $user) {

            array_map(function ($role) use ($user){
                $user->assignRole($role);
            }, request()->roles);

            if (!$brand->users->contains($user->id))
                $brand->users()->attach($user->id);

        }

        return $brand->load('users');
    }

    public function detachUser(Brand $brand)
    {
        $user_ids = $this->checkMakeArray(request('users'));

        $brand->users()->detach($user_ids);

        return $brand->load('users');
    }
}
