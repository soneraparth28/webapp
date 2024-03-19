<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\UserDelete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\AdminSettings;
use App\Models\Subscriptions;
use App\Models\Categories;
use App\Models\TaxRates;
use App\Models\States;
use App\Models\Countries;
use App\Models\Withdrawals;
use App\Models\ReferralTransactions;
use App\Models\Purchases;
use App\Models\Notifications;
use App\Models\PaymentGateways;
use App\Models\Comments;
use App\Models\Transactions;
use App\Models\Products;
use App\Models\Media;
use App\Models\Like;
use App\Models\Blogs;
use App\Models\Updates;
use App\Models\Referrals;
use App\Models\Reports;
use App\Models\VerificationRequests;
use App\Helper;
use Carbon\Carbon;
use App\Models\Deposits;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PostRejected;
use App\Events\NewPostEvent;
use Yabacon\Paystack;
use Illuminate\Validation\Rule;
use Image;
use Mail;

class AdminController extends Controller
{
    use UserDelete;

    public function __construct(AdminSettings $settings)
    {
        $this->settings = $settings::first();
    }
    public function admin(): JsonResponse
    {
        if (! auth()->user()->hasPermission('dashboard')) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'unauthorized User.'
            ];
            return response()->json($response , 400);
        }

        $users               = User::orderBy('id','DESC')->take(4)->get();
        $total_raised_funds  = Transactions::whereApproved('1')->sum('earning_net_admin');
        $total_subscriptions = Subscriptions::count();
        $subscriptions       = Subscriptions::orderBy('id','desc')->take(4)->get();
        $total_posts         = Updates::count();

        // Statistics of the month

        // Today
        $stat_revenue_today = Transactions::where('created_at', '>=', date('Y-m-d H:i:s', strtotime('today')))
            ->whereApproved('1')
            ->sum('earning_net_admin');

        // Week
        $stat_revenue_week = Transactions::whereBetween('created_at', [
            Carbon::parse()->startOfWeek(),
            Carbon::parse()->endOfWeek(),
        ])->whereApproved('1')
            ->sum('earning_net_admin');

        // Month
        $stat_revenue_month = Transactions::whereBetween('created_at', [
            Carbon::parse()->startOfMonth(),
            Carbon::parse()->endOfMonth(),
        ])->whereApproved('1')
            ->sum('earning_net_admin');

        $response = [
            'success' => true,
            'data' => [
                'users' => $users,
                'total_raised_funds' => $total_raised_funds,
                'total_subscriptions' => $total_subscriptions,
                'subscriptions' => $subscriptions,
                'total_posts' => $total_posts,
                'stat_revenue_today' => $stat_revenue_today,
                'stat_revenue_week' => $stat_revenue_week,
                'stat_revenue_month' => $stat_revenue_month,
            ],
            'message' => 'Admin Dashboard Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.dashboard', [
//            'users' => $users,
//            'total_raised_funds' => $total_raised_funds,
//            'total_subscriptions' => $total_subscriptions,
//            'subscriptions' => $subscriptions,
//            'total_posts' => $total_posts,
//            'stat_revenue_today' => $stat_revenue_today,
//            'stat_revenue_week' => $stat_revenue_week,
//            'stat_revenue_month' => $stat_revenue_month
//        ]);
    }
    public function settings(): JsonResponse
    {
        $genders = explode(',', $this->settings->genders);
        $response = [
            'success' => true,
            'data' => [
                'genders' => $genders,
            ],
            'message' => 'Admin Panel Settings.'
        ];
        return response()->json($response , 200);
//        return view('admin.settings', ['genders' => $genders]);
    }
    public function settingsLimits(): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [
                'setting_limits' => $this->settings,
            ],
            'message' => 'Settings Limits.',
        ];
        return response()->json($response , 200);
//        return view('admin.limits')->withSettings($this->settings);
    }
    public function theme(): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'View admin Theme Page.',
        ];
        return response()->json($response , 200);
