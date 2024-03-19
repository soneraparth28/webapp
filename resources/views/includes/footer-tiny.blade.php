<div class="card border-0 bg-transparent">
    <div class="card-body p-0">
        <small class="text-white">&copy; {{date('Y')}} {{$settings->title}}</small>
        <ul class="list-inline mb-0 small">

            <li class="list-inline-item"><a class="link-footer footer-tiny" target="_blank" href="https://mator.io/about-us">About Us</a></li>
            <li class="list-inline-item"><a class="link-footer footer-tiny" target="_blank" href="https://mator.io/terms-and-conditions">Terms and conditions</a></li>
            <li class="list-inline-item"><a class="link-footer footer-tiny" target="_blank" href="https://mator.io/privacy-policy">Privacy Policy</a></li>
            <li class="list-inline-item"><a class="link-footer footer-tiny" target="_blank" href="https://mator.io/cookiepolicy">Cookies Policy</a></li>
            <li class="list-inline-item"><a class="link-footer footer-tiny" target="_blank" href="https://mator.io/faq">FAQs</a></li>

            @if (App\Models\Blogs::count() != 0)
            <li class="list-inline-item"><a class="link-footer footer-tiny" href="{{ url('blog') }}">{{ trans('general.blog') }}</a></li>
            @endif

            @guest
            <div class="btn-group dropup d-inline">
                <li class="list-inline-item">
                    <a class="link-footer dropdown-toggle text-decoration-none footer-tiny" href="javascript:;" data-toggle="dropdown">
                        <i class="feather icon-globe"></i>
                        @foreach (Languages::orderBy('name')->get() as $languages)
                        @if( $languages->abbreviation == config('app.locale') ) {{ $languages->name }}  @endif
                        @endforeach
                    </a>

                    <div class="dropdown-menu">
                        @foreach (Languages::orderBy('name')->get() as $languages)
                        <a @if ($languages->abbreviation != config('app.locale')) href="{{ url('lang', $languages->abbreviation) }}" @endif class="dropdown-item mb-1 @if( $languages->abbreviation == config('app.locale') ) active text-white @endif">
                            @if ($languages->abbreviation == config('app.locale')) <i class="fa fa-check mr-1"></i> @endif {{ $languages->name }}
                        </a>
                        @endforeach
                    </div>
                </li>
            </div><!-- dropup -->
            @endguest

            <li class="list-inline-item">
                <div id="installContainer" class="display-none">
                    <a class="link-footer footer-tiny" id="butInstall" href="javascript:void(0);">
                        <i class="bi-phone"></i> {{ __('general.install_web_app') }}
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
