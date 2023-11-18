@extends('panel.layout.app')
@section('title', __('Custom Templates'))

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="items-center row g-2">
                <div class="col">
                    <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.index') ) }}" class="flex items-center page-pretitle">
                        <svg class="!me-2 rtl:-scale-x-100" width="8" height="10" viewBox="0 0 6 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.45536 9.45539C4.52679 9.45539 4.60714 9.41968 4.66071 9.36611L5.10714 8.91968C5.16071 8.86611 5.19643 8.78575 5.19643 8.71432C5.19643 8.64289 5.16071 8.56254 5.10714 8.50896L1.59821 5.00004L5.10714 1.49111C5.16071 1.43753 5.19643 1.35718 5.19643 1.28575C5.19643 1.20539 5.16071 1.13396 5.10714 1.08039L4.66071 0.633963C4.60714 0.580392 4.52679 0.544678 4.45536 0.544678C4.38393 0.544678 4.30357 0.580392 4.25 0.633963L0.0892856 4.79468C0.0357141 4.84825 0 4.92861 0 5.00004C0 5.07146 0.0357141 5.15182 0.0892856 5.20539L4.25 9.36611C4.30357 9.41968 4.38393 9.45539 4.45536 9.45539Z"/>
                        </svg>
                        {{__('Back to dashboard')}}
                    </a>
                    <h2 class="mb-3 page-title">
                        {{__('Custom Templates')}}
                    </h2>
					<p class="mb-2">{{__('Manage Custom Prompts and Templates')}}</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="pt-6 page-body">
        <div class="container-xl">
            <div class="mb-2">
                <a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.openai.custom.addOrUpdate') ) }}" class="btn btn-success">{{__('Add Template')}}</a>
            </div>
            <div class="card">
				<div id="table-default" class="card-table table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>{{__('Status')}}</th>
							<th>{{__('Template Name')}}</th>
							<th>{{__('Template Description')}}</th>
							<th>{{__('Package')}}</th>
							<th>{{__('Updated At')}}</th>
							<th>{{__('Actions')}}</th>
						</tr>
						</thead>
						<tbody class="align-middle table-tbody text-heading">
						@foreach($list as $entry)
							<tr>
								<td class="sort-status">
									@if($entry->active == 1)
										<div class="badge bg-success">{{__('Active')}}</div>
									@else
										<div class="badge bg-danger">{{__('Passive')}}</div>
									@endif
								</td>
								<td class="sort-name">{{$entry->title}}</td>
								<td class="sort-description">{{$entry->description}}</td>
								<style>
									.select-wrapper {
										position: relative;
									}
								
									.arrow-down {
										position: absolute;
										right: 10px;
										top: 50%;
										transform: translateY(-50%);
										width: 0;
										height: 0;
										border-left: 5px solid transparent;
										border-right: 5px solid transparent;
										border-top: 5px solid #ccc; /* Change the color as needed */
									}
								</style>
								<td class="sort-package">
									<div class="select-wrapper">
										@if(env('APP_STATUS') == 'Demo')
										<select class="form-control min-w-[90px]" onchange="return toastr.info('This feature is disabled in Demo version.');" name="premium" id="premium">
										@else
										<select class="form-control min-w-[90px]" onchange="return updatePackageStatus(this.value, {{$entry->id}});" name="premium" id="premium">
										@endif
											<option value="0" {{$entry->premium == 0 ? 'selected' : ''}}>{{__('Regular')}} </option>
											<option value="1" {{$entry->premium == 1 ? 'selected' : ''}}>{{__('Premium')}}</option>
										</select>
										<div class="arrow-down"></div>
									</div>
								</td>

								<td class="sort-date" data-date="{{strtotime($entry->updated_at)}}">
									<p class="m-0">{{date("j.n.Y", strtotime($entry->updated_at))}}</p>
									<p class="m-0 text-muted">{{date("H:i:s", strtotime($entry->updated_at))}}</p>
								</td>
								<td class="whitespace-nowrap">
									<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.openai.custom.addOrUpdate', $entry->id) ) }}" class="btn w-[36px] h-[36px] p-0 border hover:bg-[var(--tblr-primary)] hover:text-white" title="{{__('Edit')}}">
										<svg width="13" height="12" viewBox="0 0 16 15" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path d="M9.3125 2.55064L12.8125 5.94302M11.5 12.3038H15M4.5 14L13.6875 5.09498C13.9173 4.87223 14.0996 4.60779 14.224 4.31676C14.3484 4.02572 14.4124 3.71379 14.4124 3.39878C14.4124 3.08377 14.3484 2.77184 14.224 2.48081C14.0996 2.18977 13.9173 1.92533 13.6875 1.70259C13.4577 1.47984 13.1849 1.30315 12.8846 1.1826C12.5843 1.06205 12.2625 1 11.9375 1C11.6125 1 11.2907 1.06205 10.9904 1.1826C10.6901 1.30315 10.4173 1.47984 10.1875 1.70259L1 10.6076V14H4.5Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</a>
									<a href="{{ LaravelLocalization::localizeUrl( route('dashboard.admin.openai.custom.delete', $entry->id) ) }}" onclick="return confirm('{{__('Are you sure? This is permanent.')}}')" class="btn p-0 border w-[36px] h-[36px] hover:bg-red-600 hover:text-white" title="{{__('Delete')}}">
										<svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path d="M9.08789 1.74609L5.80664 5L9.08789 8.25391L8.26758 9.07422L4.98633 5.82031L1.73242 9.07422L0.912109 8.25391L4.16602 5L0.912109 1.74609L1.73242 0.925781L4.98633 4.17969L8.26758 0.925781L9.08789 1.74609Z"/>
										</svg>
									</a>
								</td>
							</tr>
						@endforeach

						</tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/panel/openai.js"></script>
@endsection
