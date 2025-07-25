@extends('admin.layout')

@section('content')
	<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
      <i class="bi-chevron-right me-1 fs-6"></i>
      <span class="text-muted">{{ __('general.posts') }} ({{$data->total()}})</span>
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

					@if ($data->total() !=  0 && $data->count() != 0)
					<div class="d-lg-flex justify-content-lg-between align-items-center mb-2 w-100">
						<form action="{{ url('panel/admin/posts') }}" id="formSort" method="get">
							 <select name="sort" id="sort" class="form-select d-inline-block w-auto filter">
									<option @if ($sort == '') selected="selected" @endif value="">{{ __('admin.sort_id') }}</option>
									<option @if ($sort == 'pending') selected="selected" @endif value="pending">{{ __('admin.pending') }}</option>
								</select>
								</form><!-- form -->
						</div>
						@endif

					<div class="table-responsive p-0">
						<table class="table table-hover">
						 <tbody>

							@if ($data->count() !=  0)
								 <tr>
									  <th class="active">ID</th>
										<th class="active">{{ __('admin.description') }}</th>
										<th class="active">{{ __('admin.content') }}</th>
										<th class="active">{{ __('admin.type') }}</th>
										<th class="active">{{ __('general.creator') }}</th>
										<th class="active">{{ __('admin.date') }}</th>
										<th class="active">{{ __('admin.status') }}</th>
										<th class="active">{{ __('admin.actions') }}</th>
									</tr>

								@foreach ($data as $post)

									@php
										$allFiles = $post->media()->groupBy('type')->get();
									@endphp

									<tr>
										<td>{{ $post->id }}</td>
										<td>{{ str_limit($post->description, 40, '...') }}</td>

										<td>
											@if ($allFiles->count() != 0)
												@foreach ($allFiles as $media)

													@if ($media->type == 'image')
														<i class="far fa-image"></i>
													@endif

													@if ($media->type == 'video')
														<i class="far fa-play-circle"></i>
													@endif

													@if ($media->type == 'music')
														<i class="fa fa-microphone"></i>
														@endif

														@if ($media->type == 'file')
													<i class="far fa-file-archive"></i>
													@endif

													@if ($media->type == 'epub')
													<i class="fas fa-book-open"></i>
													@endif

												@endforeach

											@else
												<i class="fa fa-font"></i>
											@endif
										</td>

										<td>{{ $post->locked == 'yes' ? __('users.content_locked') : __('general.public') }}</td>
										<td>
											@if (isset($post->user()->username))
												<a href="{{url($post->user()->username)}}" target="_blank">
													{{$post->user()->username}} <i class="fa fa-external-link-square-alt"></i>
												</a>
											@else
												<em>{{ __('general.no_available') }}</em>
											@endif

											</td>
										<td>{{ Helper::formatDate($post->date) }}</td>
										<td>
											@switch($post->status)
												@case('active')
												<span class="rounded-pill badge bg-success">
													{{ __('admin.active') }}
												</span>
													@break

												@case('pending')
													<span class="rounded-pill badge bg-warning">
													{{ __('admin.pending') }}
													</span>
													@break

												@case('encode')
												<span class="rounded-pill badge bg-info">
													{{ __('general.encode') }}
													</span>
													@break

												@case('schedule')
												<span class="rounded-pill badge bg-info">
													{{ __('general.scheduled') }}
													</span>
													<a tabindex="0" role="button" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="top" data-bs-content="{{ __('general.date_schedule') }} {{ Helper::formatDateSchedule($post->scheduled_date) }}">
														<i class="far fa-question-circle"></i>
													  </a>
													@break
											@endswitch
											</td>
										<td>
											<div class="d-flex">
											@if (isset($post->user()->username) && $post->status != 'encode')
											<a href="{{ url($post->user()->username, 'post').'/'.$post->id }}" target="_blank" class="btn btn-success btn-sm rounded-pill me-2" title="{{ __('admin.view') }}">
												<i class="bi-eye"></i>
											</a>
										@endif

											@if ($post->status == 'pending')
											<form method="POST" action="{{ url('panel/admin/posts/approve/'.$post->id) }}" class="displayInline">
												@csrf
												<button type="submit" class="btn btn-success btn-sm padding-btn rounded-pill me-2 actionApprovePost">
													{{ __('admin.approve') }}
												</button>
											</form>
											@endif

											<form method="POST" action="{{ url('panel/admin/posts/delete/'.$post->id) }}" class="displayInline">
												@csrf
												@if ($post->status == 'active' || $post->status == 'encode' || $post->status == 'schedule')
													<button type="submit" class="btn btn-danger btn-sm padding-btn rounded-pill actionDelete">
														<i class="bi-trash-fill"></i>
													</button>
												@else
													<button type="submit" class="btn btn-danger btn-sm padding-btn rounded-pill actionDeletePost">
														{{ __('general.reject') }}
													</button>
												@endif
											</form>

									 </div>

												</td>

									</tr><!-- /.TR -->
									@endforeach

									@else
										<h5 class="text-center p-5 text-muted fw-light m-0">{{ __('general.no_results_found') }}</h5>
									@endif

								</tbody>
								</table>
							</div><!-- /.box-body -->

				 </div><!-- card-body -->
 			</div><!-- card  -->

		@if ($data->lastPage() > 1)
			{{ $data->appends(['sort' => $sort])->onEachSide(0)->links() }}
		@endif
 		</div><!-- col-lg-12 -->

	</div><!-- end row -->
</div><!-- end content -->
@endsection
