<?php /** @noinspection ALL */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\AddFundsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CCBillController;
use App\Http\Controllers\API\CommentsController;
use App\Http\Controllers\API\CountriesStatesController;
use App\Http\Controllers\API\InstallController;
use App\Http\Controllers\API\InstallScriptController;
use App\Http\Controllers\API\LangController;
use App\Http\Controllers\API\LiveStreamingsController;
use App\Http\Controllers\API\MessagesController;
use App\Http\Controllers\API\PagesController;
use App\Http\Controllers\API\PayPalController;
use App\Http\Controllers\PayPerViewController;
use App\Http\Controllers\API\PaystackController;
use App\Http\Controllers\API\ProductsController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\StripeConnectController;
use App\Http\Controllers\API\StripeController;
use App\Http\Controllers\API\StripeWebHookController;
use App\Http\Controllers\API\SubscriptionsController;
use App\Http\Controllers\API\TaxRatesController;
use App\Http\Controllers\API\TipController;
use App\Http\Controllers\API\TwoFactorAuthController;
use App\Http\Controllers\API\UpgradeController;
use App\Http\Controllers\API\UploadMediaController;
use App\Http\Controllers\API\UploadMediaFileShopController;
use App\Http\Controllers\API\UploadMediaMessageController;
use App\Http\Controllers\API\UploadMediaPreviewShopController;
use App\Http\Controllers\API\UpdatesController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//AuthController API Routes without Auth
Route::post('login' , [AuthController::class, 'login']);
Route::post('register' , [AuthController::class, 'register']);
Route::post('forget_password', [ForgotPasswordController::class , 'sendResetLinkEmail']);
Route::get('passwordreset' , function(){
    Auth::logout();
   return 'Password Reset Please Login in App.'; 
});


//HomeController API Routes without Auth

Route::get('explore/creators/live' , [HomeController::class , 'creatorsBroadcastingLive']);
Route::get('verify/account/{confirmation_code}' , [HomeController::class , 'getVerifyAccount'])->where('confirmation_code','[A-Za-z0-9]+');
Route::get('search/creators' , [HomeController::class , 'searchCreator']);

//BlogController API Routes without Auth
Route::get('blog' , [BlogController::class , 'blog']);
Route::get('blog/post/{id}/{slug?}' , [BlogController::class , 'post']);

//CCBillController API Routes without Auth
Route::any('ccbill/approved' , [CCBillController::class , 'approved']);
Route::get('payment/ccbill' , [CCBillController::class , 'show']);

//CommentsController API Routes without Auth
Route::get('loadmore/comments' , [CommentsController::class , 'loadmore']);

//InstallController API Routes without Auth
Route::get('install/{addon}' , [InstallController::class , 'install']);

//InstallScriptController API Routes without Auth
Route::get('install/script' , [InstallScriptController::class , 'requirements']);
Route::get('install/script/database' , [InstallScriptController::class , 'database']);

//PagesController API Routes
Route::get('p/{page}' , [PagesController::class , 'show'])->where('page','[^/]*');

//PayPalController API Routes
Route::get('payment/paypal' , [PayPalController::class , 'show']);

//PaystackController API Routes
Route::get('payment/paystack', [PaystackController::class , 'show']);

//StripeConnectController API Routes
Route::get('stripe/connect' , [StripeConnectController::class , 'redirectToStripe']);
Route::get('connect/{token}' , [StripeConnectController::class , 'saveStripeAccount']);

//StripeController API Routes
Route::get('payment/stripe' , [StripeController::class , 'show']);



//UploadMediaFileShopController API Routes
Route::any('upload/media/shop/file' , [UploadMediaFileShopController::class , 'store']);

//UploadMediaMessageController API Routes
Route::any('upload/media/message' , [UploadMediaMessageController::class , 'store']);

//UploadMediaPreviewShopController API Routes
Route::any('upload/media/shop/preview' , [UploadMediaPreviewShopController::class , 'store']);

//UpdatesController API Routes
Route::get('ajax/updates' , [UpdatesController::class , 'ajaxUpdates']);
Route::get('files/storage/{id}/{path}' , [UpdatesController::class , 'image'])->where(['id' =>'[0-9]+', 'path' => '.*']);
Route::get('files/messages/{id}/{path}' , [UpdatesController::class , 'messagesImage'])->where(['id' =>'[0-9]+', 'path' => '.*']);
Route::get('file/media/{typeMedia}/{fileId}/{filename}' , [UpdatesController::class , 'getFileMedia']);
Route::get('media/storage/focus/{type}/{path}' , [UpdatesController::class , 'imageFocus'])->where(['type' => '(video|photo|message)$', 'path' => '.*']);

