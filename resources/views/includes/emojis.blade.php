<div class="container">
  <div class="flex-row-start flex-align-center flex-wrap">
		@foreach (Helper::emojis() as $emoji)
			<div class="m-1 cursor-pointer hover-opacity-8" style="width: 18px; height: 18px;">
	            <span class="emoji" data-emoji="{{$emoji}}">{{$emoji}}</span>
	        </div>
		@endforeach
  </div>
</div>
