<?php /** @noinspection ALL */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Functions;
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

class LiveStreamingsController extends Controller
{
    use Functions;
    public function __construct(Request $request, AdminSettings $settings) {
        $this->request = $request;
        $this->settings = $settings::first();
    }
    public function show()
    {
        // Live Streaming OFF
        if ($this->settings->live_streaming_status == 'off') {
            $response = [
                'success' => false,
                'data' => [
                    'redirect_url' => '/',
                ],
                'message' => 'Live Stream is Off.',
            ];
            return response()->json($response , 400);
//            return redirect('/');
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
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Error Occured.',
            ];
            return response()->json($response , 404);
//            abort(404);
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
            && $checkSubscription->free == 'no'
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
        $response = [
            'success' => true,
            'data' => [
                'creator' => $creator,
                'live' => $live,
                'checkSubscription' => $checkSubscription,
                'comments' => $live->comments ?? null,
                'likes' => $likes ?? null,
                'likeActive' => $likeActive ?? null,
                'paymentRequiredToAccess' => $paymentRequiredToAccess,
                'limitLiveStreaming' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0
            ],
            'message' => 'Live Stream Data.',
        ];
        return response()->json($response , 200);
//        return view('users.live', [
//            'creator' => $creator,
//            'live' => $live,
//            'checkSubscription' => $checkSubscription,
//            'comments' => $live->comments ?? null,
//            'likes' => $likes ?? null,
//            'likeActive' => $likeActive ?? null,
//            'paymentRequiredToAccess' => $paymentRequiredToAccess,
//            'limitLiveStreaming' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0
//        ]);
    }
    public function getDataLive()
    {
        if (! auth()->check()) {
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'Session Null.',
            ];
            return response()->json($response , 400);
//            return response()->json([
//                'session_null' => true
//            ]);
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
            $response = [
                'success' => true,
                'data' => [
                    'total' => null,
                    'comments' => [],
                    'onlineUsers' => 0,
                    'status' => 'offline'
                ],
                'message' => 'Live stream Data.',
            ];
            return response()->json($response , 200);
//            return response()->json([
//                'success' => true,
//                'total' => null,
//                'comments' => [],
//                'onlineUsers' => 0,
//                'status' => 'offline'
//            ]);
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
        $response = [
            'success' => true,
            'data' => [
                'comments' => $allComments,
                'likes' => Helper::formatNumber($likes),
                'onlineUsers' => Helper::formatNumber($onlineUsers),
                'status' => $status,
                'total' => $totalComments,
                'time' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0,
            ],
            'message' => 'Live stream Data.',
        ];
        return response()->json($response , 200);
//        return response()->json([
//            'success' => true,
//            'comments' => $allComments,
//            'likes' => Helper::formatNumber($likes),
//            'onlineUsers' => Helper::formatNumber($onlineUsers),
//            'status' => $status,
//            'total' => $totalComments,
//            'time' => $limitLiveStreaming > 0 ? $limitLiveStreaming : 0,
//        ]);
    }
}
