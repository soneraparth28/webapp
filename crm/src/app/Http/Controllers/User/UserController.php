<?php

namespace App\Http\Controllers\User;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Core\Auth\User\UserController as BaseUserController;
use App\Repositories\App\StatusRepository;
use Illuminate\Database\Eloquent\Builder;

class UserController extends BaseUserController
{
    public function index()
    {
        return $this->basicQuery()
            ->whereDoesntHave('brands')
            ->get(['id', 'first_name', 'last_name', 'email']);
    }

    public function brandUsers()
    {
        return $this->basicQuery()
            ->whereHas('brands', function (Builder $builder) {
                $builder->where('id', brand()->id);
            })->get(['id', 'first_name', 'last_name', 'email']);;
    }

    /**
     * @return Builder
     */
    public function basicQuery()
    {
        $existing_users = request()->get('existing') ? explode(',', request()->existing) : [1];
        $status_invited = resolve(StatusRepository::class)->userInvited();
        return $this->service
            ->filters($this->filter)
            ->when(count($existing_users), function (Builder $builder) use ($existing_users) {
                $builder
                    ->with('profilePicture')
                    ->whereNotIn('id', array_merge($existing_users, [1]));
            })->where('status_id', '!=', $status_invited)
            ->latest('id');
    }

    public function user()
    {
        return view('auth.profile');
    }

    public function profile()
    {
        return view('auth.brand_user_profile');
    }

    public function lists()
    {
        if (authorize_any(['view_users', 'view_roles'])) {
            return view('brands.userRoles.index');
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }
}