//        return view('admin.theme');
    }
    public function withdrawals(): JsonResponse
    {
        $data = Withdrawals::orderBy('id','DESC')->paginate(50);
        $response  = [
            'success' => true,
            'data' => [
                'withdrawals' => $data,
            ],
            'message' => 'Withdrawals Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.withdrawals', ['data' => $data]);
    }
    public function withdrawalsView($id): JsonResponse
    {
        $data = Withdrawals::findOrFail($id);
        $response  = [
            'success' => true,
            'data' => [
                'withdrawal' => $data,
            ],
            'message' => 'Withdrawal '.$id.' Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.withdrawal-view', ['data' => $data]);
    }
    public function subscriptions(): JsonResponse
    {
        $data = Subscriptions::orderBy('id','DESC')->paginate(50);
        $response  = [
            'success' => true,
            'data' => [
                'subscriptions' => $data,
            ],
            'message' => 'Subscriptions  Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.subscriptions', ['data' => $data]);
    }
    public function transactions(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if ($query != '' && strlen( $query ) > 2) {
            $data = Transactions::where('txn_id', 'LIKE', '%'.$query.'%')->orderBy('id','DESC')->paginate(50);
        } else {
            $data = Transactions::orderBy('id','DESC')->paginate(50);
        }
        $response  = [
            'success' => true,
            'data' => [
                'transactions' => $data,
            ],
            'message' => 'Transactions  Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.transactions', ['data' => $data]);
    }
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $sort  = $request->input('sort');

        if ($search != '' && strlen( $search ) > 2) {
            $data = User::where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('username', 'LIKE', '%'.$search.'%')
                ->orWhere('email', 'LIKE', '%'.$search.'%')
                ->orderBy('id','desc')->paginate(20);
        } else {
            $data = User::orderBy('id','desc')->paginate(20);
        }

        if (request('sort') == 'admins') {
            $data = User::whereRole('admin')->orderBy('id','desc')->paginate(20);
        }

        if (request('sort') == 'creators') {
            $data = User::where('verified_id', 'yes')->orderBy('id','desc')->paginate(20);
        }

        if (request('sort') == 'email_pending') {
            $data = User::whereStatus('pending')->orderBy('id','desc')->paginate(20);
        }
        $response  = [
            'success' => true,
            'data' => [
                'users' => $data,
                'query' => $search,
                'sort' => $sort,
            ],
            'message' => 'Members  Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.members', ['data' => $data, 'query' => $search, 'sort' => $sort]);
    }
    public function edit($id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id == 1 || $user->id == auth()->user()->id) {
            \Session::flash('info_message', trans('admin.user_no_edit'));
            $response = [
                'success' => true,
                'data' => [
                    'redirect_url' => 'panel/admin/members',
                ],
                'message' => 'Redirect Url.',
            ];
            return response()->json($response , 200);
            // return redirect('panel/admin/members');
        }
        $response = [
            'success' => true,
            'data' => [
                'user' => $user,
            ],
            'message' => 'User Data.',
        ];
        return response()->json($response , 200);
        // return view('admin.edit-member')->withUser($user);
    }
    public function memberVerification(): JsonResponse
    {
        $data = VerificationRequests::orderBy('id','desc')->get();
        $response = [
            'success' => true,
            'data' => [
                'verification_request_data' => $data,
            ],
            'message' => 'Verification Request Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.verification')->withData($data);
    }
    public function payments()
    {
        $stripeConnectCountries = explode(',', $this->settings->stripe_connect_countries);
        $response = [
            'success' => true,
            'data' => [
                'stripe_countries' => $stripeConnectCountries,
            ],
            'message' => 'Stripe Connected Countries.',
        ];
        return response()->json($response , 200);
//        return view('admin.payments-settings')->withStripeConnectCountries($stripeConnectCountries);
    }
    public function paymentsGateways($id)
    {
        $data = PaymentGateways::findOrFail($id);
        $name = ucfirst($data->name);
        $response = [
            'success' => true,
            'data' => [
                'payment_gateways' => $data,
                'name' => $name,
            ],
            'message' => 'Payment Gateways Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.'.str_slug($name).'-settings')->withData($data);
    }
    public function profiles_social()
    {
        $response = [
            'success' => true,
            'data' => [
                'settings' => $this->settings,
            ],
            'message' => 'Profile Social Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.profiles-social')->withSettings($this->settings);
    }
    public function categories()
    {
        $categories      = Categories::orderBy('name')->get();
        $totalCategories = count( $categories );
        $response = [
            'success' => true,
            'data' => [
                'categories' => $categories,
                'total_categories' => $totalCategories,
            ],
            'message' => 'Categories Data.',
        ];
        return response()->json($response , 200);
//        return view('admin.categories', compact( 'categories', 'totalCategories' ));
    }
    public function addCategories()
    {
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Add Category Page View.',
        ];
        return response()->json($response , 200);
