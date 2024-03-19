<ul class="list-unstyled menu-left-home sticky-top">
	<li>
		<a href="{{url('/')}}" @if (request()->is('/')) class="active disabled" @endif>
			<i class="bi bi-house-door"></i>
			<span class="ml-2">{{ trans('admin.home') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url("creators") }}">
			<i class="bi bi-person"></i>
			<span class="ml-2">{{ trans("general.creators")  }}</span>
		</a>
	</li>
	@if (auth()->user()->verified_id == 'yes')
	<li>
		<a href="{{ url('dashboard') }}">
			<i class="bi bi-box "></i>
			<span class="ml-2">{{ trans('admin.dashboard') }}</span>
		</a>
	</li>
	@endif
    <li>
        <a href="{{ url('my/purchases') }}" @if (request()->is('my/purchases')) class="active disabled" @endif>
            <i class="bi bi-bag-check"></i>
            <span class="ml-2">{{ trans('general.purchased') }}</span>
        </a>
    </li>
	<li>
		<a href="{{ url('explore') }}" @if (request()->is('explore')) class="active disabled" @endif>
			<i class="bi bi-compass"></i>
			<span class="ml-2">{{ trans('general.explore') }}</span>
		</a>
	</li>
	<li>
		<a href="{{ url('my/bookmarks') }}" @if (request()->is('my/bookmarks')) class="active disabled" @endif>
			<i class="bi bi-bookmark"></i>
			<span class="ml-2">{{ trans('general.bookmarks') }}</span>
		</a>
	</li>

</ul>
