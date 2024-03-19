@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection

@section('content')

    <?php $pageTitle = $isMyOwnPage ? "My page" : ""; ?>

    <div class="session-main-wrapper">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper @if(!auth()->check()) visiting @endif">
            @if(auth()->check())
            @include("includes.menus.main-creator-navbar")
            @else
                @include("includes.menus.main-creator-navbar-guest")
            @endif

            <div class="session-main-content-wrapper">



                <div class="row">
                    <div class="col-12">
                        <div class="jumbotron jumbotron-cover-user home m-0 position-relative border-radius-10px" style="padding: 125px 0; background: #505050 @if ($user->cover != '') url('{{Helper::getFile(config('path.cover').$user->cover)}}') no-repeat center center; background-size: cover; @endif">
                            @if (auth()->check() && auth()->user()->status == 'active' && auth()->id() == $user->id)

                                <div class="progress-upload-cover"></div>

                                <form action="{{url('upload/cover')}}" method="POST" id="formCover" accept-charset="UTF-8" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="image" id="uploadCover" accept="image/*" class="visibility-hidden">
                                </form>

                                <button class="light-btn abs-bl-10-10 z-100" id="coverFile" onclick="$('#uploadCover').trigger('click');">
                                    <i class="fa fa-camera mr-lg-1"></i>  <span class="d-none d-lg-inline">{{trans('general.change_cover')}}</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-12">
                        <div class="w-100 text-center py-4 img-profile-user">
                            <div class="text-center position-relative flex-row-around">

                                <div class="progress-upload">0%</div>

                                <div style="@if(auth()->check() && !$isMyOwnPage && $user->isLive()) width: 160px; height: 160px; background: #E3493F;
                                    @else width: 150px; height: 150px; @endif" class="border-radius-50 flex-col-around flex-align-center position-relative"
                                     @if ($user->isLive() && auth()->check() && !$isMyOwnPage) data-href="{{ url('live', $user->username) }}" @endif>
                                    <img src="{{Helper::getFile(config('path.avatar').$user->avatar)}}" alt="{{$user->hide_name == 'yes' ? $user->username : $user->name}}"
                                         class="border-radius-50 square-150 @if (auth()->check() && !$isMyOwnPage && $user->isLive()) border-0 avatar-live @endif">

                                    @if (auth()->check() && auth()->user()->status == 'active' && $isMyOwnPage)
                                        <form action="{{url('upload/avatar')}}" method="POST" id="formAvatar" accept-charset="UTF-8" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="avatar" id="uploadAvatar" accept="image/*" class="visibility-hidden">
                                        </form>

                                        <a href="javascript:;" class="position-absolute button-avatar-upload " style="bottom: 0; left: 0; right: 0;" id="avatar_file">
                                            <i class="fa fa-camera color-light"></i>
                                        </a>
                                    @endif
                                </div>

                            </div><!-- avatar-wrap -->


                            <div class="">

                                <p class="mt-1 mb-0 font-22 font-weight-bold">
                                    {{$user->hide_name == 'yes' ? $user->username : ucfirst($user->name)}}

                                    @if ($user->featured == 'yes')
                                        <i class="fas fa fa-award ml-2" title="{{trans('users.creator_featured')}}" data-toggle="tooltip" data-placement="top"></i>
                                    @endif

                                    @if ($user->verified_id == 'yes')
                                        <i class="bi bi-patch-check-fill ml-2" title="{{trans('general.verified_account')}}" data-toggle="tooltip" data-placement="top"></i>
                                    @endif
                                </p>

                                @if(!$isMyOwnPage)

                                    <div class="flex-col-start flex-align-center mt-2 mb-2">

                                        <div class="flex-row-start flex-align-center mobileOnlyFlex">
                                            @include("includes.profile-subscription-buttons")
                                        </div>

                                        <div class="flex-row-start flex-align-center mt-2 mt-md-0">

                                            @if (auth()->guest() && $user->verified_id == 'yes' || auth()->check() && !$isMyOwnPage && $user->verified_id == 'yes')
                                                <button @guest data-toggle="modal" data-target="#loginFormModal" @else id="sendMessageUser" @endguest data-url="{{url('messages/'.$user->id, $user->username)}}" title="{{trans('general.message')}}" class="dark-btn-mobile-light square-50 flex-row-around flex-align-center mr-2">
                                                    <i class="fa fa-message color-light"></i>
                                                </button>
                                            @endif

                                            @if ($user->verified_id == 'yes')
                                                <button class="dark-btn-mobile-light square-50 flex-row-around flex-align-center mr-2" title="{{trans('general.share')}}" id="dropdownUserShare" role="button" data-toggle="modal" data-target=".share-modal">
                                                    <i class="fa fa-share color-light"></i>
                                                </button>

                                                <!-- Share modal -->
                                                <div class="modal fade share-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header border-bottom-0">
                                                                <button type="button" class="close close-inherit" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true"><i class="bi bi-x-lg"></i></span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="container-fluid">
                                                                    <div class="row">
                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{url($user->username).Helper::referralLink()}}" title="Facebook" target="_blank" class="social-share text-muted d-block text-center h6">
                                                                                <i class="fab fa-facebook-square facebook-btn"></i>
                                                                                <span class="btn-block mt-3">Facebook</span>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="https://twitter.com/intent/tweet?url={{url($user->username).Helper::referralLink()}}&text={{ e( $user->hide_name == 'yes' ? $user->username : $user->name ) }}" data-url="{{url($user->username)}}" class="social-share text-muted d-block text-center h6" target="_blank" title="Twitter">
                                                                                <i class="fab fa-twitter twitter-btn"></i> <span class="btn-block mt-3">Twitter</span>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="whatsapp://send?text={{url($user->username).Helper::referralLink()}}" data-action="share/whatsapp/share" class="social-share text-muted d-block text-center h6" title="WhatsApp">
                                                                                <i class="fab fa-whatsapp btn-whatsapp"></i> <span class="btn-block mt-3">WhatsApp</span>
                                                                            </a>
                                                                        </div>

                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="mailto:?subject={{ e( $user->hide_name == 'yes' ? $user->username : $user->name ) }}&amp;body={{url($user->username).Helper::referralLink()}}" class="social-share text-muted d-block text-center h6" title="{{trans('auth.email')}}">
                                                                                <i class="far fa-envelope"></i> <span class="btn-block mt-3">{{trans('auth.email')}}</span>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="sms://?body={{ trans('general.check_this') }} {{url($user->username).Helper::referralLink()}}" class="social-share text-muted d-block text-center h6" title="{{ trans('general.sms') }}">
                                                                                <i class="fa fa-sms"></i> <span class="btn-block mt-3">{{ trans('general.sms') }}</span>
                                                                            </a>
                                                                        </div>
                                                                        <div class="col-md-4 col-6 mb-3">
                                                                            <a href="javascript:void(0);" id="btn_copy_url" class="social-share text-muted d-block text-center h6 link-share" title="{{trans('general.copy_link')}}">
                                                                                <i class="fas fa-link"></i> <span class="btn-block mt-3">{{trans('general.copy_link')}}</span>
                                                                            </a>
                                                                            <input type="hidden" readonly="readonly" id="copy_link" class="form-control" value="{{url($user->username).Helper::referralLink()}}">
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif


                                            @if (auth()->check() && !$isMyOwnPage)
                                                <button class="dark-btn-mobile-light square-50 flex-row-around flex-align-center mr-2" title="{{trans('general.report_user')}}" role="button" data-toggle="modal" data-target="#reportCreator">
                                                    <i class="fa fa-flag color-light"></i>
                                                </button>
                                            @endif


                                            <div class="flex-row-start flex-align-center hideOnMobileFlex">
                                                @include("includes.profile-subscription-buttons")
                                            </div>



                                            @if ((auth()->check() && !$isMyOwnPage && $user->updates()->count() <> 0 && $settings->disable_tips == 'off'))
                                                <a href="javascript:void(0);" data-toggle="modal" title="{{trans('general.tip')}}" data-target="#tipForm" class="dark-btn-mobile-light mr-2" data-cover="{{Helper::getFile(config('path.cover').$user->cover)}}" data-avatar="{{Helper::getFile(config('path.avatar').$user->avatar)}}" data-name="{{$user->hide_name == 'yes' ? $user->username : $user->name}}" data-userid="{{$user->id}}">
                                                    <i class="fa fa-coins mr-2 color-light"></i> Give Tips
                                                </a>
                                            @elseif ((auth()->guest() && $user->updates()->count() <> 0))
                                                <a href="{{url('login')}}" data-toggle="modal" data-target="#loginFormModal" class="dark-btn-mobile-light mr-2" title="{{trans('general.tip')}}">
                                                    <i class="fa fa-coins mr-2 color-light"></i> Give Tips
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($user->verified_id == 'yes')
                    @php
                        $mediaTypeItems = [
                            [
                                "active" => in_array(request()->path(), [$user->username, "/", ""]),
                                "link" => url($user->username),
                                "title" => trans('general.posts'),
                                "text" => trans('general.posts'),
                                "icon" => "feather icon-file-text",
                            ],
                            [
                                "active" => in_array(request()->path(), [$user->username.'/photos']),
                                "link" => url($user->username, 'photos'),
                                "title" => trans('general.photos'),
                                "text" => trans('general.photos'),
                                "icon" => "feather icon-image",
                            ],
                            [
                                "active" => in_array(request()->path(), [$user->username.'/videos']),
                                "link" => url($user->username, 'videos'),
                                "title" => trans('general.video'),
                                "text" => trans('general.video'),
                                "icon" => "feather icon-video",
                            ],
                            [
                                "active" => in_array(request()->path(), [$user->username.'/audio']),
                                "link" => url($user->username, 'audio'),
                                "title" => trans('general.audio'),
                                "text" => trans('general.audio'),
                                "icon" => "feather icon-mic",
                            ],
                        ];
                    @endphp

                    @if (\App\Models\Updates::where("user_id", $user->id)->where("course", "yes")->count() != 0)
                        @php
                            $mediaTypeItems[] = [
                                "active" => in_array(request()->path(), [$user->username.'/courses']),
                                "link" => url($user->username, 'courses'),
                                "title" => trans('general.courses'),
                                "text" => trans('general.courses'),
                                "icon" => "bi bi-mortarboard",
                            ];
                        @endphp
                    @endif

                    @if ($user->media()->where('media.file', '<>', '')->count() != 0)
                        @php
                            $mediaTypeItems[] = [
                                "active" => in_array(request()->path(), [$user->username.'/files']),
                                "link" => url($user->username, 'files'),
                                "title" => trans('general.files'),
                                "text" => trans('general.files'),
                                "icon" => "far fa-file-archive",
                            ];
                        @endphp
                    @endif

                    @if ($settings->shop || ! $settings->shop && $userProducts->count() != 0)
                        @php
                            $mediaTypeItems[] = [
                                "active" => in_array(request()->path(), [$user->username.'/shop']),
                                "link" => url($user->username, 'shop'),
                                "title" => trans('general.shop'),
                                "text" => trans('general.shop'),
                                "icon" => "feather icon-shopping-bag",
                            ];
                        @endphp
                    @endif
                @endif



                @if ($user->verified_id == 'yes')
                    <div class="row mt-3">
                        <div class="col-12 mobileOnlyFlex mb-3 justify-content-around">
                            <div class="flex-row-start flex-align-center flex-nowrap overflow-auto hideScrollBar">
                                @foreach($mediaTypeItems as $i => $mediaTypeItem)
                                    @if($i > 4) @break @endif
                                    <a href="{{$mediaTypeItem['link']}}" title="{{$mediaTypeItem['title']}}"
                                       class="@if($mediaTypeItem["active"]) dark-btn color-light @else lightest-gray-btn @endif mr-3 pl-3 pr-3 flex-row-start flex-align-center flex-nowrap">
                                        <i class="{{$mediaTypeItem['icon']}}"></i>
                                        <span class="ml-2">{{$mediaTypeItem['text']}}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <div class="col-12 col-lg-4 col-xl-3 @if($isMyOwnPage) d-none d-lg-flex @endif">
                        <div class="flex-col-start w-100">

                            <div class="item-container-box w-100">
                                <p class="font-20 font-weight-bold color-dark mb-0">{{trans('users.about_me')}}</p>

                                @if ($user->verified_id == 'yes')
                                    <p class="font-14 color-dark">
                                        {!! Helper::checkText($user->story)  !!}
                                    </p>
                                @endif

                                @if ($subscriptionsActive != 0 && $user->hide_count_subscribers == 'no')
                                    <div class="flex-row-start flex-align-center">
                                        <i class="feather icon-users color-light-gray"></i>
                                        <p class="mb-0 font-16 ml-2 color-light-gray" >{{ Helper::formatNumber($subscriptionsActive) }} {{ trans_choice('general.subscribers', $subscriptionsActive) }}</p>
                                    </div>
                                @endif

                                @if (isset($user->country()->country_name) && $user->hide_my_country == 'no')
                                    <div class="flex-row-start flex-align-center mt-2">
                                        <i class="feather icon-map color-light-gray"></i>
                                        <p class="mb-0 font-16 ml-2 color-light-gray">{{$user->country()->country_name}}</p>
                                    </div>
                                @endif

                                <div class="flex-row-start flex-align-center mt-2">
                                    <i class="far fa-user-circle color-light-gray"></i>
                                    <p class="mb-0 font-16 ml-2 color-light-gray">{{ trans('general.member_since') }} {{ Helper::formatDate($user->date) }}</p>
                                </div>

                                @if ($user->show_my_birthdate == 'yes')
                                    <div class="flex-row-start flex-align-center mt-2">
                                        <i class="far fa-calendar-alt color-light-gray"></i>
                                        <p class="mb-0 font-16 ml-2 color-light-gray">
                                            {{ trans('general.birthdate') }} {{ Helper::formatDate($user->birthdate) }} ({{ \Carbon\Carbon::parse($user->birthdate)->age }} {{ __('general.years') }})
                                        </p>
                                    </div>
                                @endif

                                @if ($user->website != '')
                                    <div class="d-block text-truncate mt-2">
                                        <a href="{{$user->website}}" title="{{$user->website}}" target="_blank" class="color-light-gray">
                                            <i class="fa fa-link color-light-gray"></i>
                                            <span class="font-16 ml-2 color-light-gray">{{Helper::removeHTPP($user->website)}}</span>
                                        </a>
                                    </div>
                                @endif

                                @if ($user->facebook != '')
                                    <a href="{{$user->facebook}}" title="{{$user->facebook}}" target="_blank" class="text-muted share-btn-user"><i class="bi bi-facebook mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->twitter != '')
                                    <a href="{{$user->twitter}}" title="{{$user->twitter}}" target="_blank" class="text-muted share-btn-user"><i class="fab fa-twitter mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->instagram != '')
                                    <a href="{{$user->instagram}}" title="{{$user->instagram}}" target="_blank" class="text-muted share-btn-user"><i class="fab fa-instagram mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->youtube != '')
                                    <a href="{{$user->youtube}}" title="{{$user->youtube}}" target="_blank" class="text-muted share-btn-user"><i class="fab fa-youtube mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->pinterest != '')
                                    <a href="{{$user->pinterest}}" title="{{$user->pinterest}}" target="_blank" class="text-muted share-btn-user"><i class="fab fa-pinterest-p mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->github != '')
                                    <a href="{{$user->github}}" title="{{$user->github}}" target="_blank" class="text-muted share-btn-user"><i class="fab fa-github mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->snapchat != '')
                                    <a href="{{$user->snapchat}}" title="{{$user->snapchat}}" target="_blank" class="text-muted share-btn-user"><i class="bi bi-snapchat mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->tiktok != '')
                                    <a href="{{$user->tiktok}}" title="{{$user->tiktok}}" target="_blank" class="text-muted share-btn-user"><i class="bi bi-tiktok mr-2 color-light-gray"></i></a>
                                @endif

                                @if ($user->categories_id != '0' && $user->categories_id != '' && $user->verified_id == 'yes')
                                    <div class="w-100 mt-2">
                                        @foreach (Categories::where('mode','on')->orderBy('name')->get() as $category)
                                            @foreach ($categories as $categoryKey)
                                                @if ($categoryKey == $category->id)
                                                    <a href="{{url('category', $category->slug)}}" class="button-white-sm mb-2 color-light-gray">
                                                        #{{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endforeach

                                    </div>
                                @endif
                            </div>

                            <div class="item-container-box w-100 color-gray mt-4">
                                <p class="font-20 font-weight-bold color-dark mb-0">Filter</p>

                                <select class="form-control mt-2" id="selected-category">
                                    <option value="">All Categories</option>
                                    @foreach($update_categories as $c)
                                        <option value="{{$c->id}}">{{$c->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($user->verified_id === "yes" && ($checkSubscription || $isMyOwnPage))
                            <div class="item-container-box w-100 color-gray mt-4">
                                <p class="font-20 font-weight-bold color-dark mb-0">Channels</p>
                                @foreach($channels as $c)
                                    <a href="{{ Request::url().'?channel='.$c->id }}" style="text-decoration: none;">
                                        <div class="flex-row-start flex-align-center mt-2">
                                            <p class="mb-0 font-16 ml-2 @if(app('request')->input('channel') && app('request')->input('channel') == $c->id) font-weight-bold @else color-light-gray @endif">#{{ $c->channel }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>



                    <div class="col-12 col-lg-8 col-xl-9 mt-3 mt-lg-0">
                        @if ($user->verified_id == 'yes')
                            <div class="flex-row-start flex-align-center flex-nowrap hideOnMobileFlex">
                                @foreach($mediaTypeItems as $i => $mediaTypeItem)
                                    @if($i > 4) @break @endif
                                    <a href="{{$mediaTypeItem['link']}}" title="{{$mediaTypeItem['title']}}"
                                       class="@if($mediaTypeItem["active"]) dark-btn color-light @else lightest-gray-btn @endif mr-3 pl-3 pr-3 flex-row-start flex-align-center flex-nowrap">
                                        <i class="{{$mediaTypeItem['icon']}}"></i>
                                        <span class="ml-2">{{$mediaTypeItem['text']}}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif






                        @if (auth()->check()
                            && $user->verified_id === "yes"
                            && $isMyOwnPage
                            && ! $userPlanMonthlyActive
                            && auth()->user()->free_subscription == 'no'
                            )
                            <div class="alert alert-danger mb-3">
                                <ul class="list-unstyled m-0">
                                    <li><i class="fa fa-exclamation-triangle"></i> {{trans('general.alert_not_subscription')}} <a href="{{url('settings/subscription')}}" class="text-white link-border">{{trans('general.activate')}}</a></li>
                                </ul>
                            </div>
                        @endif

                        @if (auth()->check() && $user->verified_id === "yes" && (($isMyOwnPage && auth()->user()->verified_id != 'reject') || app('request')->input('channel')) && in_array(request()->path(), [$user->username, "/", ""]))
                            @include('includes.form-post')
                        @endif








                        @if (($updates->count() == 0 && $findPostPinned->count() == 0))
                            <div class="grid-updates"></div>
                            <div class="my-5 text-center no-updates">
                                <span class="btn-block mb-3">
                                    <i class="fa fa-photo-video ico-no-result"></i>
                                </span>
                                <h4 class="font-weight-light">{{trans('general.no_posts_posted')}}</h4>
                            </div>
                        @else
                            @php
                                $counterPosts = ($updates->total() - $settings->number_posts_show);
                            @endphp


                            <div class="grid-updates position-relative" id="updatesPaginator">
                                @if ($findPostPinned && ! request('media'))
                                    @include('includes.updates', ['updates' => $findPostPinned])
                                @endif

                                @include('includes.updates')
                            </div>
                        @endif






                    </div>
                </div>




                @if (!auth()->check())
                    @include("includes.modal-login")
                @endif


                <!-- Modal reportCreator -->
                @if (auth()->check() && !$isMyOwnPage)
                    <div class="modal fade modalReport" id="reportCreator" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-danger modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title font-weight-light" id="modal-title-default"><i class="fas fa-flag mr-1"></i> {{trans('general.report_user')}}</h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                                <!-- form start -->
                                <form method="POST" action="{{url('report/creator', $user->id)}}" enctype="multipart/form-data">
                                    <div class="modal-body">
                                    @csrf
                                    <!-- Start Form Group -->
                                        <div class="form-group">
                                            <label>{{trans('admin.please_reason')}}</label>
                                            <select name="reason" class="form-control custom-select">
                                                <option value="spoofing">{{trans('admin.spoofing')}}</option>
                                                <option value="copyright">{{trans('admin.copyright')}}</option>
                                                <option value="privacy_issue">{{trans('admin.privacy_issue')}}</option>
                                                <option value="violent_sexual">{{trans('admin.violent_sexual_content')}}</option>
                                                <option value="spam">{{trans('general.spam')}}</option>
                                                <option value="fraud">{{trans('general.fraud')}}</option>
                                                <option value="under_age">{{trans('general.under_age')}}</option>
                                            </select>
                                        </div><!-- /.form-group-->
                                    </div><!-- Modal body -->

                                    <div class="modal-footer">
                                        <button type="button" class="dark-btn text-white" data-dismiss="modal">{{trans('admin.cancel')}}</button>
                                        <button type="submit" class="light-btn sendReport ml-auto"><i></i> {{trans('general.report_user')}}</button>
                                    </div>

                                </form>
                            </div><!-- Modal content -->
                        </div><!-- Modal dialog -->
                    </div>
                @endif








                @if (auth()->check() && auth()->id() != $user->id && ! $checkSubscription  && $user->verified_id == 'yes')

                <!-- after first subscrition information model -->
                    <div class="modal fade" id="firstSubscriptionConfirmationInfo" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                        <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="card bg-theme-card shadow border-0">
                                        <div class="card-header pb-2 border-0 position-relative text-center">
                                            <h5>Waiting for successfull payment</h5>
                                        </div>
                                        <div class="card-body px-lg-5 py-lg-5 position-relative">

                                            <div class="text-center mb-3 position-relative">
                                                <div class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <!-- <p class="font-weight-light">
                                                  Please wait while we are confirming your payment. It takes 5-20 mins for first time. After successfull payment subscription will automatically start.
                                                </p> -->
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End after first subscrition information model -->

                    @if ($user->free_subscription == 'no')
                        @include("includes.modal-subscription")
                    @endif

                <!-- Subscription Free -->
                    <div class="modal fade" id="subscriptionFreeForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                        <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <div class="card bg-theme-card shadow border-0">
                                        <div class="card-header pb-2 border-0 position-relative" style="height: 100px; background: {{$settings->color_default}} @if ($user->cover != '')  url('{{Helper::getFile(config('path.cover').$user->cover)}}') no-repeat center center @endif; background-size: cover;">

                                        </div>
                                        <div class="card-body px-lg-5 py-lg-5 position-relative">

                                            <div class="text-muted text-center mb-3 position-relative modal-offset">
                                                <img src="{{Helper::getFile(config('path.avatar').$user->avatar)}}" width="100" alt="{{$user->hide_name == 'yes' ? $user->username : $user->name}}" class="avatar-modal rounded-circle mb-1">
                                                <h6 class="font-weight-light">
                                                    {{trans('general.subscribe_free_content') }} {{$user->hide_name == 'yes' ? $user->username : $user->name}}
                                                </h6>
                                            </div>

                                            @if ($updates->total() == 0 && $findPostPinned->count() == 0)
                                                <div class="bg-dark color-light border-radius-5px p-2" role="alert">
                                                    <i class="fa fa-exclamation-triangle mr-1"></i> {{ $user->first_name }} {{ trans('general.not_posted_any_content') }}
                                                </div>
                                            @endif

                                            <div class="text-center text-muted mb-2 mt-2">
                                                <h5>{{trans('general.what_will_you_get')}}</h5>
                                            </div>

                                            <ul class="list-unstyled">
                                                <li><i class="fa fa-check mr-2 text-primary"></i> {{trans('general.full_access_content')}}</li>
                                                <li><i class="fa fa-check mr-2 text-primary"></i> {{trans('general.direct_message_with_this_user')}}</li>
                                                <li><i class="fa fa-check mr-2 text-primary"></i> {{trans('general.cancel_subscription_any_time')}}</li>
                                            </ul>

                                            <div class="flex-col-start flex-align-center mt-2">
                                                <a href="javascript:void(0);" data-id="{{ $user->id }}" id="subscribeFree" class="dark-btn py-2 px-3 mr-1">
                                                    <i class="feather icon-user-plus mr-1"></i> {{trans('general.subscribe_for_free')}}
                                                </a>

                                                <button type="button" class="btn e-none p-0 mt-2" data-dismiss="modal">{{trans('admin.cancel')}}</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Modal Subscription Free -->
                @endif








            </div>
        </div>
    </div>

@endsection


@section("script")
