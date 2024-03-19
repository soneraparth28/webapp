<!-- Start Modal payPerViewForm -->
<div class="modal fade" id="customContentForm{{$sale->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<div class="modal-dialog modal- modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<div class="card bg-theme-card shadow border-0">

					<div class="card-body px-lg-5 py-lg-5 position-relative">

						<div class="mb-4 position-relative">
							 <strong>{{ __('general.details_custom_content') }}</strong>
							 <small data-dismiss="modal" class="btn-cancel-msg"><i class="bi bi-x-lg"></i></small>
						</div>

						<h6>
							{{ __('auth.email') }}:

							@if (! isset($sale->user()->email))
								{{ trans('general.no_available') }}
							@else
							{{ $sale->user()->email }}
						@endif
						</h6>

						<p>
							{!! Helper::checkText($sale->description_custom_content) !!}
						</p>

					</div><!-- End card-body -->
				</div><!-- End card -->
			</div><!-- End modal-body -->
		</div><!-- End modal-content -->
	</div><!-- End Modal-dialog -->
</div><!-- End Modal BuyNow -->
