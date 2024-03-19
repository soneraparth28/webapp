<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveStreamings;
use App\Models\LiveOnlineUsers;
use App\Models\LiveComments;
use App\Models\LiveLikes;
use App\Models\AdminSettings;
use App\Events\LiveBroadcasting;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Helper;
use RtcTokenBuilder;

class LiveStreamingsController extends Controller
{
  use Traits\Functions;

  public function __construct(Request $request, AdminSettings $settings) {
    $this->request = $request;
    $this->settings = $settings::first();
    $this->middleware('auth');
  }

  // Create live Stream
  public function create()
  {
    // Currency Position
    if ($this->settings->currency_position == 'right') {
      $currencyPosition =  2;
    } else {
      $currencyPosition =  null;
    }

    $messages = [
      'price.min' => trans('general.amount_minimum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'price.max' => trans('general.amount_maximum'.$currencyPosition, ['symbol' => $this->settings->currency_symbol, 'code' => $this->settings->currency_code]),
      'price.required_if' => trans('validation.required')
  ];

    $validator = Validator::make($this->request->all(), [
        'name' => 'required|max:50',
        'price' => 'required_if:availability,all_pay|integer|min:'.$this->settings->live_streaming_minimum_price.'|max:'.$this->settings->live_streaming_max_price,
//        'price' => 'required_if:availability,all_pay,free_paid_subscribers|integer|min:'.$this->settings->live_streaming_minimum_price.'|max:'.$this->settings->live_streaming_max_price,
    ], $messages);

    if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }


        // Create Live Stream
        $live          = new LiveStreamings();
        $live->user_id = auth()->id();
        $live->name    = $this->request->name;
        $live->channel = 'live_'.str_random(5) .'_'.auth()->id();
        $live->price   = $this->request->price ?? 0;
        $live->availability = $this->request->availability;
        $live->save();

       // Notify to subscribers
       event(new LiveBroadcasting(auth()->user(), $live->id));

       return response()->json([
         'success' => true,
         'url' => url('live', auth()->user()->username)
       ]);
  }// End method create


  // End Live Stream
  public function finish($id)
  {
    if (! $this->request->expectsJson()) {
        abort(404);
    }

    $live = LiveStreamings::whereId($id)
        ->whereUserId(auth()->id())
        ->firstOrFail();
    $live->status = '1';
    $live->save();

    return response()->json([
      'success' => true
    ]);
  }// End method finish

  // Show Live Stream
  public function show()
  {
    // Live Streaming OFF
    if ($this->settings->live_streaming_status == 'off') {
      return redirect('/');
    }

    // Find Creator
    $creator = User::whereUsername($this->request->username)
    ->whereVerifiedId('yes')
    ->firstOrFail();

    // Hidden Live Blocked Countries
    if (in_array(Helper::userCountry(), $creator->blockedCountries())
        && auth()->check()
        && auth()->user()->permission != 'all'
        && auth()->id() != $creator->id
        || auth()->guest()
        && in_array(Helper::userCountry(), $creator->blockedCountries())
      ) {
        abort(404);
      }

    // Search last Live Streaming
    $live = LiveStreamings::whereUserId($creator->id)
    ->where('updated_at', '>', now()->subMinutes(5))
    ->whereStatus('0')
    ->orderBy('id', 'desc')
    ->first();

    // Check subscription
    $checkSubscription = auth()->user()->checkSubscription($creator);

    // Free for paying subscribers
    if ($live
        && $checkSubscription
        && $creator->id != auth()->id()
        && $live->availability == 'free_paid_subscribers'
      ) {
          LiveOnlineUsers::firstOrCreate([
          'user_id' => auth()->id(),
          'live_streamings_id' => $live->id
        ]);

        // Inser Comment Joined User
        LiveComments::firstOrCreate([
        'user_id' => auth()->id(),
        'live_streamings_id' => $live->id
      ]);
    }

    // Free for everyone
    if ($live
        && $creator->id != auth()->id()
        && $live->availability == 'everyone_free'
      ) {
          LiveOnlineUsers::firstOrCreate([
          'user_id' => auth()->id(),
          'live_streamings_id' => $live->id
        ]);

        // Inser Comment Joined User
        LiveComments::firstOrCreate([
        'user_id' => auth()->id(),
        'live_streamings_id' => $live->id
      ]);
    }

    // Check User Online (Already paid)
    if ($live) {
      $userPaidAccess = LiveOnlineUsers::whereUserId(auth()->id())
          ->whereLiveStreamingsId($live->id)
          ->first();

      $likes = $live->likes->count();
      $likeActive = $live->likes()->whereUserId(auth()->id())->first();

      if ($userPaidAccess) {
        $userPaidAccess->updated_at = now();
        $userPaidAccess->update();
      }
    }

    // Payment Access
    if ($live && $creator->id == auth()->id()) {
      $paymentRequiredToAccess = false;
    } elseif ($live && $userPaidAccess) {
      $paymentRequiredToAccess = false;
    } else {
      $paymentRequiredToAccess = true;
    }

    if ($live && $this->settings->limit_live_streaming_paid != 0 && $live->availability != 'everyone_free') {
      $limitLiveStreaming = $this->settings->limit_live_streaming_paid - $live->TimeElapsed;
    } elseif ($live && $this->settings->limit_live_streaming_free != 0 && $live->availability == 'everyone_free') {
      $limitLiveStreaming = $this->settings->limit_live_streaming_free - $live->TimeElapsed;
    } else {
      $limitLiveStreaming = false;
    }

    // --------------------------------------------------------------------------
    // ---------------- Agora Token Generation Work Start------------------------
    // --------------------------------------------------------------------------

    if(empty($live)){
        return redirect('/');
    }

    $live->agora_token = $this->generateAgoraToken($live->channel, auth()->id(), $creator->id == auth()->id());


    // --------------------------------------------------------------------------
    // ----------------- Agora Token Generation Work End-------------------------
    // --------------------------------------------------------------------------

    return view('users.live', [
      'creator' => $creator,
      'live' => $live,
      'checkSubscription' => $checkSubscription,
      'comments' => $live->comments ?? null,
      'likes' => $likes ?? null,
      'likeActive' => $likeActive ?? null,
      'paymentRequiredToAccess' => $paymentRequiredToAccess,
      'limitLiveStreaming' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0
    ]);

  }// End method show