//API Routes with Auth
Route::middleware('auth:api')->group( function () {
    
    Route::post('subscription/cancel/{id}',[UserController::class , 'cancelSubscription']);
    Route::post('subscription/wallet/cancel/{id}',[SubscriptionsController::class , 'cancelWalletSubscription']);
    Route::post('subscription/free/cancel/{id}',[SubscriptionsController::class ,'cancelFreeSubscription']);
    Route::post('subscription/free',[SubscriptionsController::class , 'subscriptionFree']);
    Route::post('buy/subscription',[SubscriptionsController::class , 'buy']);
    
    Route::post('send/ppv', [PayPerViewController::class , 'send']);
    Route::post('send/tip', [TipController::class , 'send']);
    
    Route::post('send/ppv_demo', 'PayPerViewController@send_demo')->name('ppv_demo');
    
    
    
    //UploadMediaController API Routes
    Route::any('upload/media' , [UploadMediaController::class , 'store']);
    Route::post('delete/media',[UploadMediaController::class , 'delete']);

    Route::post('/logout', [AuthController::class  , 'logout']);
    
    //HomeController API Routes with Auth
    Route::get('/' , [HomeController::class , 'index']);
    Route::get('mode/{mode}' , [HomeController::class , 'darkMode'])->where('mode', '(dark|light)$');
    Route::get('ajax/user/updates' , [HomeController::class , 'ajaxUserUpdates']);
    Route::post('ajax/bookmark',[HomeController::class , 'addBookmark']);

    //AddFundsController API Routes with Auth
    Route::get('my/wallet' , [AddFundsController::class , 'wallet']);
    Route::get('add/funds' , [AddFundsController::class , 'addfunds']);
    Route::post('add/funds', [AddFundsController::class , 'send']);
    Route::post('webhook/mollie', [AddFundsController::class , 'webhookMollie']);
    Route::any('coinpayments/ipn' , [AddFundsController::class , 'coinPaymentsIPN']);
    Route::get('wallet/payment/success' , [AddFundsController::class , 'paymentProcess']);
    Route::get('mercadopado/process' , [AddFundsController::class , 'mercadoPagoProcess']);
    Route::get('flutterwave/callback' , [AddFundsController::class , 'flutterwaveCallback']);

    //LiveStreamingsController API Routes
    Route::get('live/{username}' , [LiveStreamingsController::class , 'show']);

    //MessagesController API Routes
    Route::get('messages' , [MessagesController::class , 'inbox']);
    Route::get('messages/{id}/{username?}' , [MessagesController::class , 'messages'])->where(array('id' => '[0-9]+'));
    Route::get('loadmore/messages' , [MessagesController::class , 'loadmore']);
    Route::get('messages/ajax/chat' , [MessagesController::class , 'ajaxChat']);
    Route::get('load/chat/ajax/{id}' , [MessagesController::class , 'loadAjaxChat']);
    Route::get('messages/search/creator', [MessagesController::class , 'searchCreator']);
    
    Route::post('message/send', [MessagesController::class , 'send']);
    Route::any('upload/media/message',[UploadMediaMessageController::class , 'store']);
    Route::post('delete/media/message',[UploadMediaMessageController::class , 'delete']);
    Route::post('message/delete', [MessagesController::class , 'delete']);
    Route::post('conversation/delete/{id}', [MessagesController::class , 'deleteChat']);
    
    Route::get('download/message/file/{id}' , [MessagesController::class , 'downloadFileZip']);

    //PaystackController API Routes
    Route::get("paystack/card/authorization/verify" , [PaystackController::class , 'cardAuthorizationVerify']);

    //ProductsController API Routes
    Route::get('add/product' , [ProductsController::class , 'create']);
    Route::get('add/custom/content' , [ProductsController::class , 'createCustomContent']);
    Route::get('product/download/{id}' , [ProductsController::class , 'download']);

    //UpdatesController API Routes
    Route::get('update/edit/{id}' , [UpdatesController::class , 'edit']);
    Route::get('ajax/user/bookmarks' , [UpdatesController::class , 'ajaxBookmarksUpdates']);
    Route::get('explore' , [UpdatesController::class , 'explore']);
    Route::get('ajax/explore', [UpdatesController::class , 'ajaxExplore']);
    Route::post('update/create',[UpdatesController::class , 'create']);
    Route::post('pin/post',[UpdatesController::class , 'pinPost']);
    Route::post('update/edit',[UpdatesController::class , 'postEdit']);
    Route::post('update/delete/{id}',[UpdatesController::class , 'delete']);

    //UserController API Routes
    Route::get('dashboard' , [UserController::class , 'dashboard']);
    Route::get('ajax/notifications' , [UserController::class , 'ajaxNotifications']);
    Route::get('settings/page' , [UserController::class , 'settingsPage']);
    Route::get('privacy/security' , [UserController::class , 'privacySecurity']);
    Route::get('settings/verify/account' , [UserController::class , 'verifyAccount']);
    Route::post('settings/verify/account',[UserController::class , 'verifyAccountSend']);
    Route::get('notifications' , [UserController::class , 'notifications']);
    Route::get('settings/password' , [UserController::class , 'password']);
    Route::get('my/subscribers' , [UserController::class , 'mySubscribers']);
    Route::get('my/subscriptions' , [UserController::class , 'mySubscriptions']);
    Route::get('my/payments' , [UserController::class , 'myPayments']);
    Route::get('my/payments/received' , [UserController::class , 'myPayments']);
    Route::get('my/payments/invoice/{id}' , [UserController::class , 'invoice']);
    Route::get('settings/payout/method' , [UserController::class , 'payoutMethod']);
    Route::get('settings/withdrawals' , [UserController::class , 'withdrawals']);
    Route::get("settings/payments/card" , [UserController::class , 'formAddUpdatePaymentCard']);
    Route::get('my/bookmarks' , [UserController::class , 'myBookmarks']);
    Route::get('my/purchases' , [UserController::class , 'myPurchases']);
    Route::get('ajax/user/purchases', [UserController::class , 'ajaxMyPurchases']);
    Route::get('download/file/{id}' , [UserController::class , 'downloadFile']);
    Route::get('deposits/invoice/{id}' , [UserController::class , 'invoiceDeposits']);
    Route::get('my/cards', [UserController::class , 'myCards']);
    Route::get('settings/restrictions' , [UserController::class , 'restrictions']);
    Route::get('my/posts',[UserController::class , 'myPosts']);
    Route::get('block/countries',[UserController::class , 'blockCountries']);
    Route::get('my/referrals',[UserController::class , 'myReferrals']);
    Route::get('my/purchased/items',[UserController::class , 'purchasedItems']);
    Route::get('my/sales',[UserController::class , 'mySales']);
    Route::get('my/products',[UserController::class , 'myProducts']);
    Route::get('ajax/mentions', [UserController::class , 'mentions']);
    Route::post('ajax/like', [UserController::class , 'like']);
    Route::post('upload/cover',[UserController::class , 'uploadCover']);
    Route::post('upload/avatar',[UserController::class , 'uploadAvatar']);
    Route::post('settings/page',[UserController::class , 'updateSettingsPage']);
    Route::post('report/creator/{id}',[UserController::class , 'reportCreator']);
    Route::post('restrict/user/{id}', [UserController::class , 'restrictUser']);
    Route::post('block/countries',[UserController::class , 'blockCountriesStore']);
    Route::post('settings/password',[UserController::class , 'updatePassword']);
    Route::post('logout/session/{id}', [UserController::class , 'logoutSession']);
    Route::post('privacy/security',[UserController::class , 'savePrivacySecurity']);
    Route::post('settings/subscription',[UserController::class , 'saveSubscription']);
    Route::get('postdata/{id}',[UserController::class , 'getpostdata']);
    Route::post('account/delete',[UserController::class , 'deleteAccount']);
    
    //API Routes with Auth CommentController
    Route::post('ajax/delete-comment/{id}', [CommentsController::class , 'destroy']);
	 Route::post('comment/store', [CommentsController::class , 'store']);
	 Route::post('comment/like',[CommentsController::class , 'like']);

    //API Routes with Auth and role
    Route::middleware('role')->group(function (){

        //AdminController API Routes
        Route::get('panel/admin' , [AdminController::class , 'admin']);
        Route::get('panel/admin/settings' , [AdminController::class , 'settings']);
        Route::get('panel/admin/settings/limits' , [AdminController::class , 'settingsLimits']);
        Route::get('panel/admin/theme' , [AdminController::class , 'theme']);
        Route::get('panel/admin/withdrawals' , [AdminController::class , 'withdrawals']);
        Route::get('panel/admin/withdrawal/{id}' , [AdminController::class , 'withdrawalsView']);
        Route::get('panel/admin/subscriptions' , [AdminController::class , 'subscriptions']);
        Route::get('panel/admin/transactions' , [AdminController::class , 'transactions']);
        Route::get('panel/admin/members' , [AdminController::class , 'index']);
        Route::get('panel/admin/members/edit/{id}' , [AdminController::class , 'edit']);
        Route::get('panel/admin/verification/members' , [AdminController::class , 'memberVerification']);
        Route::get('panel/admin/payments' , [AdminController::class , 'payments']);
        Route::get('panel/admin/payments/{id}' , [AdminController::class , 'paymentsGateways']);
        Route::get('panel/admin/profiles-social' , [AdminController::class , 'profiles_social']);
        Route::get('panel/admin/categories' , [AdminController::class , 'categories']);
        Route::get('panel/admin/categories/add' , [AdminController::class , 'addCategories']);
        Route::get('panel/admin/categories/edit/{id}' , [AdminController::class , 'editCategories']);
        Route::get('panel/admin/posts' , [AdminController::class , 'posts']);
        Route::get('panel/admin/reports' , [AdminController::class , 'reports']);
        Route::get('panel/admin/google' , [AdminController::class , 'google']);
        Route::get('panel/admin/blog' , [AdminController::class , 'blog']);
        Route::get('panel/admin/blog/{id}' , [AdminController::class , 'editBlog']);
        Route::get('panel/admin/resend/email/{id}' , [AdminController::class , 'resendConfirmationEmail']);
        Route::get('panel/admin/deposits' , [AdminController::class , 'deposits']);
        Route::get('panel/admin/deposits/{id}' , [AdminController::class , 'depositsView']);
        Route::get('panel/admin/members/roles-and-permissions/{id}' , [AdminController::class , 'roleAndPermissions']);
        Route::get('file/verification/{filename}' ,  [AdminController::class , 'getFileVerification']);
        Route::get('panel/admin/referrals' , [AdminController::class , 'referrals']);
        Route::get('panel/admin/products' , [AdminController::class , 'products']);
        Route::get('panel/admin/sales' , [AdminController::class , 'sales']);

        //CountriesStatesController API Routes
        Route::get('panel/admin/countries' , [CountriesStatesController::class , 'countries']);
        Route::get('panel/admin/countries/edit/{id}' , [CountriesStatesController::class , 'editCountry']);
        Route::get('panel/admin/states' , [CountriesStatesController::class , 'states']);
        Route::get('panel/admin/states/edit/{id}' , [CountriesStatesController::class , 'editState']);

        //LangController API Routes with Auth
        Route::get('panel/admin/languages' , [LangController::class , 'index']);
        Route::get('panel/admin/languages/create' , [LangController::class , 'create']);
        Route::get('panel/admin/languages/edit/{id}' , [LangController::class , 'edit']);

        //PagesController API Routes with Auth and Role
        Route::get('panel/admin/pages' , [PagesController::class , 'index']);
        Route::get('panel/admin/pages/create' , [PagesController::class , 'create']);
        Route::get('panel/admin/pages/edit/{id}' , [PagesController::class , 'edit']);

        //TaxRatesController API Routes
        Route::get('panel/admin/tax-rates' , [TaxRatesController::class , 'show']);
        Route::get('panel/admin/tax-rates/edit/{id}' , [TaxRatesController::class , 'edit']);

        //UpgradeController API Routes
        Route::get('update/{version}' , [UpgradeController::class , 'update']);

    });

    //API Routes with live
    Route::middleware('live')->group(function (){

        //LiveStreamingController API Routes
        Route::get('get/data/live' , [LiveStreamingsController::class , 'getDataLive']);

    });

});

//API Routes with Private.Content
Route::middleware('private.content')->group(function (){

    //HomeController API Routes
    Route::get('creators/{type?}' , [HomeController::class , 'creators']);
    Route::get('category/{slug}/{type?}' , [HomeController::class , 'category']);

    //ProductsController API Routes
    Route::get('shop' , [ProductsController::class , 'index']);
    Route::get('shop/product/{id}/{slug?}' , [ProductsController::class , 'show']);

    //UserController API Routes
    Route::get('{slug}', [UserController::class , 'profile'])->where('slug','[A-Za-z0-9\_-]+');
    Route::get('{slug}/{media}', [UserController::class , 'profile'])->where('media', '(photos|videos|audio|shop|files)$');
    Route::get('{slug}/post/{id}', [UserController::class , 'postDetail'])->where('slug','[A-Za-z0-9\_-]+');

});

//API Routes with Guest
Route::middleware('guest')->group( function () {

    //SocialAuthController API Routes
    Route::get('oauth/{provider}' , [SocialAuthController::class , 'redirect'])->where('provider', '(facebook|google|twitter)$');
    Route::get('oauth/{provider}/callback' , [SocialAuthController::class , 'callback'])->where('provider', '(facebook|google|twitter)$');
});
