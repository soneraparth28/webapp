<?php

namespace App\Http\Controllers;
use App\Models\Addons;
use App\Models\MollieSubscriptionDetails;
use App\Models\Subscriptions;
use App\Models\Updates;
use App\Models\Plans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Razorpay\Api\Addon;
use function Aws\map;

class AddonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $activeSubscriptions = Subscriptions::where("user_id", auth()->id())->where("ends_at", ">", date("Y-m-d H:i:s"))->get();
        $subscriptionDetails = MollieSubscriptionDetails::where(function ($query) use ($activeSubscriptions) {
            if(empty($activeSubscriptions)) $query->where("id", 0);
            else {
                foreach ($activeSubscriptions as $subscription) {
                    $query->where("subscriber_user_id", $subscription->user_id)->where("subscribe_id", $subscription->subscription_id)
                        ->where("subscription_item", "addon");
                }
            }
        })->get();


        if(!empty($subscriptionDetails)) {
            $subscriptionDetails = $subscriptionDetails->map(function ($subscriptionDetail) use($activeSubscriptions) {
                foreach ($activeSubscriptions as $activeSubscription) {
                    if($subscriptionDetail->subscribe_id === $activeSubscription->subscription_id) {
                        $subscriptionDetail->ends_at = strtotime($activeSubscription->ends_at);
                        $subscriptionDetail->interval = $activeSubscription->interval;
                    }
                }
                return $subscriptionDetail;
            });
        }

        $myAddons = Addons::where(function ($query) use ($subscriptionDetails) {
            if(empty($subscriptionDetails)) $query->where("id", 0);
            else {
                foreach ($subscriptionDetails as $subscription) {
                    $query->where("id", $subscription->addon_id);
                }
            }
        })->get();

        $addonIds = [];

        if(!empty($myAddons)) {
            $myAddons = $myAddons->map(function ($addon) use ($subscriptionDetails, &$addonIds) {
                $addonIds[] = $addon->id;
                $addon->packages = $this->setAddonPackages($addon);

                foreach ($subscriptionDetails as $subscriptionDetail) {
                    if($subscriptionDetail->addon_id === $addon->id) {
                        $addon->ends_at = $subscriptionDetail->ends_at;
                        $addon->interval = $subscriptionDetail->interval;
                        $addon->active_plan = match($addon->interval) {
                            default => null,
                            $addon->plan_1_type => $addon->packages[0],
                            $addon->plan_2_type => $addon->packages[1],
                            $addon->plan_3_type => $addon->packages[2]
                        };
                    }
                }
                return $addon;
            });
        }


        $isCreator = auth()->user()->verified_id === 'yes';
        $addons = $isCreator ? Addons::whereNotIn("id", $addonIds)->get() : Addons::where("verified_only", 0)->whereNotIn("id", $addonIds)->get();
        // return $id;
        // return Updates::where('id', $id);

         $user_id = Auth::id();
        
        $userplans = DB::table('plans')->where('user_id',$user_id)->get()->first();


        return view("includes.addons", [
            "addons" => $addons,
            "myAddons" => $myAddons,
            'plans' => $userplans
        ]);
    }


    public static function userHasAddon($name, $userId): bool {
        $addon = Addons::where("name", $name)->get()->first();
        if(is_null($addon)) return false;
        $id = $addon->id;

        $subscriptionDetails = MollieSubscriptionDetails::where("addon_id", $id)->where("subscriber_user_id", $userId)->where("subscription_item", "addon")
            ->orderBy("id", "DESC")->take(1)->get()->first();
        if(is_null($subscriptionDetails)) return false;

        $subscriptionId = $subscriptionDetails->subscribe_id;
        $activeSubscription = Subscriptions::where("subscription_id", $subscriptionId)->where("ends_at", ">", date("Y-m-d H:i:s"))->get()->first();
        return !is_null($activeSubscription);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): void {
        $request->validate(["email" => "required|unique"]);

//        EmailList::create($request->only("email"));
    }






    public function addonPage($id) {
        $addon = Addons::whereId($id)->get()->first();
        if(empty($addon)) abort(404);
        $addon->packages = $this->setAddonPackages($addon);

        return view("includes.addon-view", [
            "addon" => $addon
        ]);
    }
    
     public function addonsPage($id) {
        $addon = Addons::whereId($id)->get()->first();
        if(empty($addon)) abort(404);
        $addon->packages = $this->setAddonPackages($addon);

        return view("includes.addons-view", [
            "addon" => $addon
        ]);
    }
    
    
    
    public function cancel_subs(){
        
        $user_id = Auth::id();
        $plans = Plans::where('user_id',$user_id)->get()->first();
        $plans->is_cancelled = '1';
        $plans->save();
        return back()->with('success', 'Subscription canceled successful!');
        $userplans = DB::table('plans')->where('user_id',$user_id)->get()->first();
        
        $activeSubscriptions = Subscriptions::where("user_id", auth()->id())->where("ends_at", ">", date("Y-m-d H:i:s"))->get();
        $subscriptionDetails = MollieSubscriptionDetails::where(function ($query) use ($activeSubscriptions) {
            if(empty($activeSubscriptions)) $query->where("id", 0);
            else {
                foreach ($activeSubscriptions as $subscription) {
                    $query->where("subscriber_user_id", $subscription->user_id)->where("subscribe_id", $subscription->subscription_id)
                        ->where("subscription_item", "addon");
                }
            }
        })->get();


        if(!empty($subscriptionDetails)) {
            $subscriptionDetails = $subscriptionDetails->map(function ($subscriptionDetail) use($activeSubscriptions) {
                foreach ($activeSubscriptions as $activeSubscription) {
                    if($subscriptionDetail->subscribe_id === $activeSubscription->subscription_id) {
                        $subscriptionDetail->ends_at = strtotime($activeSubscription->ends_at);
                        $subscriptionDetail->interval = $activeSubscription->interval;
                    }
                }
                return $subscriptionDetail;
            });
        }

        $myAddons = Addons::where(function ($query) use ($subscriptionDetails) {
            if(empty($subscriptionDetails)) $query->where("id", 0);
            else {
                foreach ($subscriptionDetails as $subscription) {
                    $query->where("id", $subscription->addon_id);
                }
            }
        })->get();

        $addonIds = [];

        if(!empty($myAddons)) {
            $myAddons = $myAddons->map(function ($addon) use ($subscriptionDetails, &$addonIds) {
                $addonIds[] = $addon->id;
                $addon->packages = $this->setAddonPackages($addon);

                foreach ($subscriptionDetails as $subscriptionDetail) {
                    if($subscriptionDetail->addon_id === $addon->id) {
                        $addon->ends_at = $subscriptionDetail->ends_at;
                        $addon->interval = $subscriptionDetail->interval;
                        $addon->active_plan = match($addon->interval) {
                            default => null,
                            $addon->plan_1_type => $addon->packages[0],
                            $addon->plan_2_type => $addon->packages[1],
                            $addon->plan_3_type => $addon->packages[2]
                        };
                    }
                }
                return $addon;
            });
        }


        $isCreator = auth()->user()->verified_id === 'yes';
        $addons = $isCreator ? Addons::whereNotIn("id", $addonIds)->get() : Addons::where("verified_only", 0)->whereNotIn("id", $addonIds)->get();

        // $id = Auth::id();
        // return $id;
        // return Updates::where('id', $id);


        return view("includes.addons", [
            "addons" => $addons,
            "myAddons" => $myAddons,
            'plans' => $userplans
        ]);
        
        
        
    }
    
    
     public function apply_subs(){
        
        $user_id = Auth::id();
        $plans = Plans::where('user_id',$user_id)->get()->first();
        $plans->status = '1';
        $plans->save();
        
        $userplans = DB::table('plans')->where('user_id',$user_id)->get()->first();
        
        $activeSubscriptions = Subscriptions::where("user_id", auth()->id())->where("ends_at", ">", date("Y-m-d H:i:s"))->get();
        $subscriptionDetails = MollieSubscriptionDetails::where(function ($query) use ($activeSubscriptions) {
            if(empty($activeSubscriptions)) $query->where("id", 0);
            else {
                foreach ($activeSubscriptions as $subscription) {
                    $query->where("subscriber_user_id", $subscription->user_id)->where("subscribe_id", $subscription->subscription_id)
                        ->where("subscription_item", "addon");
                }
            }
        })->get();


        if(!empty($subscriptionDetails)) {
            $subscriptionDetails = $subscriptionDetails->map(function ($subscriptionDetail) use($activeSubscriptions) {
                foreach ($activeSubscriptions as $activeSubscription) {
                    if($subscriptionDetail->subscribe_id === $activeSubscription->subscription_id) {
                        $subscriptionDetail->ends_at = strtotime($activeSubscription->ends_at);
                        $subscriptionDetail->interval = $activeSubscription->interval;
                    }
                }
                return $subscriptionDetail;
            });
        }

        $myAddons = Addons::where(function ($query) use ($subscriptionDetails) {
            if(empty($subscriptionDetails)) $query->where("id", 0);
            else {
                foreach ($subscriptionDetails as $subscription) {
                    $query->where("id", $subscription->addon_id);
                }
            }
        })->get();

        $addonIds = [];

        if(!empty($myAddons)) {
            $myAddons = $myAddons->map(function ($addon) use ($subscriptionDetails, &$addonIds) {
                $addonIds[] = $addon->id;
                $addon->packages = $this->setAddonPackages($addon);

                foreach ($subscriptionDetails as $subscriptionDetail) {
                    if($subscriptionDetail->addon_id === $addon->id) {
                        $addon->ends_at = $subscriptionDetail->ends_at;
                        $addon->interval = $subscriptionDetail->interval;
                        $addon->active_plan = match($addon->interval) {
                            default => null,
                            $addon->plan_1_type => $addon->packages[0],
                            $addon->plan_2_type => $addon->packages[1],
                            $addon->plan_3_type => $addon->packages[2]
                        };
                    }
                }
                return $addon;
            });
        }


        $isCreator = auth()->user()->verified_id === 'yes';
        $addons = $isCreator ? Addons::whereNotIn("id", $addonIds)->get() : Addons::where("verified_only", 0)->whereNotIn("id", $addonIds)->get();

        // $id = Auth::id();
        // return $id;
        // return Updates::where('id', $id);


        return view("includes.addons", [
            "addons" => $addons,
            "myAddons" => $myAddons,
            'plans' => $userplans
        ]);
        
        
        
    }
    
    
    
    
    

    private function setAddonPackages($addon) {
        if(empty($addon)) return null;
        $packages = ["plan_1_" => [],"plan_2_" => [],"plan_3_" => []];

        foreach (array_keys($packages) as $i => $packageItem) {
            foreach (json_decode(json_encode($addon), true) as $key => $value) {
                if(str_contains($key, $packageItem)) {
                    $packages[$packageItem][str_replace($packageItem, "", $key)] = $value;
                    if(!array_key_exists(($i + 1), $packages[$packageItem])) $packages[$packageItem]["id"] = ($i +1);
                }
            }
        }
        return json_decode(json_encode(array_values($packages)));
    }


    public function purchaseAddon(Request $request) {
        if(!Auth::check()) return response()->json(['success' => false,'errors' => ['error' => __('general.error')]]);
        //<---- Validation
        $validator = Validator::make($request->all(), [
            'agree_terms' => 'required',
            'addon' => 'gte:1',
            'interval' => Rule::in(["monthly", "biannually", "annually"]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        $user = auth()->user();
        $addon = Addons::where(function ($query) use ($user, $request) {
            $query->whereId($request->addon)->where("status", 1);
            if($user->verified_id !== "yes") $query->where("verified_only", 0);
        })->firstOrFail();




        if(empty($addon)) return response()->json(['success' => false,'errors' => ['error' => __('general.error')]]);
        $query = [
            "addon" => $addon->id,
            "interval" => $request->interval,
        ];

        // Check if subscription exists
        //......


        $redirect_url = route('subscription-addon-initial') . "?" . http_build_query($query);

        return response()->json([
            'success' => true,
            'url' => $redirect_url,
        ]);
    }


}
