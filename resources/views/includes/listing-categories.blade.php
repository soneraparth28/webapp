<style>
	#categories-listing-n img{
		max-height: 25px;
	    max-width: 25px;
	}

	#categories-listing-n span.image-container{
		width: 35px;
	    display: inline-block;
	    text-align: center;
	}


</style>

<button type="button" class="btn-menu-expand btn btn-primary btn-block mb-4 d-lg-none" type="button" data-toggle="collapse" data-target="#navbarUserHome" aria-controls="navbarCollapse" aria-expanded="false">
		<i class="fa fa-bars mr-2"></i> {{trans('general.categories')}}
	</button>

	<div class="navbar-collapse collapse d-lg-block" id="navbarUserHome">

		<span class="category-filter">
			<i class="bi bi-list-ul mr-2"></i> {{trans('general.categories')}}
		</span>

	<div class="py-1 mb-4">
	<div class="text-center" id="categories-listing-n">
        @php
        $emojiList = [
            "artists" => "🎨",
            "musicians" => "🎤",
            "courses" => "🎓",
            "personal trainers" => "🏋🏻‍♂️",
            "influencers" => "🌴",
        ];
        @endphp
		@foreach (Categories::where('mode','on')->orderBy('name')->get() as $category)
		<a class="text-muted btn btn-sm bg-theme-card border mb-2 e-none btn-category @if(Request::path() == "category/$category->slug" || Request::path() == "category/$category->slug/featured" || Request::path() == "category/$category->slug/more-active" || Request::path() == "category/$category->slug/new" || Request::path() == "category/$category->slug/free")active-category @endif" href="{{url('category', $category->slug)}}">
			<span class="image-container">
            <span class="mr-2 w-30px">{{$emojiList[strtolower($category->name)]}}</span>
{{--				<img src="{{url('public/img-category', $category->image)}}" class="mr-2 rounded" />--}}
			</span> {{ Lang::has('categories.' . $category->slug) ? __('categories.' . $category->slug) : $category->name }}
		</a>
	@endforeach
</div>
</div>
</div>
