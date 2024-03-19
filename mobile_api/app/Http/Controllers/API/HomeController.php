<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Categories;
use App\Models\Updates;
use App\Models\LiveStreamings;
use App\Models\Bookmarks;
use App\Models\Comments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Helper;
use Image;
use Cache;
use Mail;
use App\Models\Countries;

class HomeController extends Controller
{
    use Functions;
    public function __construct(Request $request, AdminSettings $settings) {

        $this->request = $request;
        try {
            // Check Database access
            $this->settings = $settings::first();

        } catch (\Exception $e) {
            // Empty
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        //  dd($request->headers->has('authtoken'));
        try {
            // Check Database access
            $this->settings;
        }
        catch (\Exception $e) {
            // Redirect to Installer
            $response = [
                'success' => false,
                'data' => 'install/script.',
                'message' => 'Install The Software.',
            ];
            return response()->json($response , 400);
//            return redirect('install/script');
        }
        // Home Guest
        if (auth()->guest() || !$request->headers->has('authtoken')) {
            $users = User::where('featured','yes')
                ->where('status','active')
                ->whereVerifiedId('yes')
                ->whereHideProfile('no')
                ->whereRelation('plans', 'status', '1')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orWhere('featured','yes')
                ->where('status','active')
                ->whereVerifiedId('yes')
                ->whereHideProfile('no')
                ->whereFreeSubscription('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->inRandomOrder()
                ->paginate(6);

            // Total creators
            $usersTotal = User::whereStatus('active')
                ->whereVerifiedId('yes')
                ->whereRelation('plans', 'status', '1')
                ->whereHideProfile('no')
                ->orWhere('status', 'active')
                ->whereVerifiedId('yes')
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->count();

            $home = $this->settings->home_style == 0 ? 'home' : 'home-login';

            $response = [
                'success' => true,
                'data' => [
                    'home' => $home,
                    'users' => $users,
                    'usersTotal' => $usersTotal,
                ],
                'message' => 'Home Page Data.',
            ];
            return response()->json($response , 200);
//            return view('index.'.$home, [
//                'users' => $users,
//                'usersTotal' => $usersTotal
//            ]);

        } else {

            $users = $this->userExplore();

            $updates = Updates::leftjoin('users', 'updates.user_id', '=', 'users.id')
                ->leftjoin('plans', 'plans.user_id', '=', 'users.id')
                ->leftjoin('subscriptions', 'subscriptions.stripe_price', '=', 'plans.name')

                ->where('subscriptions.user_id', '=', auth()->user()->id)
                ->where('subscriptions.stripe_id', '=', '')
                ->where('ends_at', '>=', now())
                ->where('updates.status', 'active')

                ->orWhere('subscriptions.stripe_id', '<>', '')
                ->where('subscriptions.user_id', '=', auth()->user()->id)
                ->where('stripe_status', 'active')
                ->where('updates.status', 'active')

                ->orWhere('subscriptions.stripe_id', '<>', '')
                ->where('subscriptions.user_id', '=', auth()->user()->id)
                ->where('ends_at', '>=', now())
                ->where('stripe_status', 'canceled')
                ->where('updates.status', 'active')

                ->orWhere('subscriptions.user_id', '=', auth()->user()->id)
                ->where('subscriptions.stripe_id', '=', '')
                ->whereFree('yes')
                ->where('updates.status', 'active')

                ->orWhere('updates.user_id', auth()->user()->id)
                ->where('updates.status', 'active')
                ->groupBy('updates.id')
                ->orderBy('updates.id', 'desc')
                ->select('updates.*')
                ->paginate($this->settings->number_posts_show);

            $response = [
                'success' => true,
                'data' => [
                    'users' => $users,
                    'updates' => $updates,
                    'current_user' => Auth()->user(),
                ],
                'message' => 'Home Page Data.',
            ];
            return response()->json($response , 200);
//            return view('index.home-session', ['users' => $users, 'updates' => $updates]);

        }
    }
    public function ajaxUserUpdates(): JsonResponse
    {
        // dd($request->header());
        $skip = $this->request->input('skip');
        $total = $this->request->input('total');

        $data = Updates::leftjoin('users', 'updates.user_id', '=', 'users.id')
            ->leftjoin('plans', 'plans.user_id', '=', 'users.id')
            ->leftjoin('subscriptions', 'subscriptions.stripe_price', '=', 'plans.name')

            ->where('subscriptions.user_id', '=', auth()->user()->id )
            ->where('subscriptions.stripe_id', '=', '')
            ->where('ends_at', '>=', now())

            ->orWhere('subscriptions.stripe_id', '<>', '')
            ->where('subscriptions.user_id', '=', auth()->user()->id )
            ->where('stripe_status', 'active')

            ->orWhere('subscriptions.stripe_id', '<>', '')
            ->where('subscriptions.user_id', '=', auth()->user()->id )
            ->where('ends_at', '>=', now())
            ->where('stripe_status', 'canceled')

            ->orWhere('subscriptions.user_id', '=', auth()->user()->id)
            ->where('subscriptions.stripe_id', '=', '')
            ->whereFree('yes')

            ->orWhere('updates.user_id', auth()->user()->id)
            ->skip($skip)
            ->take($this->settings->number_posts_show)
            ->groupBy('updates.id')
            ->orderBy( 'updates.id', 'desc' )
            ->select('updates.*')
            ->get();

        $counterPosts = ($total - $this->settings->number_posts_show - $skip);
        $response = [
            'success' => true,
            'data' => [
                'updates' => $data,
                'ajaxRequest' => true,
                'counterPosts' => $counterPosts,
                'total' => $total
            ],
            'message' => 'User Update Data.',
        ];
        return response()->json($response , 200);
//        return view('includes.updates', ['updates' => $data, 'ajaxRequest' => true, 'counterPosts' => $counterPosts, 'total' => $total])->render();
    }
    public function getVerifyAccount($confirmation_code): JsonResponse
    {
        if (auth()->guest()
            || auth()->check()
            && auth()->user()->confirmation_code == $confirmation_code
            && auth()->user()->status == 'pending'
        ) {
            $user = User::where( 'confirmation_code', $confirmation_code )->where('status','pending')->first();

            if ($user) {

                $update = User::where( 'confirmation_code', $confirmation_code )
                    ->where('status','pending')
                    ->update(['status' => 'active', 'confirmation_code' => '']);

                auth()->loginUsingId($user->id);

                $redirect = $this->request->input('r') ?: '/';

                $response = [
                    'success' => true,
                    'data' => [
                        'url' => $redirect,
                    ],
                    'message' => 'User Account Verification Url.',
                ];
                return response()->json($response , 200);
//                return redirect($redirect)
//                    ->with([
//                        'success_verify' => true,
//                    ]);
            } else {
                $response = [
                    'success' => false,
                    'data' => null,
                    'message' => 'User Account Verification Error.',
                ];
                return response()->json($response, 400);
//                return redirect('/')
//                    ->with([
//                        'error_verify' => true,
//                    ]);
            }
        }
        else {
            $response = [
                'success' => true,
                'data' => [
                    'url' => '/',
                ],
                'message' => 'User Account Verified.',
            ];
            return response()->json($response , 200);
//            return redirect('/');
        }
    }
    public function creators($type = false): JsonResponse
    {
        $query = trim($this->request->input('q'));

        switch ($type) {
            case 'featured':
                $orderBy = 'featured_date';
                $title = trans('general.featured_creators');
                break;

            case 'more-active':
                $orderBy = 'COUNT(updates.id)';
                $title = trans('general.more_active_creators');
                break;

            case 'new':
                $orderBy = 'id';
                $title = trans('general.new_creators');
                break;

            case 'free':
                $orderBy = 'free_subscription';
                $title = trans('general.creators_with_free_subscription');
                break;

            default:
                $orderBy = 'COUNT(subscriptions.id)';
                $title = trans('general.explore_our_creators');
                break;
        }

        // Search Creator
        if (strlen($query) >= 3) {

            $title = trans('general.search').' "'.$query.'"';

            $users = User::where('users.status','active')
                ->where('username','LIKE', '%'.$query.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereRelation('plans', 'status', '1')
                ->whereFreeSubscription('no')
                ->whereHideProfile('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orWhere('users.status','active')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->where('name','LIKE', '%'.$query.'%')
                ->whereHideName('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orderBy('featured_date','desc')
                ->paginate(12);

        } else {

            if ($type == 'free') {
                $users = User::where('users.status','active')
                    ->whereVerifiedId('yes')
                    ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                    ->whereFreeSubscription('yes')
                    ->whereHideProfile('no')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                    ->orderBy(\DB::raw($orderBy),'desc')
                    ->paginate(12);
            } else {

                $data = User::where('users.status','active');

                $whereRawFeatured = $type == 'featured' ? 'featured = "yes"' : 'users.status = "active"';

                $data->where('users.status','active')
                    ->whereVerifiedId('yes')
                    ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                    ->whereRelation('plans', 'status', '1')
                    ->whereFreeSubscription('no')
                    ->whereHideProfile('no')
                    ->whereRaw($whereRawFeatured)
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                    ->orWhere('users.status','active')
                    ->whereVerifiedId('yes')
                    ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                    ->whereFreeSubscription('yes')
                    ->whereHideProfile('no')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                    ->whereRaw($whereRawFeatured);

                if ($type == 'more-active') {
                    $data->leftjoin('updates', 'updates.user_id', '=', 'users.id');
                }

                if (! $type) {
                    $data->leftjoin('plans', 'plans.user_id', '=', 'users.id')
                        ->leftjoin('subscriptions', 'subscriptions.stripe_price', '=', 'plans.name');

                    $data->orWhere('subscriptions.stripe_id', '=', '')
                        ->where('ends_at', '>=', now())
                        ->where('users.status','active')
                        ->whereHideProfile('no')
                        ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                        ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                        ->orWhere('subscriptions.stripe_id', '<>', '')
                        ->where('stripe_status', 'active')
                        ->where('users.status','active')
                        ->whereHideProfile('no')
                        ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                        ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                        ->orWhere('subscriptions.stripe_id', '=', '')
                        ->whereFree('yes')
                        ->where('users.status','active')
                        ->whereHideProfile('no')
                        ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                        ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%');
                }

                $users =  $data->groupBy('users.id')
                    ->orderBy(\DB::raw($orderBy), 'DESC')
                    ->orderBy('users.id', 'ASC')
                    ->select('users.*')
                    ->paginate(12);
            }

        }

        if ($this->request->input('page') > $users->lastPage()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            abort(response()->json($response , 404));
        }
        foreach($users as $user)
        {
            $user->updatecount = $user->updates()->count();
            $user->updatephotos = $user->media()->where('media.image', '<>', '')->count();
            $user->updatevideos = $user->media()->where('media.video', '<>', '')->orWhere('media.video_embed', '<>', '')->where('media.user_id', $user->id)->count();
            $user->updatemusic = $user->media()->where('media.music', '<>', '')->count();
            $user->likecount = $user->likesCount();
            $user->subscriptionsActive=$user->totalSubscriptionsActive();
            $user->plans = $plans = $user->plans()
            ->where('interval', '<>', 'monthly')
            ->whereStatus('1')
            ->get();
            $user->userPlanMonthlyActive = $user->planActive();
        }
        $response = [
            'success' => true,
            'data' => [
                'users' => $users,
                'title' => $title,
            ],
            'message' => 'Creator Data.'
        ];
        return response()->json($response , 200);
//        return view('index.creators', [
//            'users' => $users,
//            'title' => $title
//        ]);
    }
    public function category($slug, $type = false): JsonResponse
    {
        $category = Categories::where('slug', '=', $slug)->where('mode','on')->firstOrFail();
        $title    = \Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name;

        switch ($type) {
            case 'featured':
                $orderBy = 'featured_date';
                $title = $title.' - '.trans('general.featured_creators');
                break;

            case 'more-active':
                $orderBy = 'COUNT(updates.id)';
                $title = $title.' - '.trans('general.more_active_creators');
                break;

            case 'new':
                $orderBy = 'id';
                $title = $title.' - '.trans('general.new_creators');
                break;

            case 'free':
                $orderBy = 'free_subscription';
                $title = $title.' - '.trans('general.creators_with_free_subscription');
                break;

            default:
                $orderBy = 'COUNT(subscriptions.id)';
                break;
        }

        if ($type == 'free') {
            $users = User::where('users.status','active')
                ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orderBy($orderBy, 'desc')
                ->paginate(12);
        } else {

            $data = User::where('users.status','active');

            $whereRawFeatured = $type == 'featured' ? 'featured = "yes"' : 'users.status = "active"';

            $data->where('users.status','active')
                ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                ->whereVerifiedId('yes')
                ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereRelation('plans', 'status', '1')
                ->whereFreeSubscription('no')
                ->whereHideProfile('no')
                ->whereRaw($whereRawFeatured)
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orWhere('users.status','active')
                ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                ->whereVerifiedId('yes')
                ->where('users.id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->whereRaw($whereRawFeatured);

            if ($type == 'more-active') {
                $data->leftjoin('updates', 'updates.user_id', '=', 'users.id');
            }

            if (! $type) {
                $data->leftjoin('plans', 'plans.user_id', '=', 'users.id')
                    ->leftjoin('subscriptions', 'subscriptions.stripe_price', '=', 'plans.name');

                $data->orWhere('subscriptions.stripe_id', '=', '')
                    ->where('ends_at', '>=', now())
                    ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                    ->orWhere('subscriptions.stripe_id', '<>', '')
                    ->where('stripe_status', 'active')
                    ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                    ->orWhere('subscriptions.stripe_id', '<>', '')
                    ->where('ends_at', '>=', now())
                    ->where('stripe_status', 'canceled')
                    ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                    ->orWhere('subscriptions.stripe_id', '=', '')
                    ->whereFree('yes')
                    ->where('categories_id', 'LIKE', '%'.$category->id.'%')
                    ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%');
            }


            $users = $data->groupBy('users.id')
                ->orderBy(\DB::raw($orderBy), 'DESC')
                ->orderBy('users.id', 'ASC')
                ->select('users.*')
                ->paginate(12);
        }

        if ($this->request->input('page') > $users->lastPage()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            abort(response()->json($response , 404));
        }
        $response = [
            'success' => true,
            'data' => [
                'users' => $users,
                'title' => $title,
                'slug' => $slug,
                'image' => $category->image,
                'keywords' => $category->keywords,
                'description' => $category->description,
            ],
            'message' => 'Category Data.'
        ];
        return response()->json($response , 200);
//        return view('index.categories', [
//            'users' => $users,
//            'title' => $title,
//            'slug' => $slug,
//            'image' => $category->image,
//            'keywords' => $category->keywords,
//            'description' => $category->description,
//        ]);
    }
    public function searchCreator(): JsonResponse
    {
        $query = $this->request->get('user');
        $data = "";
        if ($query != '' && strlen($query) >= 2) {
            $sql = User::where('status','active')
                ->where('username','LIKE', '%'.$query.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereRelation('plans', 'status', '1')
                ->whereFreeSubscription('no')
                ->whereHideProfile('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                ->orWhere('name','LIKE', '%'.$query.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereRelation('plans', 'status', '1')
                ->whereFreeSubscription('no')
                ->whereHideProfile('no')
                ->whereHideName('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                ->orWhere('status','active')
                ->where('username','LIKE', '%'.$query.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')

                ->orWhere('status','active')
                ->where('name','LIKE', '%'.$query.'%')
                ->whereVerifiedId('yes')
                ->where('id', '<>', $this->settings->hide_admin_profile == 'on' ? 1 : 0)
                ->whereFreeSubscription('yes')
                ->whereHideProfile('no')
                ->whereHideName('no')
                ->where('blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
                ->orderBy('id','desc')
                ->take(4)
                ->get();

//            if ($sql) {
//                foreach ($sql as $user) {
//
//                    $name = $user->hide_name == 'yes' ? $user->username : $user->name;
//
//                    $data .= '<div class="card border-0">
//  							<div class="list-group list-group-sm list-group-flush">
//                 <a href="'.url($user->username).'" class="list-group-item list-group-item-action text-decoration-none py-2 px-3 bg-autocomplete">
//                   <div class="media">
//                    <div class="media-left mr-3 position-relative">
//                        <img class="media-object rounded-circle" src="'.Helper::getFile(config('path.avatar').$user->avatar).'" width="30" height="30">
//                    </div>
//                    <div class="media-body overflow-hidden">
//                      <div class="d-flex justify-content-between align-items-center">
//                       <h6 class="media-heading mb-0 text-truncate">
//                            '.$name.'
//                        </h6>
//                      </div>
//  										<small class="text-truncate m-0 w-100 text-left">@'.$user->username.'</small>
//                    </div>
//                </div>
//                  </a>
//               </div>
//  					 </div>';
//                }
            $response = [
                'success' => true,
                'data' => [
                    'users' => $sql,
                ],
                'message' => 'Creator Search Data.'
            ];
            return response()->json($response , 200);
        }
        $response = [
            'success' => false,
            'data' => null,
            'message' => 'Error Occured.'
        ];
        abort(response()->json($response , 404));
    }
    public function darkMode($mode): JsonResponse
    {
        if ($mode == 'dark') {
            auth()->user()->dark_mode = 'on';
            $message = "Dark Mode on Successfully.";
        }
        else {
            auth()->user()->dark_mode = 'off';
            $message = "Dark Mode off Successfully.";
        }
        auth()->user()->save();
        $response = [
            'success' => true,
            'data' => null,
            'message' => $message,
        ];
        return response()->json($response , 200);
//        return redirect()->back();

    }
    public function creatorsBroadcastingLive()
    {
        // Search Live Streaming
        $users = LiveStreamings::where('live_streamings.updated_at', '>', now()->subMinutes(5))
            ->leftjoin('users', 'users.id', '=', 'live_streamings.user_id')
            ->where('live_streamings.status', '0')
            ->where('users.blocked_countries', 'NOT LIKE', '%'.Helper::userCountry().'%')
            ->orderBy('live_streamings.id', 'desc')
            ->select('live_streamings.*')
            ->paginate(20);

        if ($this->request->input('page') > $users->lastPage()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.'
            ];
            abort(response()->json($response , 404));
        }
        $response = [
            'success' => true,
            'data' => [
                'users' => $users,
            ],
            'message' => 'Creator Live Broadcasting Data.'
        ];
        return response()->json($response , 200);
//        return view('index.creators-live', [
//            'users' => $users
//        ]);

    }
    public function addBookmark()
    {
        // Find post exists
        $post = Updates::findOrFail($this->request->id);
        $bookmark = Bookmarks::firstOrNew([
            'user_id' => auth()->user()->id,
            'updates_id' => $this->request->id
        ]);
        if ($bookmark->exists) {
            $bookmark->delete();
            return response()->json([
                'success' => true,
                'type' => 'deleted'
            ]);
        } else {
            $bookmark->save();
            return response()->json([
                'success' => true,
                'type' => 'added'
            ]);
        }
    }
}
