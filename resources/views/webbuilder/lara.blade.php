<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<!-- begin::Head -->

<head><!--begin::Base Path (base relative path for assets of this page) -->
    <base href="{{ asset('public/backend/assets/builder/builder') }}"><!--end::Base Path -->
    <meta charset="utf-8" />
    <title>Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="" />

    <!-- App favicon -->
    <link rel="icon" href="">

    <link rel="stylesheet" href="./../builder/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" href="./../builder/css/lib/fx.css" />
    <link rel="stylesheet" href="./../builder/css/lib/spectrum.css" />
    <link rel="stylesheet" href="./../builder/css/lib/codemirror.css" />
    <link rel="stylesheet" href="./../builder/css/fonts.css" />
    <link rel="stylesheet" href="./../builder/css/main.css" />
    <link rel="stylesheet" href="./../builder/css/preloader.css" />
</head>

<body class="first-show">
    <script src="./../builder/js/lib/jquery-2.1.4.min.js"></script>
    <style id="builder-style"></style>
    <div class="supra-preloader">
        {{-- <img src="" style="max-height:150px;" alt="@lang('Project Creator')" /> --}}
        <div class="progress-bar-s">
            <div class="progress">
                <div class="load"></div>
            </div>
        </div>
        <div class="rights">
            <p>&copy; {{ date('Y') }}</p>
        </div>
    </div>
    <aside class="left supra black"></aside>
    <aside class="control-panel supra black">
        <div class="title d-flex justify-content-between align-items-center">
            <h3>@lang('Sections')</h3>
            <i class="supra bookmark"></i>
        </div>
        <ul class="sections">
            @foreach ($groups as $key => $node)
                <li data-group="{{ $key }}">{{ $node['name'] }}</li>
            @endforeach
        </ul>
    </aside>
    <div id="modal-thumb" class="supra">
        <div class="title">@lang('Page modals')</div>
        <div class="container-thumb"></div>
    </div>
    <div class="wrap-iframe d-flex justify-content-center align-items-center">
        <div class="wrap viewing-desctop">
            <label>
                <span class="width" contenteditable="true"></span> x <span class="height"
                    contenteditable="true"></span>
                <i class="rotate icon-blr-lg-mobile"></i>
            </label>

            <iframe id="main" src="{{ url('project/app-builder') }}"></iframe>

        </div>
    </div>
    <div id="modal-container" class="supra"></div>
    <div id="modal-project-container" class="supra"></div>
    <div id="modal-form-container" class="supra font-style-supra"></div>
    <div id="csrf_field" class="csrf_field" style="display: none">{{ csrf_field() }}</div>
    <div id="userId" class="userId" style="display: none">{{ Auth::user()->id }}</div>
    <div id="project_id" class="project_id" style="display: none">0</div>

    <script>
        localStorage.clear();
    </script>
    <script src="./../builder/js/lib/popper.min.js"></script>
    <script src="./../builder/js/lib/jquery.nicescroll.min.js"></script>

    <script src="./../builder/js/lib/tether.min.js"></script>
    <script src="./../builder/js/lib/bootstrap.min.js"></script>
    <script src="./../builder/js/lib/spectrum.js"></script>

    <script src="./../builder/js/lib/codemirror.js"></script>
    <script src="./../builder/js/lib/javascript.js"></script>
    <script src="./../builder/js/lib/css.js"></script>
    <script src="./../builder/js/lib/htmlmixed.js"></script>
    <script src="./../builder/js/lib/xml.js"></script>

    <script>
        @if (env('DEMO_MODE') == true)
            var demoMode = 'active';
        @else
            var demoMode = 'no';
        @endif
        var ajaxbase = '{{ url('api/ajax') }}';
        var baseurl = '{{ url('/') }}';
        var publicpath = "{{ base_path('public') }}";
        var basepath = "{{ base_path('public/backend/assets/builder') }}";
        var googleKey = ''; //'get_option('google_map_key')';
        var userId = '{{ Auth::user()->id }}';
        var project_id = 0;
        var project_file = '';
        var project_file_name = '';
    </script>
    <script src="./../builder/js/options.js"></script>
    <script src="./../builder/js/download.js"></script>
    <script src="./../builder/js/builder.min.js"></script>
    <script>
      $(document).ready(function() {
        let l = setInterval(() => {
          if (!$('.progress-bar-s').html()?.length) {
            $('#viewing-mobile').click();
            clearInterval(l);
          }
        }, 1000);
      });
    </script>

</body>

</html>