  public function getDataLive()
  {
    if (! auth()->check()) {
      return response()->json([
        'session_null' => true
      ]);
    }

    // Find Live Streaming
    $live = LiveStreamings::whereId($this->request->live_id)
    ->whereUserId($this->request->creator)
    ->where('updated_at', '>', now()->subMinutes(5))
    ->whereStatus('0')
    ->first();

    // Limit Live Streaming (time)
    if ($live && $this->settings->limit_live_streaming_paid != 0 && $live->availability != 'everyone_free') {
      $limitLiveStreaming = $this->settings->limit_live_streaming_paid - $live->TimeElapsed;
    } elseif ($live && $this->settings->limit_live_streaming_free != 0 && $live->availability == 'everyone_free') {
      $limitLiveStreaming = $this->settings->limit_live_streaming_free - $live->TimeElapsed;
    } else {
      $limitLiveStreaming = false;
    }

    $status = $live ? 'online' : 'offline';

    if ($status == 'offline' || $limitLiveStreaming && $limitLiveStreaming <= 0) {

      if ($live && $limitLiveStreaming && $limitLiveStreaming <= 0) {
          $live->status = '1';
          $live->save();
      }

      return response()->json([
        'success' => true,
        'total' => null,
        'comments' => [],
        'onlineUsers' => 0,
        'status' => 'offline'
      ]);
    }

    // Online users
    $onlineUsers = $live->onlineUsers->count();

    // Comments
    $comments = $live->comments()
    ->where('id', '>', $this->request->get('last_id'))
    ->get();

    $totalComments = $comments->count();
    $allComments = array();

    if ($totalComments != 0) {

      foreach ($comments as $comment) {

      $allComments[] = view('includes.comments-live', [
          'comments' => $comments
          ])->render();

      }//<--- foreach
    }//<--- IF != 0

    // Likes
    $likes = $live->likes->count();

    return response()->json([
      'success' => true,
      'comments' => $allComments,
      'likes' => Helper::formatNumber($likes),
      'onlineUsers' => Helper::formatNumber($onlineUsers),
      'status' => $status,
      'total' => $totalComments,
      'time' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0,
    ]);

  }// End method getDataLive







