@extends('layouts.app')

@section('title'){{trans('general.live_streaming')}} {{trans('general.by')}} {{ '@'.$creator->username }} -@endsection

  @section('css')
    <script type="text/javascript">
        var liveOnline = {{ $live ? 'true' : 'false' }};
        @if ($live)
        var appIdAgora = '{{ $settings->agora_app_id }}'; // set app id
        var agorachannelName = '{{ $live->channel }}'; // set channel name
        var agoraToken = '{{ $live->agora_token }}'; // set channel name
        var creatorId = {{ $creator->id }};
        var userId = {{ auth()->id() }};
        var liveMode = true;
        /*var liveCreator = true;*/
        var liveCreator = {{ $creator->id == auth()->id() ? 'true' : 'false' }};
        /*var role = "host";*/
        var role = "{{ $creator->id == auth()->id() ? 'host' : 'audience' }}";
        var availability = '{{ $live->availability }}';
        @endif
    </script>

    @if ($live)
      <script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.19.0.js"></script>
    @endif
  @endsection

@section('content')
<section class="section pt-0 pb-0 h-100 w-100 section-msg position-fixed live-data" @if ($live) data="{{ $live->id}}" data-creator="{{ $creator->id}}" @endif>
      <div class="w-100 h-100">
        <div class="row justify-content-center h-100 position-relative">

          <div class="col-lg-9 h-100 p-0 liveContainerFullScreen" @if ($live) data-id="{{ $live->id }}" @endif>
            <div class="w-100 rounded-0 h-100 border-0 liveContainer @if (! $live) live_offline @endif" @if (! $live) style="background:url('{{Helper::getFile(config('path.avatar').$creator->avatar)}}') no-repeat center center; background-size: cover;" @endif>

              <div class="content @if (! $live) px-4 py-3 @endif d-scrollbars container-msg position-relative h-100">
                @if (! $live)
                  <div class="flex-column d-flex justify-content-center text-center h-100 text-content-live">
                    <div class="w-100">

                      @if (! $live && $creator->id == auth()->id())
                        <h2 class="mb-0 font-montserrat"><i class="bi bi-broadcast mr-2"></i> {{trans('general.stream_live')}}</h2>
                        <p class="lead mt-0">{{trans('general.create_live_stream_subtitle')}}</p>
                        <button class="btn btn-primary btn-sm w-small-100 btnCreateLive">
                          <i class="bi bi-plus-lg mr-1"></i> {{trans('general.create_live_stream')}}
                        </button>

                        <div class="mt-3 d-block ">
                          <a href="{{ url('/') }}" class="text-white"><i class="bi bi-arrow-left"></i> {{ trans('error.go_home') }}</a>
                        </div>

                      @elseif (! $live && $creator->id != auth()->id())

                        <h2 class="mb-0 font-montserrat"><i class="bi bi-broadcast mr-2"></i> {{trans('general.welcome_live_room')}}</h2>
                        @if ($checkSubscription)
                          <p class="lead mt-0">{{trans('general.info_offline_live')}}</p>

                          <div class="mt-3 d-block ">
                            <a href="{{ url('/') }}" class="text-white"><i class="bi bi-arrow-left"></i> {{ trans('error.go_home') }}</a>
                          </div>

                        @else
                          <p class="lead mt-0">{{trans('general.info_offline_live_non_subscribe')}}</p>
                          <a href="{{url($creator->username)}}" class="btn btn-primary btn-sm w-small-100">
                            <i class="feather icon-unlock mr-1"></i> {{trans('general.subscribe_now')}}
                          </a>

                          <div class="mt-3 d-block ">
                            <a href="{{ url('/') }}" class="text-white"><i class="bi bi-arrow-left"></i> {{ trans('error.go_home') }}</a>
                          </div>

                        @endif

                      @endif
                    </div>
                  </div><!-- flex-column -->
                @else

                <div class="live-top-menu position-absolute h-50px z-100 px-4 px-lg-2 py-4 py-lg-2" style="left: 15px; right: 15px; top: 0;">
                    <div class="w-100">
                        <div class="flex-row-between flex-align-center">
                            <div class="flex-row-start flex-align-center">
                                <div class="border-radius-50 flex-col-around flex-align-center mr-2" style="background: #E3493F; width: 44px; height: 44px;">
                                    <img src="{{Helper::getFile(config('path.avatar').$creator->avatar)}}" class="border-radius-50 avatar-live square-40">
                                </div>
                                <span class="font-weight-bold text-white text-shadow-sm d-lg-inline-block d-none">{{ $creator->username }}</span>
                                <span class="font-weight-bold text-white text-shadow-sm d-lg-none d-inline-block">{{ str_limit($creator->username, 7, '...') }}</span>

                                @if ($live && ! $paymentRequiredToAccess && $limitLiveStreaming)
                                    <small class="text-white text-shadow-sm limitLiveStreaming ml-2">
                                        <i class="bi bi-clock mr-1"></i> <span>{{ $limitLiveStreaming }}</span> {{ trans('general.minutes') }}
                                    </small>
                                @endif
                            </div>



                            <div class="flex-row-end flex-align-center">
                                <div class="flex-row-start color-light px-2 py-0 px-lg-3 py-lg-1 border-radius-5px font-16" style="background: #E3493F">
                                    <p  class="mb-0 live text-uppercase font-weight-bold color-light">{{ trans("general.live")  }}</p>
                                </div>
                                <div class="flex-row-start color-light bg-dark px-2 py-0 px-lg-3 py-lg-1 border-radius-5px font-16 ml-2">
                                    <i class="bi bi-eye color-light"></i>
                                    <p class="mb-0 color-light ml-2" id="liveViews">{{ $live->onlineUsers->count() }}</p>
                                </div>


                                @if ($creator->id == auth()->id())
                                    <div class="live-options text-shadow-sm ml-2" id="optionsLive" role="button" data-toggle="dropdown">
                                        <i class="bi bi-gear color-light font-16"></i>
                                    </div>

                                    <div class="dropdown-menu dropdown-menu-right menu-options-live mb-1" aria-labelledby="optionsLive">
                                        <div id="live-video-options">
                                            <a class="dropdown-item live-options" id="muteAudio">
                                                <i class="fas fa-volume-up font-16 mr-2"></i> <span>Mute audio</span>
                                            </a>
                                            <a class="dropdown-item live-options" id="muteVideo">
                                                <i class="bi bi-camera-video-fill font-16 mr-2"></i> <span>Mute video</span>
                                            </a>
                                        </div>
                                        <div class="dropdown-divider"></div>

                                        <div id="camera-list"></div>
                                        <div id="mic-list"></div>
                                    </div>

                                    <form method="POST" action="{{ url('end/live/stream', $live->id) }}" accept-charset="UTF-8" class="d-none" id="formEndLive">
                                        @csrf
                                    </form>
                                    <div class="close-live cursor-pointer text-shadow-sm ml-2" id="endLive" data-toggle="tooltip" data-placement="top" title="{{ trans('general.end_live') }}">
                                        <i class="bi bi-x-lg color-light font-16"></i>
                                    </div>
                                @else
                                    <a href="javascript:void(0);" class="exit-live text-shadow-sm ml-2" id="exitLive" data-toggle="tooltip" data-placement="top" title="{{ trans('general.exit_live_stream') }}">
                                        <i class="bi bi-x-lg color-light font-16"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                  <div id="full-screen-video" class="h-100"></div>

                @endif

              </div><!-- container-msg -->

              </div><!-- card -->
            </div><!-- end col-md-8 -->

          <!-- Chat Box -->
          <div class="col-lg-3 h-100 p-0 border-right wrapper-msg-inbox wrapper-live-chat position-relative">

          <div class="rounded-0 border-0" id="live-chat-container">

            <div class="w-100 py-3 px-3 border-bottom titleChat border-med-light-gray d-none d-lg-block">
            	<div class="w-100">
            		<span class="h5 align-top font-weight-bold">{{ trans('general.chat') }}</span>
              </div>
            </div>

              <div class="flex-col-end">
                  <div class="content px-4 py-3 d-scrollbars container-msg chat-msg" id="contentDIV">

                      <div class="div-flex"></div>

                      @if ($live && ! $paymentRequiredToAccess)
                          <ul class="list-unstyled mb-0" id="allComments">
                              @include('includes.comments-live')
                          </ul>
                      @endif


                  </div>

                  <div class=" px-3 py-2 bg-transparent position-relative @if (! $live) offline-live @endif" id="live-chat-msg-container">

                      <!-- Alert -->
                      <div class="alert alert-danger my-3 display-none" id="errorMsg">
                          <ul class="list-unstyled m-0" id="showErrorMsg"></ul>
                      </div><!-- Alert -->

                      <form action="{{ url('comment/live') }}" method="post" accept-charset="UTF-8" id="formSendCommentLive" enctype="multipart/form-data" class="flex-row align-items-center justify-content-start justify-content-lg-between">

                          @if ($live)
                              <input type="hidden" name="live_id" value="{{ $live->id }}">
                          @endif

                          @csrf

                              <div class="flex-row-start flex-align-center">
                                  <input type="text" class="form-control border-0" id="commentLive" placeholder="{{ trans('general.write_something') }}" name="comment" />
                              </div>


                              <div class="flex-row-end flex-align-center">
                                  @if ($creator->id != auth()->id())
                                      @if ($live && ! $paymentRequiredToAccess)
                                          <a href="javascript:void(0);" data-toggle="modal" title="{{trans('general.tip')}}" data-target="#tipForm" class="ml-3" id="live-stream-tip-btn" data-cover="{{Helper::getFile(config('path.cover').$creator->cover)}}" data-avatar="{{Helper::getFile(config('path.avatar').$creator->avatar)}}" data-name="{{$creator->hide_name == 'yes' ? $creator->username : $creator->name}}" data-userid="{{$creator->id}}">
                                              <i class="fa fa-coins"></i>
                                          </a>
                                      @endif
                                  @endif

                                  @if (! $paymentRequiredToAccess)
                                      <div class="flex-row-start flex-align-center flex-nowrap ml-3">
                                          <i class="bi bi-heart{{ $likeActive ? '-fill active' : null }} buttons-live button-like-live cursor-pointer"></i>
                                          <p class="mb-0 font-14 ml-1" id="counterLiveLikes">
                                              @if ($live && $likes != 0)
                                                  {{ $likes }}
                                              @endif
                                          </p>
                                      </div>
                                  @endif
                              </div>




                      </form>
                  </div>
              </div>

            </div><!-- end card -->

          </div><!-- end col-md-3 -->

          </div><!-- end row -->
        </div><!-- end container -->
