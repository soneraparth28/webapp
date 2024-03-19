<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.->name('send/ppv' Now create something great!
|
*/


//Testing stuff
Route::get('testing', 'TestingController@index');
Route::post('testing', 'TestingController@transactionMap')->name("testing_filter");
Route::post('register-user-events', 'UserEventController@store')->name("register-user-events");
Route::get('user-events', 'UserEventController@index')->name("user-events");

/*
 |-----------------------------------
 | Index
 |-----------------------------------
 */
Route::get('/', 'HomeController@index')->name('home');
//Route::get('/', 'HomeController@index')->name('home');

Route::get('home', function() {
	return redirect('/');
});

// rq
Route::get('rq', 'HomeController@rq');

// creators referrals
Route::get('creator-signup', 'Auth\RegisterController@showCreatorRegistrationForm');
Route::post('creator-signup', 'Auth\RegisterController@creatorRegister')->name('creator-register');
Route::get('my/content-creators-referrals','UserController@myContentCreatorsReferrals')->middleware('auth');

// course invite
Route::get('course-invitation-signup/{id}','CourseContentController@showCourseInvitationRegistrationForm');
Route::post('course-invitation-signup/{id}','Auth\RegisterController@courseInvitationRegister');


Route::view('plans','auth.plans');
Route::view('plans2','auth.creator-register');

// Authentication Routes.
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout');

// Registration Routes.
Route::get('signup', 'Auth\RegisterController@showRegistrationForm');
Route::post('signup', 'Auth\RegisterController@register')->name('register');

// Password Reset Routes.
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// Contact
Route::view('contact','index.contact');
Route::post('contact','HomeController@contactStore');

// Blog
Route::get('blog', 'BlogController@blog');
Route::get('blog/post/{id}/{slug?}', 'BlogController@post')->name('seo');

// Shop
Route::get('shop', 'ProductsController@index');
Route::get('shop/product/{id}/{slug?}', 'ProductsController@show')->name('seo');

// Pages Static Custom
Route::get('p/{page}','PagesController@show')->where('page','[^/]*' )->name('seo');

// Offline
Route::view('offline','vendor.laravelpwa.offline');

// Social Login
Route::group(['middleware' => 'guest'], function() {
	Route::get('oauth/{provider}', 'SocialAuthController@redirect')->where('provider', '(facebook|google|twitter)$');
	Route::get('oauth/{provider}/callback', 'SocialAuthController@callback')->where('provider', '(facebook|google|twitter)$');
});//<--- End Group guest

// Verify Account
Route::get('verify/account/{confirmation_code}', 'HomeController@getVerifyAccount')->where('confirmation_code','[A-Za-z0-9]+');

 /*
  |-----------------------------------------------
  | Ajax Request
  |--------- -------------------------------------
  */
 Route::get('ajax/updates', 'UpdatesController@ajaxUpdates');
 Route::get('ajax/user/updates', 'HomeController@ajaxUserUpdates');
 Route::get('loadmore/comments', 'CommentsController@loadmore');

 /*
  |-----------------------------------
  | Subscription
  |--------- -------------------------
  */

 // Paypal IPN
 Route::post('paypal/ipn','PayPalController@paypalIpn');

 Route::get('buy/subscription/success/{user}', function($user) {

	 $notifyPayPal = request()->input('paypal') ? ' <br><br>'.trans('general.alert_paypal_delay') : null;

	 session()->put('subscription_success', trans('general.subscription_success').$notifyPayPal);
	 return redirect($user);
 	});

 Route::get('buy/subscription/cancel/{user}', function($user){
	 session()->put('subscription_cancel', trans('general.subscription_cancel'));
	 return redirect($user);
 	});

	// Stripe Webhook
	Route::post('stripe/webhook','StripeWebHookController@handleWebhook');

	// Paystack Webhook
	Route::post('webhook/paystack', 'PaystackController@webhooks');

	// Paypal IPN (TIPS)
  Route::post('paypal/tip/ipn','TipController@paypalTipIpn');

  Route::get('paypal/tip/success/{user}', function($user){
 	 session()->put('subscription_success', trans('general.tip_sent_success'));
 	 return redirect($user);
  	});

  Route::get('paypal/tip/cancel/{user}', function($user){
 	 session()->put('subscription_cancel', trans('general.payment_cancelled'));
 	 return redirect($user);
  	});

	// Tip on Messages
   Route::get('paypal/msg/tip/redirect/{id}', function($id){
  	 return redirect('messages/'.$id);
   	});

		// Paypal IPN (Add Funds)
	  Route::post('paypal/add/funds/ipn','AddFundsController@paypalIpn');

		// CCBill Webhook
		Route::post('webhook/ccbill', 'CCBillController@webhooks');
		Route::any('ccbill/approved', 'CCBillController@approved');

		// Paypal IPN (PPV)
	  Route::post('paypal/ppv/ipn','PayPerViewController@paypalPPVIpn');


// mollie after first verification payment redirect url
Route::post('mollie-subscription-webhook','SubscriptionsController@mollieSubscriptionWebhook')->name('mollie-subscription-webhook');

// revolut cronjob to process subscription payments
Route::get('revolut-subscription-cronjob','SubscriptionsController@revolutSubscriptionCronjob')->name('revolut-subscription-cronjob');

// revolut after content purchase callback url
Route::post('revolut-content-purchase-callback','PayPerViewController@revolutContentPurchaseCallBack')->name('revolut-content-purchase-callback');
 /*
  |-----------------------------------
  | User Views LOGGED
  |--------- -------------------------
  */
 Route::group(['middleware' => 'auth'], function() {

	 // Dashboard
	 Route::get('dashboard','UserController@dashboard');

	 // Buy Subscription
	 Route::post('buy/subscription','SubscriptionsController@buy')->name('buy/subscription');

	 // mollie after first verification payment
	 Route::get('mollie-subscription-first-payment','SubscriptionsController@mollieSubscriptionFirstPayment')->name('mollie-subscription-first-payment');

	 // mollie after first verification payment callback url
	 Route::get('mollie-subscription-first-payment-callback','SubscriptionsController@mollieSubscriptionFirstPaymentCallback')->name('mollie-subscription-first-payment-callback');

	 // Revolut after first verification payment callback url
	 Route::get('revolut-subscription-first-payment-callback','SubscriptionsController@revolutSubscriptionFirstPaymentCallback')->name('revolut-subscription-first-payment-callback');

	 // cancel mollie subscription
	 Route::get('cancel-mollie-subscription/{subscription_id}','SubscriptionsController@cancelMollieSubscription')->name('cancel-mollie-subscription');

	 // Free Subscription
	 Route::post('subscription/free','SubscriptionsController@subscriptionFree');

	 // Cancel Subscription
	 Route::post('subscription/free/cancel/{id}','SubscriptionsController@cancelFreeSubscription');

	 // Ajax Request
	 Route::post('ajax/like', 'UserController@like');
	 Route::get('ajax/notifications', 'UserController@ajaxNotifications');

	 // Comments
	 Route::post('ajax/delete-comment/{id}', 'CommentsController@destroy');
	 Route::post('comment/store', 'CommentsController@store');

	 // Settings Page
  	Route::get('settings/page','UserController@settingsPage');
  	Route::post('settings/page','UserController@updateSettingsPage');
		Route::post('delete/cover','UserController@deleteImageCover');

		// Privacy and Security
   	Route::get('privacy/security','UserController@privacySecurity');
   	Route::post('privacy/security','UserController@savePrivacySecurity');

		Route::post('logout/session/{id}', 'UserController@logoutSession');

		// Subscription Page
   	Route::view('settings/subscription','users.subscription');
   	Route::post('settings/subscription','UserController@saveSubscription');

		// Verify Account
   	Route::get('settings/verify/account','UserController@verifyAccount');
   	Route::post('settings/verify/account','UserController@verifyAccountSend');


    // Addons
     Route::get('settings/addons','AddonsController@index')->name("addons");
     Route::get('settings/addon/{id}','AddonsController@addonPage')->name("addon");
     Route::post('settings/addons','AddonsController@purchaseAddon')->name("addon-subscribe");

     Route::get('subscribe-to-addon','SubscriptionsController@subscriptionAddonInitial')->name("subscription-addon-initial");
     Route::get('subscribe-to-addon-callback','SubscriptionsController@subscriptionAddonInitialCallback')->name("subscription-addon-initial-callback");

     //Ajax user transactions
     Route::post('ajax/transactions-map','UserController@transactionEarningsMap')->name("transactions-map");


		// Delete Account
		Route::view('account/delete','users.delete_account');
   	Route::post('account/delete','UserController@deleteAccount');

	// Notifications
 	Route::get('notifications','UserController@notifications');
	Route::post('notifications/settings','UserController@settingsNotifications');
	Route::post('notifications/delete','UserController@deleteNotifications');

	// Messages
	Route::get('messages', 'MessagesController@inbox');
	// Message Chat
	Route::get('messages/{id}/{username?}', 'MessagesController@messages')->where(array('id' => '[0-9]+'));
	Route::get('messages/old/{id}/{username?}', 'MessagesController@messagesOld')->where(array('id' => '[0-9]+'));
	Route::get('loadmore/messages', 'MessagesController@loadmore');
	Route::post('message/send', 'MessagesController@send');
	Route::get('messages/search/creator', 'MessagesController@searchCreator');
	Route::post('message/delete', 'MessagesController@delete');
	Route::get('messages/ajax/chat', 'MessagesController@ajaxChat');
	Route::post('conversation/delete/{id}', 'MessagesController@deleteChat');
	Route::get('load/chat/ajax/{id}', 'MessagesController@loadAjaxChat');

	// Upload Avatar
	Route::post('upload/avatar','UserController@uploadAvatar');

	// Upload Cover
	Route::post('upload/cover','UserController@uploadCover');

 	// Password
 	Route::get('settings/password','UserController@password');
 	Route::post('settings/password','UserController@updatePassword');

 	// My subscribers
 	Route::get('my/subscribers','UserController@mySubscribers');

	// My subscriptions
 	Route::get('my/subscriptions','UserController@mySubscriptions');
	Route::post('subscription/cancel/{id}','UserController@cancelSubscription');

	// My channels
	Route::get('my/channels','ChannelsController@channels');
	Route::get('my/channel/add','ChannelsController@addChannel');
	Route::post('my/channel/store','ChannelsController@storeChannel');
	Route::get('my/channel/{id}/edit','ChannelsController@editChannel');
	Route::post('my/channel/{id}/update','ChannelsController@updateChannel');
	Route::get('my/channel/{id}/delete','ChannelsController@deleteChannel');

	// My payments
	Route::get('my/payments','UserController@myPayments');
	Route::get('my/payments/received','UserController@myPayments');
	Route::get('my/payments/invoice/{id}','UserController@invoice');

	// Payout Method
 	Route::get('settings/payout/method','UserController@payoutMethod');
	Route::post('settings/payout/method/{type}','UserController@payoutMethodConfigure');

	// Withdrawals
 	Route::get('settings/withdrawals','UserController@withdrawals');
	Route::post('settings/withdrawals','UserController@makeWithdrawals');
	Route::post('delete/withdrawal/{id}','UserController@deleteWithdrawal');

 	// Upload Avatar
 	Route::post('upload/avatar','UserController@uploadAvatar');

	// Updates
	Route::post('update/create','UpdatesController@create');
	Route::get('update/edit/{id}','UpdatesController@edit');
	Route::post('update/edit','UpdatesController@postEdit');
	Route::post('update/delete/{id}','UpdatesController@delete');

	// courses

     Route::get('course/dashboard','CourseContentController@dashboard');
     Route::get('course/{id}/email-flow','CourseContentController@getUpdateEmailFlows');
     Route::post('course/{id}/email-flow','CourseContentController@setUpdateEmailFlow')->name("set-email-flow");
     Route::post('course/{id}/update-email-flow-interval','CourseContentController@updateFlowDays')->name("update-flow-interval");

     Route::get('create/course','CourseContentController@index');
     Route::post('create/course','CourseContentController@create');
     Route::get('course/{id}','CourseContentController@viewCourse')->name("view-course");
     Route::get('course/{id}/edit','CourseContentController@viewEditCourse')->name("edit-course");
     Route::post('course/ajax/media-view','CourseContentController@mediaViewAjaxCreateForEmbed');
     Route::get('course/ajax/media-view','CourseContentController@mediaView');
     Route::get('course/ajax/course-completion','CourseContentController@courseCompletion');
     Route::post('course/ajax/complete','CourseContentController@complete');
     Route::get('course-diploma/{id}','CourseContentController@courseDiploma')->name("view-course-diploma");
     Route::get('course-completion/{id}','CourseContentController@courseCompletionPage')->name("view-course-completion");


     Route::get('my-courses','CourseContentController@myCourses');
     Route::get('manage-courses','CourseContentController@myCourses');
     
     Route::post('change-lessons-order-numbers','CourseContentController@changeLessonsOrderNumbers');
     
     Route::post('course-invitation-step2','CourseContentController@CourseInvitationRegisterStep2');
     Route::post('course-invitation-get-taxes','CourseContentController@CourseInvitationGetTaxes');
     



//	Route::post('update/create','UpdatesController@create');
//	Route::post('update/edit','UpdatesController@postEdit');
//	Route::post('update/delete/{id}','UpdatesController@delete');

	// Report Update
	Route::post('report/update/{id}','UpdatesController@report');

	// Report Creator
	Route::post('report/creator/{id}','UserController@reportCreator');

	//========================== Update's categories ================================//
	Route::post('updatecategory/create','UpdatesController@createUpdateCategory');

	//======================================= STRIPE ================================//
	Route::get("settings/payments/card", 'UserController@formAddUpdatePaymentCard');
	Route::post("settings/payments/card", 'UserController@addUpdatePaymentCard');
	Route::post("stripe/delete/card", 'UserController@deletePaymentCard');


	//======================================= Paystack ================================//
	Route::post("paystack/card/authorization", 'PaystackController@cardAuthorization');
	Route::get("paystack/card/authorization/verify", 'PaystackController@cardAuthorizationVerify');
	Route::post("paystack/delete/card", 'PaystackController@deletePaymentCard');

	// Cancel Subscription Paystack
	Route::post('subscription/paystack/cancel/{id}','PaystackController@cancelSubscription');

	// Cancel Subscription Wallet
	Route::post('subscription/wallet/cancel/{id}','SubscriptionsController@cancelWalletSubscription');

	// Pin Post
	Route::post('pin/post','UpdatesController@pinPost');

	// Dark Mode
	Route::get('mode/{mode}','HomeController@darkMode')->where('mode', '(dark|light)$');

	// Bookmarks
	Route::post('ajax/bookmark','HomeController@addBookmark');
	Route::get('my/bookmarks','UserController@myBookmarks');
	Route::get('ajax/user/bookmarks', 'UpdatesController@ajaxBookmarksUpdates');
	Route::get('my/purchases','UserController@myPurchases');
	Route::get('ajax/user/purchases', 'UserController@ajaxMyPurchases');

	// Downloads Files
	Route::get('download/file/{id}','UserController@downloadFile');

	// Downloads Files
	Route::get('download/message/file/{id}','MessagesController@downloadFileZip');

	// My Wallet
 	Route::get('my/wallet', 'AddFundsController@wallet');
	Route::get('deposits/invoice/{id}','UserController@invoiceDeposits');

	// My Cards
	Route::get('my/cards', 'UserController@myCards');

	// Add Funds
	Route::post('add/funds', 'AddFundsController@send');

	// Send Tips
	Route::post('send/tip', 'TipController@send');

	// Pay Per Views
	Route::post('send/ppv', 'PayPerViewController@send');
	
	Route::post('send/ppv_demo', 'PayPerViewController@send_demo')->name('ppv_demo');
	

	// mollie after content purchase callback url
	Route::get('mollie-content-purchase-callback','PayPerViewController@mollieContentPurchaseCallBack')->name('mollie-content-purchase-callback');

	

	// Explore
	Route::get('explore','UpdatesController@explore');
	Route::get('ajax/explore', 'UpdatesController@ajaxExplore');

 });//<------ End User Views LOGGED

// Private content
Route::group(['middleware' => 'private.content'], function() {

	// Creators
	Route::get('creators/{type?}','HomeController@creators');

	// Category
	Route::get('category/{slug}/{type?}','HomeController@category')->name('seo');

	// Profile User
	Route::get('{slug}', 'UserController@profile')->where('slug','[A-Za-z0-9\_-]+')->name('profile');
	Route::get('{slug}/{media}', 'UserController@profile')->where('media', '(photos|videos|audio|shop|files|courses)$')->name('profile');

	// Profile User
	Route::get('{slug}/post/{id}', 'UserController@postDetail')->where('slug','[A-Za-z0-9\_-]+')->name('profile');

});//<------ Private content


 /*
  |-----------------------------------
  | Admin Panel
  |--------- -------------------------
  */
 Route::group(['middleware' => 'role'], function() {

     // Upgrades
 	Route::get('update/{version}','UpgradeController@update');

 	// Dashboard
 	Route::get('panel/admin','AdminController@admin')->name('dashboard');

 	// Settings
 	Route::get('panel/admin/settings','AdminController@settings')->name('general');
 	Route::post('panel/admin/settings','AdminController@saveSettings');

	// Limits
	Route::get('panel/admin/settings/limits','AdminController@settingsLimits')->name('general');
	Route::post('panel/admin/settings/limits','AdminController@saveSettingsLimits');

	// BILLING
	Route::view('panel/admin/billing','admin.billing')->name('billing');
	Route::post('panel/admin/billing','AdminController@billingStore');

	// EMAIL SETTINGS
	Route::view('panel/admin/settings/email','admin.email-settings')->name('email');
	Route::post('panel/admin/settings/email','AdminController@emailSettings');

	// STORAGE
	Route::view('panel/admin/storage','admin.storage')->name('storage');
	Route::post('panel/admin/storage','AdminController@storage');

	// THEME
	Route::get('panel/admin/theme','AdminController@theme')->name('theme');
	Route::post('panel/admin/theme','AdminController@themeStore');

 	//Withdrawals
 	Route::get('panel/admin/withdrawals','AdminController@withdrawals')->name('withdrawals');
 	Route::get('panel/admin/withdrawal/{id}','AdminController@withdrawalsView')->name('withdrawals');
 	Route::post('panel/admin/withdrawals/paid/{id}','AdminController@withdrawalsPaid');

 	// Subscriptions
 	Route::get('panel/admin/subscriptions','AdminController@subscriptions')->name('subscriptions');

	// Transactions
	Route::get('panel/admin/transactions','AdminController@transactions')->name('transactions');
	Route::post('panel/admin/transactions/cancel/{id}','AdminController@cancelTransaction');
	Route::post('panel/admin/ajax/transactions','AdminController@transactionMap')->name("filter-transactions");

 	// Members
	Route::get('panel/admin/members','AdminController@index')->name('members');

	// EDIT MEMBER
	Route::get('panel/admin/members/edit/{id}','AdminController@edit')->name('members');

	// EDIT MEMBER POST
	Route::post('panel/admin/members/edit/{id}', 'AdminController@update');

	// DELETE MEMBER
	Route::post('panel/admin/members/{id}', 'AdminController@destroy');

 	// Pages
	Route::get('panel/admin/pages','PagesController@index')->name('pages');

	// ADD NEW PAGES
	Route::get('panel/admin/pages/create','PagesController@create')->name('pages');

	// ADD NEW PAGES POST
	Route::post('panel/admin/pages/create','PagesController@store');

	// EDIT PAGES
	Route::get('panel/admin/pages/edit/{id}','PagesController@edit')->name('pages');

	// EDIT PAGES POST
	Route::post('panel/admin/pages/edit/{id}', 'PagesController@update');

	// DELETE PAGES
	Route::post('panel/admin/pages/{id}', 'PagesController@destroy');

	// Verification Requests
 	Route::get('panel/admin/verification/members','AdminController@memberVerification')->name('verification_requests');
 	Route::post('panel/admin/verification/members/{action}/{id}/{user}','AdminController@memberVerificationSend');

 	// Payments Settings
 	Route::get('panel/admin/payments','AdminController@payments')->name('payments');
 	Route::post('panel/admin/payments','AdminController@savePayments');

	Route::get('panel/admin/payments/{id}','AdminController@paymentsGateways')->name('payments');
	Route::post('panel/admin/payments/{id}','AdminController@savePaymentsGateways');

 	// Profiles Social
 	Route::get('panel/admin/profiles-social','AdminController@profiles_social')->name('profiles_social');
 	Route::post('panel/admin/profiles-social','AdminController@update_profiles_social');

 	// Categories
 	Route::get('panel/admin/categories','AdminController@categories')->name('categories');
 	Route::get('panel/admin/categories/add','AdminController@addCategories')->name('categories');
 	Route::post('panel/admin/categories/add','AdminController@storeCategories');
 	Route::get('panel/admin/categories/edit/{id}','AdminController@editCategories')->name('categories');
 	Route::post('panel/admin/categories/update','AdminController@updateCategories');
 	Route::post('panel/admin/categories/delete/{id}','AdminController@deleteCategories');

	// Posts
 	Route::get('panel/admin/posts','AdminController@posts')->name('posts');
	Route::post('panel/admin/posts/delete/{id}','AdminController@deletePost');

	// Approve post
	Route::post('panel/admin/posts/approve/{id}','AdminController@approvePost');

	// Reports
 	Route::get('panel/admin/reports','AdminController@reports')->name('reports');
	Route::post('panel/admin/reports/delete/{id}','AdminController@deleteReport');

	// Social Login
	Route::view('panel/admin/social-login','admin.social-login')->name('social_login');
	Route::post('panel/admin/social-login','AdminController@updateSocialLogin');

	// Google
	Route::get('panel/admin/google','AdminController@google')->name('google');
	Route::post('panel/admin/google','AdminController@update_google');

	//***** Languages
	Route::get('panel/admin/languages','LangController@index')->name('languages');

	// ADD NEW
	Route::get('panel/admin/languages/create','LangController@create')->name('languages');

	// ADD NEW POST
	Route::post('panel/admin/languages/create','LangController@store');

	// EDIT LANG
	Route::get('panel/admin/languages/edit/{id}','LangController@edit')->name('languages');

	// EDIT LANG POST
	Route::post('panel/admin/languages/edit/{id}', 'LangController@update');

	// DELETE LANG
	Route::post('panel/admin/languages/{id}', 'LangController@destroy');

	// Maintenance mode
	Route::view('panel/admin/maintenance/mode','admin.maintenance_mode')->name('maintenance_mode');
	Route::post('panel/admin/maintenance/mode','AdminController@maintenanceMode');

	Route::post("ajax/upload/image", "AdminController@uploadImageEditor")->name("upload.image");

	// Blog
	Route::get('panel/admin/blog','AdminController@blog')->name('blog');
  Route::post('panel/admin/blog/delete/{id}','AdminController@deleteBlog');

  // Add Blog Post
  Route::view('panel/admin/blog/create','admin.create-blog')->name('blog');
	Route::post('panel/admin/blog/create','AdminController@createBlogStore');

  // Edit Blog Post
  Route::get('panel/admin/blog/{id}','AdminController@editBlog')->name('blog');
	Route::post('panel/admin/blog/update','AdminController@updateBlog');

	// Resend confirmation email
	Route::get('panel/admin/resend/email/{id}','AdminController@resendConfirmationEmail')->name('members');

	// Deposits
	Route::get('panel/admin/deposits','AdminController@deposits')->name('deposits');
	Route::get('panel/admin/deposits/{id}','AdminController@depositsView')->name('deposits');
	Route::post('approve/deposits','AdminController@approveDeposits');
	Route::post('delete/deposits','AdminController@deleteDeposits');

	// Login as User
	Route::post('panel/admin/login/user/{id}','AdminController@loginAsUser');

	// Custom CSS/JS
  Route::view('panel/admin/custom-css-js','admin.css-js')->name('custom_css_js');
	Route::post('panel/admin/custom-css-js','AdminController@customCssJs');

	// PWA
  Route::view('panel/admin/pwa','admin.pwa')->name('pwa');
	Route::post('panel/admin/pwa','AdminController@pwa');

	// Role and permissions
	Route::get('panel/admin/members/roles-and-permissions/{id}','AdminController@roleAndPermissions')->name('members');
	Route::post('panel/admin/members/roles-and-permissions/{id}','AdminController@storeRoleAndPermissions');



     // My payments
     Route::get('panel/admin/invoices','AdminController@lookupPayments');
     Route::get('panel/admin/invoices/{id}','AdminController@invoice');
     Route::get('panel/admin/invoices/{dateStart}/{dateEnd}/{countryCode}','AdminController@lookupTransactionsByFilter');

 });
 //==== End Panel Admin

 // Installer Script
 Route::get('install/script','InstallScriptController@requirements');
 Route::get('install/script/database','InstallScriptController@database');
 Route::post('install/script/database','InstallScriptController@store');

// Install Controller (Add-on)
 Route::get('install/{addon}','InstallController@install');

 // Payments Gateways
 Route::get('payment/paypal', 'PayPalController@show')->name('paypal');

 Route::get('payment/stripe', 'StripeController@show')->name('stripe');
 Route::post('payment/stripe/charge', 'StripeController@charge');

// Files Images Post
Route::get('files/storage/{id}/{path}', 'UpdatesController@image')->where(['id' =>'[0-9]+', 'path' => '.*']);

// Files Images Messages
Route::get('files/messages/{id}/{path}', 'UpdatesController@messagesImage')->where(['id' =>'[0-9]+', 'path' => '.*']);

Route::get('lang/{id}', function($id) {

	$lang = App\Models\Languages::where('abbreviation', $id)->firstOrFail();

	Session::put('locale', $lang->abbreviation);

   return back();

})->where(array( 'id' => '[a-z]+'));

// Sitemaps
Route::get('sitemaps.xml', function() {
 return response()->view('index.sitemaps')->header('Content-Type', 'application/xml');
});

// Search Creators
Route::get('search/creators', 'HomeController@searchCreator');

// Explore Creators refresh
Route::post('refresh/creators', 'HomeController@refreshCreators');

Route::get('payment/paystack', 'PaystackController@show')->name('paystack');

Route::get('payment/ccbill', 'CCBillController@show')->name('ccbill');

//======= v2.4
Route::get('file/verification/{filename}', 'AdminController@getFileVerification')->middleware('role');

Route::get('file/media/{typeMedia}/{fileId}/{filename}', 'UpdatesController@getFileMedia');

Route::any('upload/media','UploadMediaController@store');
Route::post('delete/media','UploadMediaController@delete');

Route::any('upload/media/message','UploadMediaMessageController@store');
Route::post('delete/media/message','UploadMediaMessageController@delete');

Route::post('new/message/massive', 'MessagesController@sendMessageMassive');

Route::any('coinpayments/ipn', 'AddFundsController@coinPaymentsIPN')->name('coinpaymentsIPN');
Route::get('wallet/payment/success', 'AddFundsController@paymentProcess')->name('paymentProcess');

Route::view('panel/admin/announcements','admin.announcements')->name('announcements')->middleware('role');
Route::post('panel/admin/announcements', 'AdminController@storeAnnouncements')->middleware('role');

Route::get('media/storage/focus/{type}/{path}', 'UpdatesController@imageFocus')->where(['type' => '(video|photo|message)$', 'path' => '.*']);

// Comment Like
Route::post('comment/like','CommentsController@like')->middleware('auth');

// v2.6
Route::get('my/posts','UserController@myPosts')->middleware('auth');
Route::get('block/countries','UserController@blockCountries')->middleware('auth');
Route::post('block/countries','UserController@blockCountriesStore')->middleware('auth');

// v2.7
Route::get('my/referrals','UserController@myReferrals')->middleware('auth');
Route::post('verify/2fa', 'TwoFactorAuthController@verify');
Route::post('2fa/resend', 'TwoFactorAuthController@resend');

// v2.9
Route::view('panel/admin/live-streaming','admin.live_streaming')->name('live_streaming')->middleware('role');
Route::post('panel/admin/live-streaming', 'AdminController@saveLiveStreaming')->middleware('role');

Route::post('create/live', 'LiveStreamingsController@create');
Route::post('finish/live', 'LiveStreamingsController@finish');

Route::get('live/{username}','LiveStreamingsController@show')->name('live');
Route::get('get/data/live', 'LiveStreamingsController@getDataLive')->name('live.data')->middleware('live');
Route::post('end/live/stream/{id}', 'LiveStreamingsController@finish');
Route::post('send/payment/live', 'LiveStreamingsController@paymentAccess');
Route::post('comment/live', 'LiveStreamingsController@comments');
Route::get('explore/creators/live','HomeController@creatorsBroadcastingLive');
Route::post('live/like','LiveStreamingsController@like');

// v3.0
Route::get('panel/admin/tax-rates','TaxRatesController@show')->name('tax')->middleware('role');
Route::view('panel/admin/tax-rates/add', 'admin.add-tax')->name('tax')->middleware('role');
Route::post('panel/admin/tax-rates/add', 'TaxRatesController@store')->middleware('role');
Route::get('panel/admin/tax-rates/edit/{id}', 'TaxRatesController@edit')->name('tax')->middleware('role');
Route::post('panel/admin/tax-rates/update', 'TaxRatesController@update')->middleware('role');
Route::post('panel/admin/ajax/states', 'TaxRatesController@getStates')->middleware('role');

Route::get('panel/admin/countries', 'CountriesStatesController@countries')->name('countries_states')->middleware('role');
Route::view('panel/admin/countries/add', 'admin.add-country')->name('countries_states')->middleware('role');
Route::post('panel/admin/countries/add', 'CountriesStatesController@addCountry')->middleware('role');
Route::get('panel/admin/countries/edit/{id}', 'CountriesStatesController@editCountry')->name('countries_states')->middleware('role');
Route::post('panel/admin/countries/update', 'CountriesStatesController@updateCountry')->middleware('role');
Route::post('panel/admin/countries/delete/{id}', 'CountriesStatesController@deleteCountry')->middleware('role');

Route::get('panel/admin/states', 'CountriesStatesController@states')->name('countries_states')->middleware('role');
Route::view('panel/admin/states/add', 'admin.add-state')->name('countries_states')->middleware('role');
Route::post('panel/admin/states/add', 'CountriesStatesController@addState')->middleware('role');
Route::get('panel/admin/states/edit/{id}', 'CountriesStatesController@editState')->name('countries_states')->middleware('role');
Route::post('panel/admin/states/update', 'CountriesStatesController@updateState')->middleware('role');
Route::post('panel/admin/states/delete/{id}', 'CountriesStatesController@deleteState')->middleware('role');

// v3.4
Route::get('panel/admin/referrals','AdminController@referrals')->name('referrals')->middleware('role');

Route::view('panel/admin/shop','admin.shop')->name('shop')->middleware('role');
Route::post('panel/admin/shop', 'AdminController@shopStore')->middleware('role');

Route::get('panel/admin/products','AdminController@products')->name('products')->middleware('role');
Route::post('panel/admin/product/delete/{id}','AdminController@productDelete')->middleware('role');

Route::get('add/product','ProductsController@create')->middleware('auth');
Route::post('add/product','ProductsController@store')->middleware('auth');

Route::get('add/custom/content','ProductsController@createCustomContent')->middleware('auth');
Route::post('add/custom/content','ProductsController@storeCustomContent')->middleware('auth');

Route::post('edit/product/{id}','ProductsController@update')->middleware('auth');

Route::post('delete/product/{id}','ProductsController@destroy')->middleware('auth');

Route::any('upload/media/shop/preview','UploadMediaPreviewShopController@store');
Route::post('delete/media/shop/preview','UploadMediaPreviewShopController@delete');

Route::any('upload/media/shop/file','UploadMediaFileShopController@store');
Route::post('delete/media/shop/file','UploadMediaFileShopController@delete');

Route::post('buy/now/product','ProductsController@buy')->middleware('auth');
Route::get('product/download/{id}','ProductsController@download')->middleware('auth');
Route::post('delivered/product/{id}','ProductsController@deliveredProduct')->middleware('auth');

Route::get('my/purchased/items','UserController@purchasedItems')->middleware('auth');
Route::get('my/sales','UserController@mySales')->middleware('auth');
Route::get('my/products','UserController@myProducts')->middleware('auth');

Route::get('mercadopado/process', 'AddFundsController@mercadoPagoProcess')->name('mercadopadoProcess')->middleware('auth');

Route::get('flutterwave/callback', 'AddFundsController@flutterwaveCallback')->name('flutterwaveCallback')->middleware('auth');

Route::post('webhook/mollie', 'AddFundsController@webhookMollie');

// v3.7
Route::post('reject/order/{id}','ProductsController@rejectOrder')->middleware('auth');
Route::get('ajax/mentions', 'UserController@mentions');

Route::get('panel/admin/sales','AdminController@sales')->name('sales')->middleware('role');
Route::post('panel/admin/sales/refund/{id}','AdminController@salesRefund')->middleware('role');

// Stripe Connect
Route::get('stripe/connect', 'StripeConnectController@redirectToStripe')->name('redirect.stripe');
Route::get('connect/{token}', 'StripeConnectController@saveStripeAccount')->name('save.stripe');