  public function paymentAccess()
  {
    // Verify that the user has not paid
    if (LiveOnlineUsers::whereUserId(auth()->id())
        ->whereLiveStreamingsId($this->request->id)
        ->first())
        {
          return response()->json([
            "success" => false,
            "errors" => ['error' => trans('general.already_payment_live_access')]
          ]);
        }

    // Find live exists
    $live = LiveStreamings::whereId($this->request->id)
    ->where('updated_at', '>', now()->subMinutes(5))
    ->whereStatus('0')
    ->firstOrFail();


    $messages = [
      'payment_gateway_live.required' => trans('general.choose_payment_gateway')
    ];
  $validator = Validator::make($this->request->all(), ['payment_gateway_live' => 'required'], $messages);
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray(),
        ]);
    }


    $details = [];
    switch ($this->request->payment_gateway_live) {
        default:
            return response()->json([
                "success" => false,
                "errors" => ['error' => "Unknown payment method"]
            ]);

        case "wallet":
            $details["gateway"] = "wallet";
            $details["type"] = "live";
            $details["amount"] = $live->price;
            $details["amount_ex_vat"] = $live->price;
            $details["is_incl_vat"] = false;
            $details["buyer_id"] = auth()->id();
            $details["seller_id"] = $live->user()->id;
            $details["subscription_id"] = 0;
            $details["taxes"] = auth()->user()->isTaxable();
            $details["txnId"] = $details["type"] . '_' . str_random(25);

            $res = PayPerViewController::handleTransaction(["request" => $this->request, "settings" => $this->settings, "data" => $details]);
            if($res !== true) return $res;
            break;

        case "card":
            $details["amount"] = $live->price;
            $details["is_incl_vat"] = false;
            $details["buyer_id"] = auth()->id();
            $details["seller_id"] = $live->user()->id;
            $details["taxes"] = auth()->user()->isTaxable();
            $details["description"] = "Live stream access purchase";
            $details["metadata"] = [
                "amount" => $live->price,
                "is_incl_vat" => false,
                "buyer_id" => $details["buyer_id"],
                "seller_id" => $details["seller_id"],
                "type" => "livestream",
                "livestream_id" => $this->request->id,
                "redirect_uri" => url("live/" . $live->user()->username),
                "subscription_id" => 0,
            ];
            return PayPerViewController::makePayment(["data" => $details]);
    }


    self::addAudienceUserToLiveStream($live->id, auth()->id());

        return response()->json([
          "success" => true,
        ]);

  }// End method paymentAccess




    public static function addAudienceUserToLiveStream($streamId, $userId): void {
        $sql = new LiveOnlineUsers();
        $sql->user_id = $userId;
        $sql->live_streamings_id = $streamId;
        $sql->save();

        $sql            = new LiveComments();
        $sql->user_id   = $userId;
        $sql->live_streamings_id = $streamId;
        $sql->save();
    }

    public static function addTipCommentToLiveStream($streamId, $userId, $amount): void {
        $sql = new LiveComments();
        $sql->user_id = $userId;
        $sql->live_streamings_id = $streamId;
        $sql->joined = 0;
        $sql->tip = '1';
        $sql->tip_amount = $amount;
        $sql->save();
    }









  // Comments
  public function comments()
  {
    // Find Live Streaming
    $live = LiveStreamings::whereId($this->request->live_id)
    ->where('updated_at', '>', now()->subMinutes(5))
    ->whereStatus('0')
    ->firstOrFail();

    $messages = [
      'comment.required' => trans('general.please_write_something'),
    ];

  //<---- Validation
  $validator = Validator::make($this->request->all(), [
      'comment' =>  'required|max:100|min:1',
      ], $messages);

    if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

   $sql            = new LiveComments();
   $sql->user_id   = auth()->id();
   $sql->live_streamings_id = $live->id;
   $sql->comment   = trim(Helper::checkTextDb($this->request->comment));
   $sql->joined    = 0;
   $sql->save();

   return response()->json([
     'success' => true,
   ]);

 }//<--- End Method

 public function like()
 {
   // Find Live Streaming
   $live = LiveStreamings::whereId($this->request->id)
   ->where('updated_at', '>', now()->subMinutes(5))
   ->whereStatus('0')
   ->firstOrFail();

   $like = LiveLikes::firstOrNew([
     'user_id' => auth()->id(),
     'live_streamings_id' => $this->request->id
   ]);

   if ($like->exists) {
       $like->delete();
   } else {
     $like->save();
   }

   $likes = $live->likes->count();

   return response()->json([
     'success' => true,
     'likes' => $likes
   ]);

 }//<---- End Method



    public function generateAgoraToken($channelName, $uid, $host): string {
        $appID = $this->settings->agora_app_id;
        $appCertificate = "b60b6a06ba2148fb866272381476b853";
        $uid = (int)$uid;

        include_once(__DIR__ . "/../../../agoratoken/RtcTokenBuilder.php");
        $role = !$host ? RtcTokenBuilder::RoleSubscriber : RtcTokenBuilder::RoleAttendee;

        $expireTimeInSeconds = 3600 * 24;
        $privilegeExpiredTs = time() + $expireTimeInSeconds;
        return RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    }

}// End Class
