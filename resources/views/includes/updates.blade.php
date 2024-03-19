
@foreach ($updates as $response)
	@php
		if (auth()->check()) {
			$checkUserSubscription = auth()->user()->checkSubscription($response->user());
			$checkPayPerView = auth()->user()->payPerView()->where('updates_id', $response->id)->first();
		}

		$totalLikes = number_format($response->likes()->count());
		$totalComments = number_format($response->comments()->count());

		$mediaCount = $response->media()->count();

		$allFiles = $response->media()->groupBy('type')->get();
		$getFirstFile = $allFiles->where('type', '<>', 'music')->where('type', '<>', 'file')->where('video_embed', '')->first();

		if ($getFirstFile && $getFirstFile->type == 'image') {
			$urlMedia =  url('media/storage/focus/photo', $getFirstFile->id);
			$backgroundPostLocked = 'background: url('.$urlMedia.') no-repeat center center #b9b9b9; background-size: cover;';
			$textWhite = 'text-white';

		} elseif ($getFirstFile && $getFirstFile->type == 'video' && $getFirstFile->video_poster) {
			$videoPoster = url('media/storage/focus/video', $getFirstFile->video_poster);
			$backgroundPostLocked = 'background: url('.$videoPoster.') no-repeat center center #b9b9b9; background-size: cover;';
			$textWhite = 'text-white';
		} else {
			$backgroundPostLocked = null;
			$textWhite = null;
		}
		$countFilesImage = $response->media()->where('image', '<>', '')->groupBy('type')->count();
		$countFilesVideo = $response->media()->where('video', '<>', '')->orWhere('video_embed', '<>', '')->where('updates_id', $response->id)->groupBy('type')->count();
		$countFilesAudio = $response->media()->where('music', '<>', '')->groupBy('type')->count();
		if($response->course === "yes" && !empty($response->image))
			$mediaImageVideo = $response->media()
			->where('image', $response->image)
			->get();
		else
			$mediaImageVideo = $response->media()
			->where('image', '<>', '')
			->orWhere('updates_id', $response->id)
			->where('video', '<>', '')
			->get();
		$mediaImageVideoTotal = $mediaImageVideo->count();

		$videoEmbed = $response->media()->where('video_embed', '<>', '')->get();
		$isVideoEmbed = false;

		if ($response->course !== "yes" && $videoEmbed->count() != 0) {
			foreach ($videoEmbed as $media) {
				$isVideoEmbed = $media->video_embed;
			}

		}
		$nth = 0; // nth foreach nth-child(3n-1)
	@endphp
	<div class="card border-radius-10px mt-3 card-updates @if ($response->status == 'pending') post-pending @endif @if ($response->fixed_post == '1' && request()->path() == $response->user()->username || auth()->check() && $response->fixed_post == '1' && $response->user()->id == auth()->user()->id) pinned-post @endif" data="{{$response->id}}" data-category-id="{{$response->update_category_id}}">
		<div class="card-body">
			<div class="pinned_post text-muted small w-100 mb-2 {{ $response->fixed_post == '1' && request()->path() == $response->user()->username || auth()->check() && $response->fixed_post == '1' && $response->user()->id == auth()->user()->id ? 'pinned-current' : 'display-none' }}">
				<i class="bi bi-pin mr-2"></i> {{ trans('general.pinned_post') }}
			</div>
			@if ($response->status == 'pending')
				<h6 class="text-muted w-100 mb-4">
					<i class="bi bi-eye-fill mr-1"></i> <em>{{ trans('general.post_pending_review') }}</em>
				</h6>
			@endif
			<div class="media">
				<span class="rounded-circle mr-3 position-relative">
					<a href="{{$response->user()->isLive() ? url('live', $response->user()->username) : url($response->user()->username)}}">
						{{--@if (auth()->check() && $response->user()->isLive())
							<span class="live-span">{{ trans('general.live') }}</span>
						@endif--}}
						<img src="{{ Helper::getFile(config('path.avatar').$response->user()->avatar) }}" alt="{{$response->user()->hide_name == 'yes' ? $response->user()->username : $response->user()->name}}" class="rounded-circle avatarUser" width="60" height="60">
					</a>
				</span>
				<div class="media-body">
					<h5 class="mb-0 font-montserrat">
						<a href="{{url($response->user()->username)}}">
							{{$response->user()->hide_name == 'yes' ? $response->user()->username : $response->user()->name}}
						</a>
						@if($response->user()->verified_id == 'yes')
							<small class="verified" title="{{trans('general.verified_account')}}"data-toggle="tooltip" data-placement="top">
								<i class="bi bi-patch-check-fill"></i>
							</small>
						@endif
						<small class="text-muted font-14">{{'@'.$response->user()->username}}</small>
						@if (auth()->check() && (auth()->user()->id == $response->user()->id || ($isMyOwnPage && $response->channel_id != "")))
							<a href="javascript:void(0);" class="text-muted float-right" id="dropdown_options" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								<i class="fa fa-ellipsis-h"></i>
							</a>
							<!-- Target -->
							<button class="d-none copy-url" id="url{{$response->id}}" data-clipboard-text="{{url($response->user()->username.'/post', $response->id)}}">{{trans('general.copy_link')}}</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_options">
								@if (request()->path() != $response->user()->username.'/post/'.$response->id)
									<a class="dropdown-item" href="{{url($response->user()->username.'/post', $response->id)}}"><i class="bi bi-box-arrow-in-up-right mr-2"></i> {{trans('general.go_to_post')}}</a>
								@endif
								@if ($response->status == 'active')
									<a class="dropdown-item pin-post" href="javascript:void(0);" data-id="{{$response->id}}">
										<i class="bi bi-pin mr-2"></i> {{$response->fixed_post == '0' ? trans('general.pin_to_your_profile') : trans('general.unpin_from_profile') }}
									</a>
								@endif
								<button class="dropdown-item" onclick="$('#url{{$response->id}}').trigger('click')"><i class="feather icon-link mr-2"></i> {{trans('general.copy_link')}}</button>
								@if($response->course === "yes")
									<button type="button" class="dropdown-item" data-href="{{url("course/" . $response->id . "/edit")}}">
										<i class="bi bi-pencil mr-2"></i> {{trans('general.edit_post')}}
									</button>
								@else
									<button type="button" class="dropdown-item" data-toggle="modal" data-target="#editPost{{$response->id}}">
										<i class="bi bi-pencil mr-2"></i> {{trans('general.edit_post')}}
									</button>
								@endif
								{!! Form::open(['method' => 'POST', 'url' => "update/delete/$response->id", 'class' => 'd-inline']) !!}
									@if (isset($inPostDetail))
										{!! Form::hidden('inPostDetail', 'true') !!}
									@endif
									{!! Form::button('<i class="feather icon-trash-2 mr-2"></i> '.trans('general.delete_post'), ['class' => 'dropdown-item actionDelete']) !!}
								{!! Form::close() !!}
							</div>
							<div class="modal fade modalEditPost" id="editPost{{$response->id}}" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header border-bottom-0">
											<h5 class="modal-title">{{trans('general.edit_post')}}</h5>
											<button type="button" class="close close-inherit" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">
													<i class="bi bi-x-lg"></i>
												</span>
											</button>
										</div>
										<div class="modal-body">
											<form method="POST" action="{{url('update/edit')}}" enctype="multipart/form-data" class="formUpdateEdit">
												@csrf
												<input type="hidden" name="id" value="{{$response->id}}" />
												<div class="card mb-4">
													<div class="blocked display-none"></div>
													<div class="card-body pb-0">
														<div class="media">
															<span class="rounded-circle mr-3">
																<img src="{{ Helper::getFile(config('path.avatar').auth()->user()->avatar) }}" class="rounded-circle" width="60" height="60">
															</span>
															<div class="media-body">
																<textarea name="description" rows="5" cols="40" placeholder="{{trans('general.write_something')}}" class="form-control textareaAutoSize border-0 updateDescription">{{$response->description}}</textarea>
															</div>
														</div><!-- media -->
														<div class="category-selection mb-2">
															<div class="col-12">
																<div class="row align-items-end">
																	<div class="col-12">
																		<select name="update_category" class="form-control update-category-list">
																			@if(isset($update_categories))
																				@foreach($update_categories as $c)
																					<option value="{{$c->id}}" @if($response->update_category_id == $c->id) selected @endif>{{$c->name}}</option>
																				@endforeach
																			@endif
																		</select>
																	</div>
																</div>
															</div>
														</div>
														<input class="custom-control-input d-none customCheckLocked" type="checkbox" {{$response->locked == 'yes' ? 'checked' : ''}}  name="locked" value="yes">
														<!-- Alert -->
														<div class="alert alert-danger my-3 display-none errorUdpate">
															<ul class="list-unstyled m-0 showErrorsUdpate small"></ul>
														</div><!-- Alert -->
													</div><!-- card-body -->
													<div class="card-footer bg-theme-card border-0 pt-0">
														<div class="flex-row-between flex-align-center">
															<div class="flex-row-start flex-align-center">
																<div class="form-group @if ($response->price == 0.00) display-none @endif price">
																	<div class="input-group mb-2">
																		<div class="input-group-prepend">
																			<span class="input-group-text">{{$settings->currency_symbol}}</span>
																		</div>
																		<input class="form-control isNumber" value="{{$response->price != 0.00 ? $response->price : null}}" autocomplete="off" name="price" placeholder="{{trans('general.price')}}" type="text">
																	</div>
																</div><!-- End form-group -->
																@if ($response->price == 0.00)
																	<button type="button" class="ml-2 btn btn-upload btn-tooltip e-none align-bottom setPrice @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill" data-toggle="tooltip" data-placement="top" title="{{trans('general.price_post_ppv')}}">
																		<i class="feather icon-tag f-size-25"></i>
																	</button>
																@endif
																@if ($response->price == 0.00)
																	<button type="button" class="ml-2 contentLocked btn e-none align-bottom @if (auth()->user()->dark_mode == 'off') text-primary @else text-white @endif rounded-pill btn-upload btn-tooltip {{$response->locked == 'yes' ? '' : 'unlock'}}" data-toggle="tooltip" data-placement="top" title="{{trans('users.locked_content')}}">
																		<i class="feather icon-{{$response->locked == 'yes' ? '' : 'un'}}lock f-size-25"></i>
																	</button>
																@endif
															</div>
															<div class="d-inline-block float-right">
																<button type="submit" class="ml-2 dark-btn py-1 px-2 btnEditUpdate"><i></i> {{trans('users.save')}}</button>
															</div>
														</div>
													</div><!-- card footer -->
												</div><!-- card -->
											</form>
										</div><!-- modal-body -->
									</div><!-- modal-content -->
								</div><!-- modal-dialog -->
							</div><!-- modal -->
						@endif
						@if(auth()->check()
							&& auth()->user()->id != $response->user()->id
							&& $response->locked == 'yes'
							&& $checkUserSubscription && $response->price == 0.00
							&& $response->channel_id == ""

							|| auth()->check()
							&& auth()->user()->id != $response->user()->id
							&& $response->locked == 'yes'
							&& $checkUserSubscription
							&& $response->price != 0.00
							&& $checkPayPerView
							&& $response->channel_id == ""

							|| auth()->check()
							&& auth()->user()->id != $response->user()->id
							&& $response->price != 0.00
							&& ! $checkUserSubscription
							&& $checkPayPerView
							&& $response->channel_id == ""

							|| auth()->check() && auth()->user()->id != $response->user()->id && auth()->user()->role == 'admin' && auth()->user()->permission == 'all' && $response->channel_id == ""
							|| auth()->check() && auth()->user()->id != $response->user()->id && $response->locked == 'no' && $response->channel_id == "")
							<a href="javascript:void(0);" class="text-muted float-right" id="dropdown_options" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								<i class="fa fa-ellipsis-h"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown_options">
								<!-- Target -->
								<button class="d-none copy-url" id="url{{$response->id}}" data-clipboard-text="{{url($response->user()->username.'/post', $response->id).Helper::referralLink()}}">
									{{trans('general.copy_link')}}
								</button>
								@if (request()->path() != $response->user()->username.'/post/'.$response->id)
									<a class="dropdown-item" href="{{url($response->user()->username.'/post', $response->id)}}">
										<i class="bi bi-box-arrow-in-up-right mr-2"></i> {{trans('general.go_to_post')}}
									</a>
								@endif
								<button class="dropdown-item" onclick="$('#url{{$response->id}}').trigger('click')">
									<i class="feather icon-link mr-2"></i> {{trans('general.copy_link')}}
								</button>
								<button type="button" class="dropdown-item" data-toggle="modal" data-target="#reportUpdate{{$response->id}}">
									<i class="bi bi-flag mr-2"></i>  {{trans('admin.report')}}
								</button>
							</div>
							<div class="modal fade modalReport" id="reportUpdate{{$response->id}}" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog modal-danger modal-sm">
									<div class="modal-content">
										<div class="modal-header">
											<h6 class="modal-title font-weight-light" id="modal-title-default"><i class="fas fa-flag mr-1"></i> {{trans('admin.report_update')}}</h6>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<i class="fa fa-times"></i>
											</button>
										</div>
										<!-- form start -->
										<form method="POST" action="{{url('report/update', $response->id)}}" enctype="multipart/form-data">
											<div class="modal-body">
												@csrf
												<!-- Start Form Group -->
												<div class="form-group">
													<label>{{trans('admin.please_reason')}}</label>
													<select name="reason" class="form-control custom-select">
														<option value="copyright">{{trans('admin.copyright')}}</option>
														<option value="privacy_issue">{{trans('admin.privacy_issue')}}</option>
														<option value="violent_sexual">{{trans('admin.violent_sexual_content')}}</option>
													</select>
												</div><!-- /.form-group-->
											</div><!-- Modal body -->
											<div class="modal-footer">
												<button type="button" class="btn border text-white" data-dismiss="modal">{{trans('admin.cancel')}}</button>
												<button type="submit" class="btn btn-xs btn-white sendReport ml-auto"><i></i> {{trans('admin.report_update')}}</button>
											</div>
										</form>
									</div><!-- Modal content -->
								</div><!-- Modal dialog -->
							</div><!-- Modal -->
						@endif
					</h5>
					<small class="timeAgo text-muted" data="{{date('c', strtotime($response->date))}}"></small>
					@if ($response->locked == 'no')
						<small class="text-muted type-post" title="{{trans('general.public')}}">
							<i class="iconmoon icon-WorldWide mr-1"></i>
						</small>
					@endif
					@if ($response->locked == 'yes')
						<small class="text-muted type-post" title="{{trans('users.content_locked')}}">
							<i class="feather icon-lock mr-1"></i>
							@if (auth()->check() && $response->price != 0.00 && $checkUserSubscription && ! $checkPayPerView || auth()->check() && $response->price != 0.00 && ! $checkUserSubscription && ! $checkPayPerView)
								{{ Helper::amountFormatDecimal($response->price) }}
							@elseif (auth()->check() && $checkPayPerView)
								{{ __('general.paid') }}
							@endif
						</small>
					@endif
				</div><!-- media body -->
			</div><!-- media -->
		</div><!-- card body -->
		@if (auth()->check() && auth()->user()->id == $response->user()->id
			|| $response->locked == 'yes' && $mediaCount != 0

			|| auth()->check() && $response->locked == 'yes'
			&& $checkUserSubscription
			&& $response->price == 0.00

			|| auth()->check() && $response->locked == 'yes'
			&& $checkUserSubscription
			&& $response->price != 0.00
			&& $checkPayPerView

			|| auth()->check() && $response->locked == 'yes'
			&& $response->price != 0.00
			&& ! $checkUserSubscription
			&& $checkPayPerView

			|| auth()->check() && auth()->user()->role == 'admin' && auth()->user()->permission == 'all'
			|| $response->locked == 'no' || $response->channel_id != "")

		    <input type="hidden" id="verified_or_not" name="verified_or_not" value="<?php echo auth()->user()->verified_id; ?>">
        	<?php if(auth()->user()->verified_id == 'yes'){  ?>

        		<?php

        		if($response->course == "yes"){?>
        			   <h3 style="line-height:50px;" >&nbsp;&nbsp;&nbsp;<?php echo $response->title ?></h3>
        		<?php } ?>

        		<div class="card-body pt-0 pb-3">
        			<p class="mb-0 update-text position-relative text-word-break">
        				{!! Helper::linkText(Helper::checkText($response->description, $isVideoEmbed ?? null)) !!}
        			</p>
        		</div>


        	<?php }else{ ?>
        		<div class="my-5 text-center no-updates" bis_skin_checked="1">
                    <span class="btn-block mb-3">
                        <i class="fa fa-photo-video ico-no-result"></i>
                    </span>
                    <h4 class="font-weight-light">No posts yet</h4>
                </div>
        	<?php } ?>


			<!--<div class="card-body pt-0 pb-3">-->






				<!--<p class="mb-0 update-text position-relative text-word-break">-->
				<!--	{!! Helper::linkText(Helper::checkText($response->description, $isVideoEmbed ?? null)) !!}-->
				<!--</p>-->
			<!--</div>-->
		@endif
		@if (auth()->check() && auth()->user()->id == $response->user()->id

			|| auth()->check() && $response->locked == 'yes'
			&& $checkUserSubscription
			&& $response->price == 0.00

			|| auth()->check() && $response->locked == 'yes'
			&& $checkUserSubscription
			&& $response->price != 0.00
			&& $checkPayPerView

			|| auth()->check() && $response->locked == 'yes'
			&& $response->price != 0.00
			&& ! $checkUserSubscription
			&& $checkPayPerView

			|| auth()->check() && auth()->user()->role == 'admin' && auth()->user()->permission == 'all'
			|| $response->locked == 'no' || $response->channel_id != "")
			<div class="btn-block position-relative">
				@if ($mediaImageVideoTotal <> 0)
					@include('includes.media-post')
				@endif
				@if($response->course != 'yes')
					@foreach ($response->media as $media)
						@if ($media->music != '')
							<div class="mx-3 border rounded @if ($mediaCount > 1) mt-3 @endif">
								<audio id="music-{{$media->id}}" class="js-player w-100 @if (!request()->ajax())invisible @endif" controls>
									<source src="{{ Helper::getFile(config('path.music').$media->music) }}" type="audio/mp3">
										Your browser does not support the audio tag.
								</audio>
							</div>
						@endif
						@if ($media->file != '')
							<a href="{{url('download/file', $response->id)}}" class="d-block text-decoration-none @if ($mediaCount > 1) mt-3 @endif">
								<div class="card mb-3 mx-3">
									<div class="row no-gutters">
										<div class="col-md-2 text-center bg-dark">
											<i class="far fa-file-archive m-4 color-light" style="font-size: 48px;"></i>
										</div>
										<div class="col-md-10">
											<div class="card-body">
												<h5 class="card-title color-dark text-truncate mb-0">
													{{ $media->file_name }}.zip
												</h5>
												<p class="card-text">
													<small class="text-muted">{{ $media->file_size }}</small>
												</p>
											</div>
										</div>
									</div>
								</div>
							</a>
						@endif
					@endforeach
				@endif
				@if ($isVideoEmbed)
					@if (in_array(Helper::videoUrl($isVideoEmbed), array('youtube.com','www.youtube.com','youtu.be','www.youtu.be')))
					<div class="embed-responsive embed-responsive-16by9 mb-2">
						<iframe class="embed-responsive-item" height="360" src="https://www.youtube.com/embed/{{ Helper::getYoutubeId($isVideoEmbed) }}" allowfullscreen></iframe>
					</div>
					@endif
					@if (in_array(Helper::videoUrl($isVideoEmbed), array('vimeo.com','player.vimeo.com')))
						<div class="embed-responsive embed-responsive-16by9">
							<iframe class="embed-responsive-item" src="https://player.vimeo.com/video/{{ Helper::getVimeoId($isVideoEmbed) }}" allowfullscreen></iframe>
						</div>
					@endif
				@endif
			</div><!-- btn-block -->
		@else
			<div class="btn-block p-sm text-center content-locked pt-lg pb-lg px-3 {{$textWhite}}" style="{{$backgroundPostLocked}}">
				<span class="btn-block text-center mb-3"><i class="feather icon-lock ico-no-result border-0 {{$textWhite}}"></i></span>
				@if ($response->user()->planActive() && $response->price == 0.00 || $response->user()->free_subscription == 'yes' && $response->price == 0.00)
					<a href="javascript:void(0);" @guest data-toggle="modal" data-target="#loginFormModal" @else @if ($response->user()->free_subscription == 'yes') data-toggle="modal" data-target="#subscriptionFreeForm" @else data-toggle="modal" data-target="#subscriptionForm" @endif @endguest class="dark-btn w-100">
						{{ trans('general.content_locked_user_logged') }}
					</a>
				@elseif ($response->user()->planActive() && $response->price != 0.00 || $response->user()->free_subscription == 'yes' && $response->price != 0.00)
					<a href="javascript:void(0);" @guest data-toggle="modal" data-target="#loginFormModal" @else @if ($response->status == 'active') data-toggle="modal" data-target="#payPerViewForm" data-mediaid="{{$response->id}}" data-price="{{Helper::amountFormatDecimal($response->price, true)}}" data-subtotalprice="{{Helper::amountFormatDecimal($response->price)}}" data-pricegross="{{$response->price}}" @endif @endguest class="dark-btn w-100">
						@guest
							{{ trans('general.content_locked_user_logged') }}
						@else
							@if ($response->status == 'active')
								<i class="feather icon-unlock mr-1"></i> {{ trans('general.unlock_post_for') }} {{Helper::amountFormatDecimal($response->price)}}
							@else
								{{ trans('general.post_pending_review') }}
							@endif
						@endguest
					</a>
				@else
					<a href="javascript:void(0);" class="dark-btn disabled w-100">
						{{ trans('general.subscription_not_available') }}
					</a>
				@endif
				<ul class="list-inline mt-3">
					@if ($mediaCount == 0)
						<li class="list-inline-item"><i class="bi bi-file-font"></i> {{ __('admin.text') }}</li>
					@endif
					@if ($mediaCount != 0)
						@foreach ($allFiles as $media)
							@if ($media->type == 'image')
								<li class="list-inline-item"><i class="feather icon-image"></i> {{$countFilesImage}}</li>
							@endif
							@if ($media->type == 'video')
								<li class="list-inline-item"><i class="feather icon-video"></i> {{$countFilesVideo}}</li>
							@endif
							@if ($media->type == 'music')
								<li class="list-inline-item"><i class="feather icon-mic"></i> {{$countFilesAudio}}</li>
							@endif
							@if ($media->type == 'file')
								<li class="list-inline-item"><i class="far fa-file-archive"></i> {{$media->file_size}}</li>
							@endif
						@endforeach
					@endif
				</ul>
			</div><!-- btn-block parent -->
		@endif
		@if ($response->status == 'active')
			@php
				$likeActive = auth()->check() && auth()->user()->likes()->where('updates_id', $response->id)->where('status','1')->first();
				$bookmarkActive = auth()->check() && auth()->user()->bookmarks()->where('updates_id', $response->id)->first();
				if(auth()->check() && auth()->user()->id == $response->user()->id
				|| auth()->check() && $response->locked == 'yes'
				&& $checkUserSubscription
				&& $response->price == 0.00
				|| auth()->check() && $response->locked == 'yes'
				&& $checkUserSubscription
				&& $response->price != 0.00
				&& $checkPayPerView

				|| auth()->check() && $response->locked == 'yes'
				&& $response->price != 0.00
				&& ! $checkUserSubscription
				&& $checkPayPerView

				|| auth()->check() && auth()->user()->role == 'admin' && auth()->user()->permission == 'all'
				|| auth()->check() && $response->locked == 'no' || $response->channel_id != "") {
					$buttonLike = 'likeButton';
					$buttonBookmark = 'btnBookmark';
				} else {
					$buttonLike = null;
					$buttonBookmark = null;
				}
			@endphp
			<div class="card-footer border-top-0 border-radius-10px">
				<div class="flex-row-between flex-align-center flex-nowrap font-18">
					<div class="flex-row-between flex-align-center flex-nowrap">
						<a href="javascript:void(0);" @guest data-toggle="modal" data-target="#loginFormModal" @endguest class="btnLike @if ($likeActive)active @endif {{$buttonLike}} flex-row-start flex-align-center" @auth data-id="{{$response->id}}" @endauth>
							<i class="@if($likeActive)fas @else far @endif fa-heart"></i>
							<small class="font-weight-bold countLikes ml-1">{{ $totalLikes == 0 ? null : $totalLikes }}</small>
						</a>
						@if($response->channel_id != "")
							<span class="ml-3 @auth @if (! isset($inPostDetail) && $buttonLike)  toggleComments @endif @endauth flex-row-start flex-align-center">
								<i class="far fa-comment"></i>
								<small class="font-weight-bold totalComments ml-1">{{ $totalComments == 0 ? null : $totalComments }}</small>
							</span>
						@endif
						<a href="javascript:void(0);" title="{{trans('general.share')}}" data-toggle="modal" data-target="#sharePost{{$response->id}}" class="pulse-btn text-decoration-none ml-3">
							<i class="feather icon-share"></i>
						</a>
						@auth
							@if (auth()->user()->id != $response->user()->id
								&& $checkUserSubscription && $response->price == 0.00
								&& $settings->disable_tips == 'off'

								|| auth()->user()->id != $response->user()->id
								&& $checkUserSubscription
								&& $response->price != 0.00
								&& $checkPayPerView
								&& $settings->disable_tips == 'off'

								|| auth()->check() && $response->locked == 'yes'
								&& $response->price != 0.00
								&& ! $checkUserSubscription
								&& $checkPayPerView
								&& $settings->disable_tips == 'off'

								|| auth()->user()->id != $response->user()->id
								&& $response->locked == 'no'
								&& $settings->disable_tips == 'off')
								<a href="javascript:void(0);" data-toggle="modal" title="{{trans('general.tip')}}" data-target="#tipForm" class="ml-3 text-decoration-none" @auth data-id="{{$response->id}}" data-cover="{{Helper::getFile(config('path.cover').$response->user()->cover)}}" data-avatar="{{Helper::getFile(config('path.avatar').$response->user()->avatar)}}" data-name="{{$response->user()->hide_name == 'yes' ? $response->user()->username : $response->user()->name}}" data-userid="{{$response->user()->id}}" @endauth>
									<i class="fa fa-coins m"></i> Give Tips
								</a>
							@endif
						@endauth
					</div>
					<a href="javascript:void(0);" @guest data-toggle="modal" data-target="#loginFormModal" @endguest class="@if ($bookmarkActive) color-light-gray @else color-dark @endif {{$buttonBookmark}}" @auth data-id="{{$response->id}}" @endauth>
						<i class="@if ($bookmarkActive)fas @else far @endif fa-bookmark"></i>
					</a>
				</div>
				<!-- Share modal -->
				<div class="modal fade" id="sharePost{{$response->id}}" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header border-bottom-0">
								<button type="button" class="close close-inherit" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true"><i class="bi bi-x-lg"></i></span>
								</button>
							</div>
							<div class="modal-body">
								<div class="container-fluid">
									<div class="row">
										<div class="col-md-3 col-6 mb-3">
											<a href="https://www.facebook.com/sharer/sharer.php?u={{url($response->user()->username.'/post', $response->id).Helper::referralLink()}}" title="Facebook" target="_blank" class="social-share text-muted d-block text-center h6">
												<i class="fab fa-facebook-square facebook-btn"></i>
												<span class="btn-block mt-3">Facebook</span>
											</a>
										</div>
										<div class="col-md-3 col-6 mb-3">
											<a href="https://twitter.com/intent/tweet?url={{url($response->user()->username.'/post', $response->id).Helper::referralLink()}}&text={{ e( $response->user()->hide_name == 'yes' ? $response->user()->username : $response->user()->name ) }}" data-url="{{url($response->user()->username.'/post', $response->id)}}" class="social-share text-muted d-block text-center h6" target="_blank" title="Twitter">
												<i class="fab fa-twitter twitter-btn"></i> <span class="btn-block mt-3">Twitter</span>
											</a>
										</div>
										<div class="col-md-3 col-6 mb-3">
											<a href="whatsapp://send?text={{url($response->user()->username.'/post', $response->id).Helper::referralLink()}}" data-action="share/whatsapp/share" class="social-share text-muted d-block text-center h6" title="WhatsApp">
												<i class="fab fa-whatsapp btn-whatsapp"></i> <span class="btn-block mt-3">WhatsApp</span>
											</a>
										</div>
										<div class="col-md-3 col-6 mb-3">
											<a href="sms://?body={{ trans('general.check_this') }} {{url($response->user()->username.'/post', $response->id).Helper::referralLink()}}" class="social-share text-muted d-block text-center h6" title="{{ trans('general.sms') }}">
												<i class="fa fa-sms"></i> <span class="btn-block mt-3">{{ trans('general.sms') }}</span>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@auth
					<div class="container-comments @if ( ! isset($inPostDetail)) display-none @endif">
						<div class="container-media">
							@if($response->comments()->count() != 0)
								@php
									$comments = $response->comments()->take($settings->number_comments_show)->orderBy('id', 'DESC')->get();
									$data = [];
									if ($comments->count()) {
										$data['reverse'] = collect($comments->values())->reverse();
									} else {
										$data['reverse'] = $comments;
									}
									$dataComments = $data['reverse'];
									$counter = ($response->comments()->count() - $settings->number_comments_show);
								@endphp
								@if (auth()->user()->id == $response->user()->id
									|| $response->locked == 'yes'
									&& $checkUserSubscription
									&& $response->price == 0.00

									|| $response->locked == 'yes'
									&& $checkUserSubscription
									&& $response->price != 0.00
									&& $checkPayPerView

									|| auth()->check() && $response->locked == 'yes'
									&& $response->price != 0.00
									&& ! $checkUserSubscription
									&& $checkPayPerView

									|| auth()->user()->role == 'admin'
									&& auth()->user()->permission == 'all'
									|| $response->locked == 'no' || $response->channel_id != "")
									@include('includes.comments')
								@endif
							@endif
						</div><!-- container-media -->
						@if (auth()->user()->id == $response->user()->id
							|| $response->locked == 'yes'
							&& $checkUserSubscription
							&& $response->price == 0.00

							|| $response->locked == 'yes'
							&& $checkUserSubscription
							&& $response->price != 0.00
							&& $checkPayPerView

							|| auth()->check() && $response->locked == 'yes'
							&& $response->price != 0.00
							&& ! $checkUserSubscription
							&& $checkPayPerView

							|| auth()->user()->role == 'admin'
							&& auth()->user()->permission == 'all'
							|| $response->locked == 'no' || $response->channel_id != "")
							<hr />
							<div class="alert alert-danger alert-small dangerAlertComments display-none">
								<ul class="list-unstyled m-0 showErrorsComments"></ul>
							</div><!-- Alert -->
							<div class="media position-relative">
								<div class="blocked display-none"></div>
								<span href="#" class="float-left">
									<img src="{{ Helper::getFile(config('path.avatar').auth()->user()->avatar) }}" class="rounded-circle mr-1 avatarUser" width="40">
								</span>
								<div class="media-body">
									<form action="{{url('comment/store')}}" method="post" class="comments-form">
										@csrf
										<input type="hidden" name="update_id" value="{{$response->id}}" />
										<div>
											<span class="triggerEmoji" data-toggle="dropdown">
												<i class="bi-emoji-smile"></i>
											</span>
											<div class="dropdown-menu dropdown-menu-right dropdown-emoji" aria-labelledby="dropdownMenuButton">
												@include('includes.emojis')
											</div>
										</div>
										<input type="text" name="comment" class="form-control comments emojiArea border-0" autocomplete="off" placeholder="{{trans('general.write_comment')}}">
									</form>
								</div>
							</div>
						@endif
					</div><!-- container-comments -->
				@endauth
			</div><!-- card-footer -->
		@endif
	</div><!-- card -->
