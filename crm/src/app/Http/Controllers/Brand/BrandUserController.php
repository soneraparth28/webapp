<?php


namespace App\Http\Controllers\Brand;


use App\Http\Controllers\Core\Auth\User\UserController;
use Illuminate\Database\Eloquent\Builder;

class BrandUserController extends UserController
{
    public function index()
    {
        return $this->service
            ->select(['id', 'first_name', 'last_name', 'email', 'status_id'])
            ->whereHas('brands', function (Builder $builder) {
                $builder->where('id', brand()->id);
            })
            ->filters($this->filter)
            ->with('roles', 'status:id,name,class', 'profilePicture')
            ->latest()
            ->paginate(request()->get('per_page', 10));
    }
}
