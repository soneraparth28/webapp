
@auth()
<div class="session-main-left-menu">

    <div class="header-top-sizing">

        <div class="flex-row-around flex-align-center" id="sidebar-logo">
            <img class="noSelect mxh-40px cursor-pointer" src="{{asset('public/img/bm-logo.png')}}" data-href="{{url("/")}}"/>
            <i class="bi bi-x font-30 text-gray" id="leftSidebarCloseBtn" ></i>
        </div>

        @if(auth()->check())
            <div class="flex-col-start flex-align-center mt-4">
                <img class="square-90 border-radius-50 noSelect" src="{{Helper::getFile(config('path.avatar').auth()->user()->avatar)}}" />
                <p class="font-weight-bold font-16 mt-2 mb-0">{{Auth::user()->name}}</p>
                <p class="mb-0">{{Auth::user()->email}}</p>
            </div>
        @endif
    </div>


    <div class="flex-col-start mt-4" id="sidebar-body">
        <div class="" id="sidebar-top-nav">
            <i class="font-25 text-gray bi bi-list" id="leftSidebarOpenBtn"></i>
        </div>

        @if(Auth::user()->verified_id === "yes")
       
            <?php
                    $app = false;
                    $user = Auth::user();
                    $plan = $user->plans->first();
                    if(isset($plan)){
                    if(isset($plan->ends_at)){
                        $end_day = strtotime($plan->ends_at);
                        $today = strtotime(date("Y-m-d H:i:s"));
                        


                        
                        if($end_day < $today){
                            $plan->status = '0';
                            $plan->save();
                        }
                    }
                    /*$user_id = Auth::id();
                    $plans = Plans::where('user_id',$user_id)->get()->first();
                    */

                    if($plan->status == 1){

                    $userSubscriptions = $user->userSubscriptions->first();
                    if($userSubscriptions){
                    if($userSubscriptions->ends_at > date("Y-m-d H:i:s") && $plan->status == 1){
                        if($userSubscriptions->stripe_price="addon_1"){
                            $app = true;
                        }
                    }
                    }
                    
                ?>
        
            <a class="menu-link-item @if(request()->is('dashboard')) active @endif"  href="{{url("dashboard")}}">
                <i class="fa fa-chart-bar font-16"></i>
                <span class="font-16 sidebar-text">Dashboard</span>
            </a>
            <a class="menu-link-item @if(request()->is('/')) active @endif" href="{{url("/")}}">
                <i class="fa fa-house-user font-16"></i>
                <span class="font-16 sidebar-text">My Profile</span>
            </a>
            
            <!--<a class="menu-link-item @if(request()->is('my/content-creators-referrals')) active @endif" href="{{url("my/content-creators-referrals")}}">-->
            <!--    <i class="bi-person-plus font-16"></i>-->
            <!--    <span class="font-16 sidebar-text">Creator referrals</span>-->
            <!--</a>-->
            <a class="menu-link-item @if(request()->is('course/dashboard')) active @endif" href="{{url("course/dashboard")}}">
                <i class="bi bi-mortarboard font-16"></i>
                <span class="font-16 sidebar-text">{{trans('general.courses')}}</span>
            </a>
            
           <?php if($app){ ?>
            
            
            
            <?php } ?>
            <a class="menu-link-item" href="{{url("app-builder")}}">
              <i class="bi bi-phone font-16"></i>
              <span class="font-16 sidebar-text">App</span>
            </a>
            <a class="menu-link-item @if(request()->is('my/channels')) active @endif" href="{{url("my/channels")}}">
                <i class="bi bi-card-text font-16"></i>
                <span class="font-16 sidebar-text">{{trans('general.community')}}</span>
            </a>
            
            <a class="menu-link-item btnCreateLive @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif" data-toggle="tooltip" data-placement="top">
                
                  <i class="bi bi-broadcast font-16"></i>
                  <span class="font-16 sidebar-text">Livestream</span>
              </a>    
             <?php } } ?>
        
            
            <a class="menu-link-item @if(request()->is('logout')) active @endif" href="{{url("logout")}}">
                <i class="feather icon-log-out font-16"></i>
                <span class="font-16 sidebar-text">{{trans('auth.logout')}}</span>
            </a>
        @else

            <a class="menu-link-item @if(request()->is('dashboard')) active @endif"  href="{{url("dashboard")}}">
                <i class="fa fa-chart-bar font-16"></i>
                <span class="font-16 sidebar-text">Dashboard</span>
            </a>
            <a class="menu-link-item @if(request()->is('/')) active @endif" href="{{url("/")}}">
                <i class="fa fa-house-user font-16"></i>
                <span class="font-16 sidebar-text">My Profile</span>
            </a>
             <a class="menu-link-item @if(request()->is('course/dashboard')) active @endif" href="{{url("course/dashboard")}}">
                <i class="bi bi-mortarboard font-16"></i>
                <span class="font-16 sidebar-text">{{trans('general.courses')}}</span>
            </a>
            <!--<a class="menu-link-item @if(request()->is('/')) active @endif" href="{{url("/")}}">-->
            <!--    <i class="fa fa-house-user font-16"></i>-->
            <!--    <span class="font-16 sidebar-text">Feed</span>-->
            <!--</a>-->
            <!--<a class="menu-link-item @if(request()->is('my/subscriptions')) active @endif" href="{{url("my/subscriptions")}}">-->
            <!--    <i class="feather icon-user-check font-16"></i>-->
            <!--    <span class="font-16 sidebar-text">{{trans('users.my_subscriptions')}}</span>-->
            <!--</a>-->
            <a class="menu-link-item @if(request()->is('logout')) active @endif" href="{{url("logout")}}">
                <i class="feather icon-log-out font-16"></i>
                <span class="font-16 sidebar-text">{{trans('auth.logout')}}</span>
            </a>
        @endif
    </div>
</div>

@endauth