//        return view('admin.add-categories');
    }
    public function editCategories($id)
    {
        $categories = Categories::find($id);
        $response = [
            'success' => true,
            'data' => [
                'category' => $categories,
            ],
            'message' => "Edit Category Data.",
        ];
        return  response()->json($response , 200);
//        return view('admin.edit-categories')->with('categories', $categories);
    }
    public function posts(Request $request)
    {
        $data = Updates::orderBy('id', 'desc')->paginate(20);
        $sort = $request->input('sort');

        if (request('sort') == 'pending') {
            $data = Updates::whereStatus('pending')->orderBy('id', 'desc')->paginate(20);
        }
        $response = [
            'success' => true,
            'data' => [
                'posts' => $data,
                'sort' => $sort,
            ],
            'message' => 'Posts Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.posts', ['data' => $data, 'sort' => $sort]);
    }
    public function reports()
    {
        $data = Reports::orderBy('id','desc')->get();
        $response = [
            'success' => true,
            'data' => [
                'reports' => $data,
            ],
            'message' => 'Reports Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.reports')->withData($data);
    }
    public function google()
    {
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Google Page View.',
        ];
        return response()->json($response, 200);
    }
    public function blog()
    {
        $data = Blogs::orderBy('id','desc')->paginate(50);
        $response = [
            'success' => true,
            'data' => [
                'blogs' => $data,
            ],
            'message' => 'Blogs Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.blog', ['data' => $data]);
    }
    public function editBlog($id)
    {
        $data = Blogs::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'blog' => $data,
            ],
            'message' => 'Blog Edit Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.edit-blog', ['data' => $data ]);
    }
    public function resendConfirmationEmail($id)
    {
        $user =  User::whereId($id)->whereStatus('pending')->firstOrFail();
        $confirmation_code = Str::random(100);
        $_username      = $user->username;
        $_email_user    = $user->email;
        $_title_site    = $this->settings->title;
        $_email_noreply = $this->settings->email_no_reply;
        Mail::send('emails.verify', array('confirmation_code' => $confirmation_code, 'isProfile' => null),
            function($message) use (
                $_username,
                $_email_user,
                $_title_site,
                $_email_noreply
            ) {
                $message->from($_email_noreply, $_title_site);
                $message->subject(trans('users.title_email_verify'));
                $message->to($_email_user,$_username);
            });
        $user->update(['confirmation_code' => $confirmation_code]);
        \Session::flash('success_message', trans('general.send_success'));
        $response = [
            'success' => true,
            'data' => null,
            'message' => 'Email Send.',
        ];
        return response()->json($response, 200);
//        return redirect('panel/admin/members');
    }
    public function deposits()
    {
        $data = Deposits::orderBy('id', 'desc')->paginate(30);
        $response = [
            'success' => true,
            'data' => [
                'deposits' => $data,
            ],
            'message' => 'Deposites Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.deposits')->withData($data);
    }
    public function depositsView($id)
    {
        $data = Deposits::findOrFail($id);
        $response = [
            'success' => true,
            'data' => [
                'deposit' => $data,
            ],
            'message' => 'Deposite Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.deposits-view')->withData($data);
    }
    public function roleAndPermissions($id, Request $request)
    {
        $user = User::findOrFail($id);
        if ($user->id == 1 || $user->id == auth()->user()->id) {
            \Session::flash('info_message', trans('admin.user_no_edit'));
            $response = [
                'success' => true,
                'data' => null,
                'message' => 'Can not Edit.',
            ];
            return response()->json($response, 200);
//            return redirect('panel/admin/members');
        }
        $permissions = explode(',', $user->permissions);
        $response = [
            'success' => true,
            'data' => [
                'user' => $user,
                'permissions' => $permissions,
            ],
            'message' => 'User Role Permissions.',
        ];
        return response()->json($response, 200);
//        return view('admin.role-and-permissions-member')->with([
//            'user' => $user,
//            'permissions' => $permissions,
//        ]);
    }
    public function getFileVerification($filename)
    {
        $filename = config('path.verification').$filename;
        $response = [
            'success' => true,
            'data' => [
                'file_name' => $filename,
            ],
            'message' => 'Download File.',
        ];
        return response()->json($response, 200);
//        return Storage::download($filename, null, [], null);
    }
    public function referrals()
    {
        $data = Referrals::orderBy('id', 'desc')->paginate(20);
        $response = [
            'success' => true,
            'data' => [
                'Referrals' => $data,
            ],
            'message' => 'Referrals Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.referrals')->withData($data);
    }
    public function products()
    {
        $data = Products::orderBy('id', 'desc')->paginate(20);
        $response = [
            'success' => true,
            'data' => [
                'products' => $data,
            ],
            'message' => 'Products Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.products')->withData($data);
    }
    public function sales()
    {
        $sales = Purchases::orderBy('id', 'desc')->paginate(10);
        $response = [
            'success' => true,
            'data' => [
                'sales' => $sales,
            ],
            'message' => 'Sales Data.',
        ];
        return response()->json($response, 200);
//        return view('admin.sales')->withSales($sales);
    }
}
