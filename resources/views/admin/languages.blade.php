@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('admin.languages') }}</span>

      <a href="{{ url('panel/admin/languages/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
				<i class="bi-plus-lg"></i> {{ trans('general.add_new') }}
			</a>
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

			<div class="card shadow-custom border-0">
				<div class="card-body p-lg-4">

          <div class="table-responsive p-0">
            <table class="table table-hover">
             <tbody>

              @if ($data->count() !=  0)
                 <tr>
                    <th class="active">ID</th>
                    <th class="active">{{ trans('admin.name') }}</th>
                    <th class="active">{{ trans('admin.abbreviation') }}</th>
                    <th class="active">{{ trans('admin.actions') }}</th>
                  </tr>

                @foreach( $data as $lang )
                  <tr>
                    <td>{{ $lang->id }}</td>
                    <td>{{ $lang->name }}</td>
                    <td>{{ strtolower($lang->abbreviation) }}</td>
                    <td>
                      <a href="{{ url('panel/admin/languages/edit', $lang->id) }}" class="btn btn-success rounded-pill btn-sm me-2">
                        <i class="bi-pencil"></i>
                      </a>

                   @if ($data->count() != 1)

                   <form method="POST" action="{{ url('panel/admin/languages', $lang->id) }}" id="form{{ $lang->id }}" class="d-inline-block align-top">
                    @csrf
                    <button type="submit" data-url="{{ $lang->id }}" class="btn btn-danger rounded-pill btn-sm actionDelete">
                        <i class="bi-trash-fill"></i>
                    </button>
                </form>
                    @endif

                  </td>

                  </tr><!-- /.TR -->
                  @endforeach

                  @else
                    <h5 class="text-center p-5 text-muted fw-light m-0">{{ trans('general.no_results_found') }}</h5>
                  @endif

                </tbody>
              </table>
          </div>

				 </div><!-- card-body -->
 			</div><!-- card  -->
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
