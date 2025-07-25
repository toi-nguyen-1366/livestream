@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.profiles_social') }}</span>
  </h5>

<div class="content">
	<div class="row">

		<div class="col-lg-12">

			@if (session('success_message'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check2 me-1"></i>	{{ session('success_message') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  <i class="bi bi-x-lg"></i>
                </button>
                </div>
              @endif

              @include('errors.errors-forms')

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-5">

					 <form method="POST" action="{{ url('panel/admin/profiles-social') }}" enctype="multipart/form-data">
             @csrf

		        <div class="row mb-3">
		          <label class="col-sm-2 col-form-label text-lg-end">Facebook</label>
		          <div class="col-sm-10">
		            <input value="{{ $settings->facebook }}" name="facebook" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
		          </div>
		        </div>

           <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">X</label>
             <div class="col-sm-10">
               <input value="{{ $settings->twitter }}" name="twitter" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

           <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Instagram</label>
             <div class="col-sm-10">
               <input value="{{ $settings->instagram }}" name="instagram" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

           <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Linkedin</label>
             <div class="col-sm-10">
               <input value="{{ $settings->linkedin }}" name="linkedin" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

           <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Youtube</label>
             <div class="col-sm-10">
               <input value="{{ $settings->youtube }}" name="youtube" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

           <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Pinterest</label>
             <div class="col-sm-10">
               <input value="{{ $settings->pinterest }}" name="pinterest" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

					 <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Tiktok</label>
             <div class="col-sm-10">
               <input value="{{ $settings->tiktok }}" name="tiktok" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

					 <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Telegram</label>
             <div class="col-sm-10">
               <input value="{{ $settings->telegram }}" name="telegram" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

					 <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Reddit</label>
             <div class="col-sm-10">
               <input value="{{ $settings->reddit }}" name="reddit" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

					 <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Snapchat</label>
             <div class="col-sm-10">
               <input value="{{ $settings->snapchat }}" name="snapchat" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

					 <div class="row mb-3">
             <label class="col-sm-2 col-form-label text-lg-end">Github</label>
             <div class="col-sm-10">
               <input value="{{ $settings->github }}" name="github" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
             </div>
           </div>

           <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-lg-end">Threads</label>
            <div class="col-sm-10">
              <input value="{{ $settings->threads }}" name="threads" type="text" class="form-control" placeholder="{{ trans('admin.url_social') }}">
            </div>
          </div>

						<div class="row mb-3">
		          <div class="col-sm-10 offset-sm-2">
		            <button type="submit" class="btn btn-dark mt-3 px-5">{{ __('admin.save') }}</button>
		          </div>
		        </div>

		       </form>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