</section>

@if ($live && $paymentRequiredToAccess)
  @include('includes.modal-pay-live')
@endif
@if ($live && !$paymentRequiredToAccess && $creator->id != auth()->id())
  @include('includes.modal-tip')
@endif

@endsection

@section('javascript')

@if ($live && $paymentRequiredToAccess)
    <script>
    // Payment Required
  		$('#payLiveForm').modal({
  				 backdrop: 'static',
  				 keyboard: false,
  				 show: true
  		 });

       //<---------------- Pay Live ----------->>>>
 			 $(document).on('click','#payLiveBtn',function(s) {

 				 s.preventDefault();
 				 var element = $(this);
 				 element.attr({'disabled' : 'true'});
 				 element.find('i').addClass('spinner-border spinner-border-sm align-middle mr-1');

 				 (function(){
 						$('#formPayLive').ajaxForm({
 						dataType : 'json',
 						success:  function(result) {

 							/*if (result.success) {
                                 if("url" in result) window.location = result.url;
                                 else window.location.reload();
 							}*/
              if(result.success == true && result.token != "") //Revolut
						{
							RevolutCheckout(result.token, result.mode).then(function (RC) {
		                
								var card = RC.createCardField({
									target: document.getElementById('card-field-livestream'),
									name: result.name,  // (mandatory!) name of the cardholder
									onSuccess() {  // Callback called when payment finished successfully
									///window.alert("Thank you!");
									//location.reload();
                  location.reload();
									},
									onError(message) {  // Callback in case some error happened
									window.alert("Payment failed! Reason: "+message);
									},
									onCancel() {  // (optional) Callback in case user cancelled a transaction
									//window.alert("Payment cancelled!");
									},
								});

								$('#payLiveForm').find('#livestream_details').hide();
								//$('#cardForm').modal('show');

								$('#payLiveForm').find('#spinner').show();

								setTimeout(function() {
									$('#payLiveForm').find('#spinner').hide();
									$('#payLiveForm').find('#card-fields').show();
									$('#payLiveForm').find('#cardholder_name').val(result.name);
									$('#payLiveForm').find("#card-field").addClass('form-group');
									$('#payLiveForm').find("#card-field").find('iframe input').css('height','25px');
								}, 1000);

								document.getElementById("go-to-livestream-payment").addEventListener("click", function () {
									card.submit({
										name: $('#payLiveForm').find('#cardholder_name').val()
									});
								});


							//	$('#subscriptionForm').modal('hide');
							

								var paymentRequest = RC.paymentRequest({
									target: document.getElementById('livestream-payment-request'),
									name: result.name,  // (mandatory!) name of the cardholder
									onSuccess() {  // Callback called when payment finished successfully
									///window.alert("Thank you!");
									//location.reload();
									window.location.href = result.url;
									},
									onError(message) {  // Callback in case some error happened
									window.alert("Payment failed! Reason: "+message);
									},
									onCancel() {  // (optional) Callback in case user cancelled a transaction
									//window.alert("Payment cancelled!");
									},
									buttonStyle: { size: 'small' }
								});

								paymentRequest.canMakePayment().then((method) => {
									if (method) {
									paymentRequest.render()
									} else {
									setResult('Not supported')
									paymentRequest.destroy()
									}
								})

							});
						} else {

 								if (result.errors) {

 									var error = '';
 									var $key = '';

 									for ($key in result.errors) {
 										error += '<li class="color-light"><i class="far fa-times-circle color-light"></i> ' + result.errors[$key] + '</li>';
 									}

 									$('#showErrorsPayLive').html(error);
 									$('#errorPayLive').show();
 									element.removeAttr('disabled');
 									element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
 								}
 							}

 						 },
 						 error: function(responseText, statusText, xhr, $form) {
 								 // error
 								 element.removeAttr('disabled');
 								 element.find('i').removeClass('spinner-border spinner-border-sm align-middle mr-1');
 								 swal({
 										 type: 'error',
 										 title: error_oops,
 										 text: error_occurred+' ('+xhr+')',
 									 });
 						 }
 					 }).submit();
 				 })(); //<--- FUNCTION %
 			 });//<<<-------- * END FUNCTION CLICK * ---->>>>
    </script>
  @endif

  @if ($live && ! $paymentRequiredToAccess)
    <script src="{{ asset('public/js/live.js') }}?v={{$settings->version}}"></script>
    <script src="{{ asset('public/js/agora/agora-admiral.js') }}"></script>

    @if ($creator->id == auth()->id() || ! $paymentRequiredToAccess)
      <script>
      // Start Live
      $(document).ready(async function() {
        await joinChannel();
      });
    	</script>
      @endif

  @endif
@endsection
