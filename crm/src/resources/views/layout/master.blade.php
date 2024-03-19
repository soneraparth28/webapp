<!doctype html>
<html lang="<?php  app()->getLocale(); ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>

    <link rel="shortcut icon" href="{{ url(config('settings.application.company_icon','/images/icon.png')) }}" />
    <link rel="apple-touch-icon" href="{{ url(config('settings.application.company_icon','/images/icon.png')) }}" />
    <link rel="apple-touch-icon-precomposed" href="{{ url(config('settings.application.company_icon','/images/icon.png')) }}" />

    <title>@yield('title') - {{ config('app.name') }}</title>
    @stack('before-styles')
    {{ style(mix('css/core.css')) }}
    {{ style(mix('css/fontawesome.css')) }}
    @stack('after-styles')
</head>
<body>
<div id="app" class="@yield('class')">
    @yield('master')
</div>
@guest()
    <script>
        window.localStorage.removeItem('permissions');
    </script>
@endguest

@auth()
    <script>

        @if(env('APP_INSTALLED') && auth()->user())
        window.localStorage.setItem('permissions', JSON.stringify(
                {!! json_encode(array_merge(
                    resolve(\App\Repositories\Core\Auth\UserRepository::class)->getPermissionsForFrontEnd(),
                    [
                        'is_app_admin' => auth()->user()->isAppAdmin(),
                        'is_brand_admin' => auth()->user()->isBrandAdmin(optional(brand())->id)
                    ])) !!}

        ))
        @endif

        window.onload = function () {
            window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
            })
        }
    </script>
@endauth

<script>
    window.localStorage.setItem('app-language', '{!! config('app.local') ?? "en" !!}');
    window.localStorage.setItem('app-languages',
        JSON.stringify(
                {!! json_encode(include resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.(config('app.local') ?? 'en').DIRECTORY_SEPARATOR.'default.php') !!}
        )
    );
    window.localStorage.setItem('base_url', '{{request()->root()}}');
    window.appLanguage = window.localStorage.getItem('app-language');
</script>
@stack('before-scripts')
{{ script(mix('js/manifest.js')) }}
{{ script(mix('js/vendor.js')) }}
{{ script(mix('js/core.js')) }}
{{ script('vendor/summernote/summernote-bs4.js') }}
@stack('after-scripts')
</body>
</html>

