<!-- Start Modal payPerViewForm -->
<div class="modal fade" id="addItemForm" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<div class="modal-dialog modal- modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<div class="card bg-theme-card shadow border-0">

					<div class="card-body px-lg-5 py-lg-5 position-relative">

						<div class="mb-4 position-relative">
						<i class="bi-tag mr-1"></i>	<strong>{{ __('general.choose_type_sale') }}</strong>

						<small data-dismiss="modal" class="btn-cancel-msg"><i class="bi bi-x-lg"></i></small>
						</div>

						<a class="card choose-type-sale mb-3" href="{{ url('add/product') }}">
							<div class="card-body">
								<h6 class="mb-1"><i class="bi-cloud-download mr-2"></i> {{ __('general.digital_products') }}</h6>
								<small>{{ __('general.digital_products_desc') }}</small>
							</div>
						</a>

						<a class="card choose-type-sale" href="{{ url('add/custom/content') }}">
							<div class="card-body">
								<h6 class="mb-1"><i class="bi-lightning-charge mr-2"></i> {{ __('general.custom_content') }}</h6>
								<small>{{ __('general.custom_content_desc') }}</small>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!-- End Modal addItemForm -->