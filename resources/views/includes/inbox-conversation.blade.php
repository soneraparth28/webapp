
<script type="text/javascript">
    var subscribed_active = {{ $subscribedToYourContent || $subscribedToMyContent ? 'true' : 'false' }};
    var user_id_chat = {{ $user->id }};
    var msg_count_chat = {{ $allMessages }};
</script>


<div class="p-4 border-bottom border-med-light-gray hideOnMobileBlock">
    <div class="flex-row-start flex-align-center">
        <a href="{{url($user->username)}}" class="">
          <span class="position-relative user-status @if ($user->active_status_online == 'yes') @if (Cache::has('is-online-' . $user->id)) user-online @else user-offline @endif @endif d-block">
            <img src="{{Helper::getFile(config('path.avatar').$user->avatar)}}" class="border-radius-10px square-50" />
          </span>
        </a>
        <div class="flex-col-start ml-3">
            <p class="font-22 font-weight-bold mb-0">{{$user->name}}</p>
            <p class="font-14 color-light-gray mb-0" @if($user->active_status_online == 'yes' && Cache::has('is-online-' . $user->id)) style="color: #4caf50 !important;" @endif>
                {{$user->active_status_online == 'yes' && Cache::has('is-online-' . $user->id) ? "Online" : "Offline"}}
            </p>
        </div>
    </div>
</div>



<div class="p-3 d-scrollbars content" id="contentDIV" data="{{$user->id}}" style="max-height: 65vh !important;">

    @if ($allMessages != 0)
        <div class="flex-column d-flex justify-content-center text-center h-100">
            <div class="w-100" id="loadAjaxChat">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    @endif
</div><!-- contentDIV -->



<div class="p-3  position-relative">

    @if ($subscribedToYourContent || $subscribedToMyContent)

        <div class="w-100 display-none" id="previewFile">
            <div class="previewFile d-inline"></div>
            <a href="javascript:;" class="text-danger" id="removeFile"><i class="fa fa-times-circle"></i></a>
        </div>

        <div class="progress-upload-cover" style="width: 0%; top:0;"></div>

        <div class="blocked display-none"></div>

        <!-- Alert -->
        <div class="alert alert-danger my-3" id="errorMsg" style="display: none;">
            <ul class="list-unstyled m-0" id="showErrorMsg"></ul>
        </div><!-- Alert -->

        <form action="{{url('message/send')}}" method="post" accept-charset="UTF-8" id="formSendMsg" enctype="multipart/form-data">
            <input type="hidden" name="id_user" id="id_user" value="{{$user->id}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="zip" id="zipFile" accept="application/x-zip-compressed" class="d-none">


            <div class="form-group display-none mt-2" id="price">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{$settings->currency_symbol}}</span>
                    </div>
                    <input class="form-control isNumber" autocomplete="off" name="price" placeholder="{{trans('general.price')}}" type="text">
                </div>
            </div><!-- End form-group -->

            <div class="w-100">
                <span id="previewImage"></span>
                <a href="javascript:void(0)" id="removePhoto" class="text-danger p-1 px-2 display-none btn-tooltip" data-toggle="tooltip" data-placement="top" title="{{trans('general.delete')}}"><i class="fa fa-times-circle"></i></a>
            </div>

            <input type="file" name="media[]" id="file" accept="image/*,video/mp4,video/x-m4v,video/quicktime,audio/mp3" multiple class="visibility-hidden filepond">






            <div class="flex-row-between flex-align-center flex-nowrap">
                <div class="flex-row-start flex-align-center flex-nowrap mr-3">

                    <div class="mr-2">
                        <div>
                            <div class="triggerEmoji" data-toggle="dropdown">
                              <i class="bi-emoji-smile font-25 cursor-pointer hover-opacity-8"></i>
                            </div>
                            <div class="dropdown-menu dropdown-menu-right dropdown-emoji" aria-labelledby="dropdownMenuButton">
                                @include('includes.emojis')
                            </div>
                        </div>
                    </div>

                    <i class="bi bi-plus-square font-25 cursor-pointer hover-opacity-8 ml-2" id="openMediaItemBox"></i>

                    <div class="flex-row-start flex-align-center">

                        <div class="flex-row-start flex-align-center flex-nowrap ml-2" id="mediaItemBox">
                            <i class="bi bi-image font-25 cursor-pointer hover-opacity-8 btnMultipleUpload ml-2"
                               data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_media')}} ({{ trans('general.media_type_upload') }})"></i>

                            <i class="bi bi-file-earmark-zip font-25 cursor-pointer hover-opacity-8 ml-2"
                               data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_file_zip')}}" onclick="$('#zipFile').trigger('click')"></i>

                            @if (auth()->user()->verified_id == 'yes')
                                <i class="bi bi-tags font-25 cursor-pointer hover-opacity-8 ml-2" id="setPrice"
                                   data-toggle="tooltip" data-placement="top" title="{{trans("general.set_price_for_msg")}}"></i>
                            @endif

                            @if ($user->verified_id == 'yes' && $settings->disable_tips == 'off')
                                <button type="button" class="btn btn-upload btn-tooltip e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="modal" title="{{trans('general.tip')}}" data-target="#tipForm" data-cover="{{Helper::getFile(config('path.cover').$user->cover)}}" data-avatar="{{Helper::getFile(config('path.avatar').$user->avatar)}}" data-name="{{$user->hide_name == 'yes' ? $user->username : $user->name}}" data-userid="{{$user->id}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                                        <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/>
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path fill-rule="evenodd" d="M8 13.5a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
                                    </svg>
                                </button>
                            @endif


                        </div>
                    </div>
                </div>
                <div class="flex-row-end flex-align-center flex-nowrap w-100 position-relative">
                    <textarea class="form-control textareaAutoSize emojiArea border-0 border-radius-10px" style="color: var(--dark) !important;" data-post-length="{{$settings->update_length}}"
                              rows="1" placeholder="{{trans('general.write_something')}}" id="message" name="message"></textarea>

                    <div class="position-absolute font-18 cursor-pointer hover-opacity-8" style="right: 20px; bottom: 10px;">
                        <i class="fas fa-arrow-circle-right"
                           id="button-reply-msg" disabled data-send="{{ trans('auth.send') }}" data-wait="{{ trans('general.send_wait') }}"></i>
                    </div>
                </div>
            </div>

        </form>
    @else
        <div class="alert alert-primary m-0 alert-dismissible fade show" role="alert">
            <i class="fa fa-info-circle mr-2"></i>
            @php
                $nameUser = $user->hide_name == 'yes' ? $user->username : $user->first_name;
            @endphp
            {!! trans('general.show_form_msg_error_subscription_', ['user' => '<a href="'.url($user->username).'" class="link-border text-white">'.$nameUser.'</a>']) !!}
        </div>
    @endif

</div>
