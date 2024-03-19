<div class="progress-wrapper px-3 px-lg-0 display-none mt-1 mb-3" id="progress">
    <div class="progress-info">
      <div class="progress-percentage">
        <span class="percent">0%</span>
      </div>
    </div>
    <div class="progress progress-xs">
      <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
      <form method="POST" action="{{url('update/create')}}" enctype="multipart/form-data" id="formUpdateCreate" class="mt-3">
        @csrf
        <?php $channel = App\Models\Channels::where('id', app('request')->input('channel'))->first(); ?>
        @if($channel)
          <input type="hidden" name="channel_id" id="channel_id" value="{{ $channel->id }}">
        @endif
      <div class="card mb-4 card-border-0 rounded-large shadow-large">
        <div class="blocked display-none"></div>
        <div class="card-body pb-0">

          <div class="media">
          <span class="border-radius-50 mr-3">
      				<img src="{{ Helper::getFile(config('path.avatar').auth()->user()->avatar) }}" class="border-radius-50 avatarUser square-50">
      		</span>

          <div class="media-body position-relative">

            <textarea name="description" id="updateDescription" data-post-length="{{$settings->update_length}}" rows="4" cols="40" placeholder="{{trans('general.write_something')}}" class="form-control textareaAutoSize border-0 emojiArea"></textarea>
          </div>
        </div><!-- media -->
        <div class="category-selection flex-row-start flex-align-end flex-wrap" style="display: none;">
            <div class="flex-col-end">
                <label>Select Category:</label>
                <select name="update_category" class="form-control update-category-list">
                    <option value="">Click to select category</option>
                    @foreach($update_categories as $c)
                        <option value="{{$c->id}}">{{$c->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-col-end ml-2">

                <button type="button" class="show-add-new-update-category-section bg-dark color-light" style="font-size: 30px; border-radius: 27px; width: 48px;" title="Add New Category"><i class="bi bi-plus color-light"></i></button>
{{--                <div class="add-new-category-section" style="display: none">--}}
{{--                    <label>New Category:</label><br>--}}
{{--                    <input type="text" name="name" class="form-control d-inline-block new-category-name" placeholder="New Category Name" style="width:75%">--}}
{{--                    <button type="button" class="add-new-category" style="background-color: #5142fd; color: white; margin-left: 10px; font-size: 16px; border-radius: 27px; padding: 7px 14px;">Add</button>--}}
{{--                </div>--}}
            </div>
        </div>
            <input class="custom-control-input d-none" id="customCheckLocked" type="checkbox" {{auth()->user()->post_locked == 'yes' ? 'checked' : ''}} name="locked" value="yes">

          <!-- Alert -->
          <div class="alert alert-danger my-3 display-none" id="errorUdpate">
           <ul class="list-unstyled m-0" id="showErrorsUdpate"></ul>
         </div><!-- Alert -->

        </div>
        <div class="card-footer bg-theme-card border-top pt-0 rounded-large pb-0">
            <div class="form-group display-none" id="price" >
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{$settings->currency_symbol}}</span>
                    </div>
                    <input class="form-control isNumber" autocomplete="off" name="price" placeholder="{{trans('general.price')}}" type="text">
                </div>
            </div><!-- End form-group -->

            <div class="w-100">
                <span id="previewImage"></span>
                <a href="javascript:void(0)" id="removePhoto" class="text-danger p-1 px-2 display-none btn-tooltip-form" data-toggle="tooltip" data-placement="top" title="{{trans('general.delete')}}"><i class="fa fa-times-circle"></i></a>
            </div>

            <input type="file" name="photo[]" id="filePhoto" accept="image/*,video/mp4,video/x-m4v,video/quicktime,audio/mp3" multiple class="visibility-hidden filepond">

            <div class="flex-row-between flex-align-center position-relative">
                <div class="flex-row-start flex-align-center">

                      <a href="{{url("create/course")}}"  class="btn btn-upload d-none d-md-inline-block p-bottom-8 btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill"  data-toggle="tooltip" data-placement="top" title="{{trans('general.create_course_button_hover')}} ">
                          <i class="bi bi-mortarboard f-size-25"></i>
                      </a>

                    <button type="button" class="btn btn-upload btnMultipleUpload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_media')}} ({{ trans('general.media_type_upload') }})">
                        <!-- <i class="feather icon-image f-size-25"></i> -->
                        <i class="bi bi-images f-size-25"></i>
                    </button>

                    <input type="file" name="zip" id="fileZip" accept="application/x-zip-compressed" class="visibility-hidden">

                    <button type="button" class="btn btn-upload btn-tooltip-form p-bottom-8 e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.upload_file_zip')}}" onclick="$('#fileZip').trigger('click')">
                        <i class="bi bi-file-earmark-zip f-size-25"></i>
                    </button>

                    <button type="button" id="setPrice" class="btn btn-upload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.price_post_ppv')}}">
                        <!-- <i class="feather icon-tag f-size-25"></i> -->
                        <i class="bi bi-tags f-size-25"></i>
                    </button>

                    <button type="button" id="contentLocked" class="btn btn-upload btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill {{auth()->user()->post_locked == 'yes' ? '' : 'unlock'}}" data-toggle="tooltip" data-placement="top" title="{{trans('users.locked_content')}}">
                        <i class="feather icon-{{auth()->user()->post_locked == 'yes' ? '' : 'un'}}lock f-size-25"></i>
                    </button>

                    <!--@if ($settings->live_streaming_status == 'on')-->
                    <!--    <button type="button" data-toggle="tooltip" data-placement="top" title="{{trans('general.stream_live')}}" class="btn btn-upload p-bottom-8 btn-tooltip-form e-none align-bottom btnCreateLive @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">-->
                    <!--        <i class="bi bi-broadcast f-size-25"></i>-->
                    <!--    </button>-->
                    <!--@endif-->


                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-upload d-none d-md-inline-block p-bottom-8 btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">
                        <i class="bi-emoji-smile f-size-25"></i>
                    </button>

                    <button type="button" data-toggle="tooltip" data-placement="top" title="Attach Category" id="attach-category" class="btn btn-upload p-bottom-8 btn-tooltip-form e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill">
                        <i class="bi bi-collection f-size-25"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right dropdown-emoji" aria-labelledby="dropdownEmoji">
                        @include('includes.emojis')
                    </div>
                </div>

                <div class="position-relative w-100-mobile flex-row-end flex-align-center">

                    <div id="the-count" class="float-right mr-2">
                        <small id="maximum">{{$settings->update_length}}</small>
                    </div>

                    <div class="position-relative w-100-mobile">
                        <div class="btn-blocked display-none"></div>

                        <button type="submit" disabled class="dark-btn pt-1 pb-1 pr-2 pl-2 float-right e-none w-150px w-100-mobile" data-empty="{{trans('general.empty_post')}}" data-error="{{trans('general.error')}}" data-msg-error="{{trans('general.error_internet_disconnected')}}" id="btnCreateUpdate">
                          <i class="fas fa-arrow-right mr-2"></i> {{trans('general.publish')}}
                        </button>
                    </div>


                </div>

          </div>
        </div><!-- card footer -->
      </div><!-- card -->
    </form>
    <div class="alert alert-primary display-none card-border-0" role="alert" id="alertPostPending">
      <button type="button" class="close mt-1" id="btnAlertPostPending">
        <span aria-hidden="true">
          <i class="bi bi-x-lg"></i>
        </span>
      </button>

        <i class="bi bi-info-circle mr-1"></i> {{ trans('general.alert_post_pending_review') }}
        <a href="{{ url('my/posts') }}" class="link-border text-white">{{ trans('general.my_posts') }}</a>
    </div><!-- end announcements -->
