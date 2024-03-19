@if ($mediaImageVideoTotal == 1)

	@foreach ($mediaImageVideo as $media)

		@if ($media->image != '')

	@php

        $urlImg = $urlImgLink = $media->img_type == 'gif' ? Helper::getFile(config('path.images').$media->image) : url("files/storage", $response->id).'/'.$media->image;
    if($response->course === "yes") {
        $urlImgLink = route("view-course",$response->id);
        $imageClass = "position-relative w-100";
    }
    else $imageClass = "glightbox w-100";

	@endphp

	<a href="{{ $urlImgLink }}" class="{{$imageClass}}" data-gallery="gallery{{$response->id}}">
		<img src="{{$urlImg}}?w=130&h=100" {!! $media->width ? 'width="'. $media->width .'"' : null !!} {!! $media->height ? 'height="'. $media->height .'"' : null !!} data-src="{{$urlImg}}?w=960&h=980" class="img-fluid lazyload d-inline-block w-100 post-image" alt="{{ e($response->description) }}">
    </a>

        @if($response->course === "yes")
            <div class="position-absolute flex-col-around flex-align-center cursor-pointer" data-href="{{$urlImgLink}}" style="top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0, .6)" >
                <div class="flex-col-start flex-align-center">
                    <p class="font-40 text-center font-weight-bold text-uppercase color-light" style="letter-spacing: 5px;">{{$response->title}}</p>
                    <p class="font-16 text-center text-uppercase color-light" style="letter-spacing: 8px;">Course</p>
                </div>
            </div>
        @endif
	@endif


	@if ($media->video != '')
	<video muted class="js-player w-100 @if (!request()->ajax())invisible @endif"  @if ($media->video_poster) data-poster="{{ Helper::getFile(config('path.videos').$media->video_poster) }}" @endif>
		<source src="{{ Helper::getFile(config('path.videos').$media->video) }}" type="video/mp4" />
	</video>
	@endif

 @endforeach

@endif

@if ($mediaImageVideoTotal >= 2)
<div class="container-post-media">

<div class="media-grid-{{ $mediaImageVideoTotal > 5 ? 5 : $mediaImageVideoTotal }}">

@foreach ($mediaImageVideo as $media)
	@php

	if ($media->type == 'video') {
		$urlMedia =  Helper::getFile(config('path.videos').$media->video);
		$videoPoster = $media->video_poster ? Helper::getFile(config('path.videos').$media->video_poster) : false;
	} else {
		$urlMedia =  $media->img_type == 'gif' ? Helper::getFile(config('path.images').$media->image) : url("files/storage", $response->id).'/'.$media->image;
		$videoPoster = null;
	}

		$nth++;
	@endphp

		@if ($media->type == 'image' || $media->type == 'video')
            <a href="{{ $urlMedia }}" class="media-wrapper glightbox d-inline-block" data-gallery="gallery{{$response->id}}"
               @if($nth <= 5) style="width: {{$mediaImageVideoTotal >= 5 ? "32%" : "49%"}}; aspect-ratio: 1 / 1; overflow: hidden;
               @if($media->type === "video" && !$videoPoster) background: var(--dark); opacity: .3;
               @elseif($nth === 5) background: var(--gray); text-decoration: none;
               @else background-image: url('{{ $videoPoster ?? $urlMedia }}?w=960&h=980'); background-position: center center; @endif "  @endif>

                @if ($nth === 5 && $mediaImageVideoTotal > 5)

                    <span class="w-100 h-100 flex-col-around flex-align-center">
                        <h2 class="color-light">+{{ $mediaImageVideoTotal - 4 }}</h2>
					</span>
                @elseif ($nth < 6 && $media->type == 'video')
                    <span class="w-100 h-100 flex-col-around flex-align-center">
						<i class="bi bi-play-fill color-light font-50"></i>
					</span>
                @endif
            </a>



		@endif




{{--        @if ($media->type == 'image' || $media->type == 'video')--}}

{{--            <a href="{{ $urlMedia }}" class="media-wrapper rounded-0 glightbox" data-gallery="gallery{{$response->id}}" style="background-image: url('{{ $videoPoster ?? $urlMedia}}?w=960&h=980')">--}}

{{--                @if ($nth == 5 && $mediaImageVideoTotal > 5)--}}
{{--                    <span class="more-media">--}}
{{--							<h2>+{{ $mediaImageVideoTotal - 5 }}</h2>--}}
{{--						</span>--}}
{{--                @endif--}}

{{--                @if ($media->type == 'video')--}}
{{--                    <span class="button-play">--}}
{{--						<i class="bi bi-play-fill text-white"></i>--}}
{{--					</span>--}}
{{--                @endif--}}

{{--                @if (! $videoPoster)--}}
{{--                    <video playsinline muted class="video-poster-html">--}}
{{--                        <source src="{{ $urlMedia }}" type="video/mp4" />--}}
{{--                    </video>--}}
{{--                @endif--}}

{{--                @if ($videoPoster)--}}
{{--                    <img src="{{ $videoPoster ?? $urlMedia }}?w=960&h=980" {!! $media->width ? 'width="'. $media->width .'"' : null !!} {!! $media->height ? 'height="'. $media->height .'"' : null !!} class="post-img-grid">--}}
{{--                @endif--}}
{{--            </a>--}}

{{--        @endif--}}

@endforeach

</div><!-- img-grid -->

</div><!-- container-post-media -->

@endif
