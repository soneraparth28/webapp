<header class="main-creator-nav header-top-sizing">

    <div class="flex-row-between flex-align-center flex-nowrap">

        @if(!str_contains(request()->path(), "messages/") || !isset($messages))
            <p class="lightest-gray-btn square-40 mx-3 flex-row-around flex-align-center mb-0 mobileOnlyFlex"><i class="font-25 text-gray bi bi-list" id="leftSidebarOpenBtn"></i></p>
            <p class="font-25 font-weight-bold mb-0">
                @if(Route::currentRouteName() == 'user.dashboard')
                    Welcome {{ auth()->user()->name }} 👋
                @else
                    {{$pageTitle}}
                @endif
            
                
            </p>
        @else
            <a href="{{url("messages")}}" class="lightest-gray-btn square-40 mx-3 flex-row-around flex-align-center mb-0"><i class="font-25 text-gray bi bi-arrow-left"></i></a>

            <div class="p-4 border-bottom border-med-light-gray mobileOnlyBlock">
                <div class="flex-row-start flex-align-center">
                    <a href="{{url($user->username)}}" class="">
                      <span class="position-relative user-status @if ($user->active_status_online == 'yes') @if (Cache::has('is-online-' . $user->id)) user-online @else user-offline @endif @endif d-block">
                        <img src="{{Helper::getFile(config('path.avatar').$user->avatar)}}" class="border-radius-10px square-30" />
                      </span>
                    </a>
                    <div class="flex-col-start ml-3">
                        <p class="font-18 font-weight-bold mb-0">{{$user->name}}</p>
                        <p class="font-14 color-light-gray mb-0" @if($user->active_status_online == 'yes' && Cache::has('is-online-' . $user->id)) style="color: #4caf50 !important;" @endif>
                            {{$user->active_status_online == 'yes' && Cache::has('is-online-' . $user->id) ? "Online" : "Offline"}}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <a href="{{url("notifications")}}" class="lightest-gray-btn square-40 flex-row-around flex-align-center mobileOnlyFlex mx-3"><i class="feather icon-bell font-25"></i></a>




        <div class="flex-row-end flex-align-center flex-nowrap mobile-position-absolute-def-static">
            @if (auth()->guest() && $settings->who_can_see_content == 'all' || auth()->check())
                <div class="navbar-nav mr-auto flex-col-around custom-dropdown-parent mobileFormHidden" id="search-creators-form">
                    <form class="my-lg-0 m-0 bg-light" method="get" action="{{url('creators')}}">
                        <input id="searchCreatorNavbar" type="text" required name="q" autocomplete="off" minlength="3" placeholder="{{ trans('general.find_user') }}"
                               class="form-control search-bar @if(auth()->guest() && request()->path() == '/') border-0 @endif py-1 px-2" style="height: 40px;">
                        <div class="button-search e-none flex-col-around h-100 mr-1 custom-dropdown-btn">
                            <i class="bi bi-search font-18 color-light-gray"></i>
                        </div>

                        <div class="custom-dropdown"  id="dropdownCreators">
                            <div class="w-100 text-center display-none py-2" id="spinnerSearch">
                                <span class="spinner-border spinner-border-sm align-middle text-primary"></span>
                            </div>

                            <div id="containerCreators"></div>

                        </div><!-- dropdown-menu -->
                    </form>

                </div>
            @endif

            <a href="{{url("/")}}" class="lightest-gray-btn square-40 flex-row-around flex-align-center ml-4 hideOnMobileFlex"><i class="feather icon-home font-25"></i></a>
            <a href="{{url("messages")}}" class="lightest-gray-btn square-40 flex-row-around flex-align-center ml-4 hideOnMobileFlex"><i class="feather icon-message-square font-25"></i></a>
            <a href="{{url("notifications")}}" class="lightest-gray-btn square-40 flex-row-around flex-align-center ml-4 hideOnMobileFlex"><i class="feather icon-bell font-25"></i></a>
            <a href="{{url("settings/page")}}" class="lightest-gray-btn square-40 flex-row-around flex-align-center ml-4 hideOnMobileFlex"><i class="feather icon-settings font-25"></i></a>
        </div>
    </div>

</header>

<div class="session-main-mobile-footer flex-row-around flex-align-center mobileOnlyFlex">
    <a href="{{url("/")}}" class="flex-row-around flex-align-center px-1"><i class="feather icon-home font-20"></i></a>
    <a href="{{url("/messages")}}" class="flex-row-around flex-align-center px-1"><i class="feather icon-message-square font-20"></i></a>
    @if(auth()->check() && auth()->user()->verified_id === "yes")
        <a href="{{url("/dashboard")}}" class="flex-row-around flex-align-center px-1"><i class="feather icon-bar-chart font-20"></i></a>
    @endif
    <a href="{{url("/settings/page")}}" class="flex-row-around flex-align-center px-1"><i class="feather icon-settings font-20"></i></a>
    @if (auth()->guest() && $settings->who_can_see_content == 'all' || auth()->check())
        <a href="javascript:void(0);" class="flex-row-around flex-align-center px-1" id="showMobileSearchForm"><i class="feather icon-search font-20"></i></a>
    @endif

</div>