@endforeach
@if (! isset($singlePost))
	<div class="card mb-3 pb-4 loadMoreSpin d-none rounded-large shadow-large">
		<div class="card-body">
			<div class="media">
				<span class="rounded-circle mr-3">
					<span class="item-loading position-relative loading-avatar"></span>
				</span>
				<div class="media-body">
					<h5 class="mb-0 item-loading position-relative loading-name"></h5>
					<small class="text-muted item-loading position-relative loading-time"></small>
				</div>
			</div>
		</div>
		<div class="card-body pt-0 pb-3">
			<p class="mb-1 item-loading position-relative loading-text-1"></p>
			<p class="mb-1 item-loading position-relative loading-text-2"></p>
			<p class="mb-0 item-loading position-relative loading-text-3"></p>
		</div>
	</div>
@endif
@php
	if (isset($ajaxRequest)) {
		$totalPosts = $total;
	} else {
		$totalPosts = $updates->total();
	}
@endphp
@if ($totalPosts > $settings->number_posts_show && $counterPosts >= 1)
	<button rel="next" class="btn btn-primary w-100 text-center loadPaginator d-none" id="paginator">
		{{trans('general.loadmore')}}
	</button>
@endif
@if(!isset($ajaxRequest))
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
     var varornot = $("#verified_or_not").val();
     if(varornot == "no"){
         $(".card-body").css('display','none');
         $('.card').css('border','none');
         $(".card-footer").css('display','none');
     }
</script>
@endif
