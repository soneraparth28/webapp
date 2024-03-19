@extends('layouts.app')

@section('title') {{ __('general.shop') }} -@endsection

@section('content')
  <section class="section section-sm">
    <div class="container">
      <div class="row justify-content-center text-center mb-sm">
        <div class="col-lg-12 py-5">
          <h2 class="mb-0 text-break">{{ __('general.shop') }} @if (request('tags')) "{{ request('tags') }}" @endif</h2>
          <p class="lead text-muted m-0">{{trans('general.explore_products_creators')}}
            @guest
              @if ($settings->registration_active == '1')
                <a href="{{url('signup')}}" class="link-border">{{ trans('general.join_now') }}</a>
              @endif
          @endguest

          @if (auth()->check() && auth()->user()->verified_id == 'yes')
            <span class="d-block mt-2 w-100">

              @if ($settings->digital_product_sale && ! $settings->custom_content)
                <a class="btn btn-primary" href="{{ url('add/product') }}">
      						<i class="bi-plus"></i> {{ __('general.add_product') }}
                </a>

              @elseif (! $settings->digital_product_sale && $settings->custom_content)
                <a class="btn btn-primary" href="{{ url('add/custom/content') }}">
      						<i class="bi-plus"></i> {{ __('general.add_custom_content') }}
                </a>

              @else
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#addItemForm">
      						<i class="bi-plus"></i> {{ __('general.add_new') }}
                </a>
              @endif
            </span>

          @endif
        </p>
        </div>
      </div>

<div class="row">

@if ($products->total() != 0)
          <div class="col-md-12 mb-4">

            <div class="btn-block mb-3 text-right">
              <span>
                <i class="bi-filter-right mr-1"></i>

                <select class="ml-2 custom-select w-auto" id="filter">
                    <option @if (! request()->get('sort')) selected @endif value="{{url('shop')}}">{{trans('general.latest')}}</option>
                    <option @if (request()->get('sort') == 'oldest') selected @endif value="{{url('shop?sort=oldest')}}">{{trans('general.oldest')}}</option>
                    <option @if (request()->get('sort') == 'priceMin') selected @endif value="{{url('shop?sort=priceMin')}}">{{trans('general.lowest_price')}}</option>
                    <option @if (request()->get('sort') == 'priceMax') selected @endif value="{{url('shop?sort=priceMax')}}">{{trans('general.highest_price')}}</option>
                    <option @if (request()->get('sort') == 'digital') selected @endif value="{{url('shop?sort=digital')}}">{{trans('general.digital_products')}}</option>
                    <option @if (request()->get('sort') == 'custom') selected @endif value="{{url('shop?sort=custom')}}">{{trans('general.custom_content')}}</option>
                  </select>
              </span>
            </div>

            <div class="row">

              @foreach ($products as $product)
              <div class="col-md-4 mb-4">
                @include('shop.listing-products')
              </div><!-- end col-md-4 -->
              @endforeach

              @if ($products->hasPages())
                <div class="w-100 d-block">
                  {{ $products->onEachSide(0)->appends(['tags' => request('tags'), 'sort' => request('sort')])->links() }}
                </div>
              @endif
            </div><!-- row -->
          </div><!-- col-md-9 -->

        @else
          <div class="col-md-12">
            <div class="my-5 text-center no-updates">
              <span class="btn-block mb-3">
                <i class="feather icon-shopping-bag ico-no-result"></i>
              </span>
            <h4 class="font-weight-light">{{trans('general.no_results_found')}}</h4>
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>

@includeWhen(auth()->check() && auth()->user()->verified_id == 'yes', 'shop.modal-add-item')

@endsection
