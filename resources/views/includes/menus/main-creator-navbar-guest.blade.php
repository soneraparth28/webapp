<header class="main-creator-nav header-top-sizing @if(!auth()->check()) visiting @endif">

    <div class="flex-row-between flex-align-center flex-nowrap">


        <img src="{{asset("public/img/favicon-1650569283.png")}}" class="square-40 border-radius-5px noSelect mobileOnlyBlock" />
        <img src="{{asset("public/img/bm-logo.png")}}" class=" noSelect hideOnMobileBlock" style="height: 40px;"/>



        <div class="flex-row-end flex-align-center flex-nowrap mobile-position-absolute-def-static">
            @if (auth()->guest() && $settings->who_can_see_content == 'all' || auth()->check())
                <div class="navbar-nav mr-auto flex-col-around custom-dropdown-parent mobileFormHidden @if(!auth()->check()) visiting @endif" id="search-creators-form">
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
        </div>
    </div>

</header>

