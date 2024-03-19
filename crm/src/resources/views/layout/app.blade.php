@extends('layout.master')

@push('before-styles')
    {{ style(mix('css/dropzone.css')) }}
    {{ style('vendor/summernote/summernote-bs4.css') }}
@endpush

@section('master')
    <div class="container-scroller">
        @section('top-bar')
            <app-top-navigation-bar logo-icon-src="{{ $logo_icon }}"
                                    :profile-data="{{ json_encode($top_bar_menu) }}">
            </app-top-navigation-bar>
        @show

        @section('side-bar')
            <sidebar :data="{{ json_encode($permissions)  }}"
                     logo-src="{{ $logo }}"
                     logo-icon-src="{{ $logo_icon }}"
                     logo-url="{{ request()->root()  }}">
            </sidebar>
        @show
        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">
                @yield('contents')
            </div>
        </div>
    </div>
@endsection

@push('before-scripts')

    <script>
        window.settings = {!! isset($settings) ? json_encode($settings): '{}' !!}
        window.user = <?php echo auth()->user()->load('profilePicture', 'roles:id,name') ?>
    </script>
@endpush
