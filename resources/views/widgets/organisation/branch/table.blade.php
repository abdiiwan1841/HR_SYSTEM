@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@if (!$widget_error_count)

	@section('widget_title')
	<h1> {!! $widget_title  or 'Cabang' !!} </h1>
	<small>Total data {{$BranchComposer['widget_data']['branchlist']['branch-pagination']->total()}}</small>
	<?php $BranchComposer['widget_data']['branchlist']['branch-pagination']->setPath(route('hr.branches.index')); ;?>

	<div class="clearfix">&nbsp;</div>
	@if(!is_null($BranchComposer['widget_data']['branchlist']['active_filter']))
		@foreach($BranchComposer['widget_data']['branchlist']['active_filter'] as $key => $value)
			<span class="active-filter">{{$value}}</span>
		@endforeach
	@endif
	@overwrite

	@section('widget_body')
		<a href="{{ $BranchComposer['widget_data']['branchlist']['route_create'] }}" class="btn btn-primary">Tambah Data</a>
		@if(isset($BranchComposer['widget_data']['branchlist']['branch']))
			<div class="clearfix">&nbsp;</div>
			<div class="table-responsive">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th class="col-sm-1">No</th>
							<th class="col-sm-2">Nama</th>
							<th class="col-sm-2">Nomor Telepon</th>
							<th class="col-sm-4">Alamat</th>
							<th class="col-sm-3">&nbsp;</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="table-responsive div-table-content">
				<table class="table table-condensed table-hover">
					<?php $i = $BranchComposer['widget_data']['branchlist']['branch-display']['from'];?>
					@foreach($BranchComposer['widget_data']['branchlist']['branch'] as $key => $value)
						<tbody>
							<tr>
								<td class="col-sm-1">
									{{$i}}
								</td>
								<td class="col-sm-2">
									{{$value['name']}}
								</td>
								<td class="col-sm-2">
									@foreach($value['contacts'] as $key2 => $value2)
										@if((strtolower($value2['item'])=='phone')||(strtolower($value2['item'])=='mobile'))
											{{$value2['value']}}
										@endif
									@endforeach
								</td>
								<td class="col-sm-4">
									@foreach($value['contacts'] as $key2 => $value2)
										@if(strtolower($value2['item'])=='address')
											{{$value2['value']}}
										@endif
									@endforeach
								</td>
								<td class="col-sm-3 text-center" style="padding-right:30px;">
									<div class="btn-group">
										<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pengaturan <span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li>
												<a href="javascript:;" data-toggle="modal" data-target="#delete" data-delete-action="{{ route('hr.branches.delete', [$value['id'], 'org_id' => $data['id']]) }}" title="hapus"><i class="fa fa-trash"></i> Hapus</a>
											</li>
											<li>
												<a href="{{route('hr.branches.edit', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}" title="ubah"><i class="fa fa-pencil"></i> Ubah</a>
											</li>
											<li>
												<a href="{{route('hr.branches.show', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}" title="lihat"><i class="fa fa-eye"></i> Detail</a>
											</li>
											<li>
												<a href="{{route('hr.branch.charts.index', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}" title="struktur organisasi"><i class="fa fa-sitemap"></i> Struktur Organisasi</a>
											</li>
											<li>
												<a href="{{route('hr.branch.apis.index', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}" title="pengaturan api key"><i class="fa fa-key"></i> Pengaturan Api Key</a>
											</li>
											<li>
												<a href="{{route('hr.branch.contacts.index', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}" title="kontak"><i class="fa fa-phone"></i> Kontak</a>
											</li>
											{{-- <li>
												<a href="{{route('hr.branch.fingers.index', [$value['id'], 'org_id' => $data['id'], 'branch_id' => $value['id']])}}"><i class="fa fa-eye"></i></a>
											</li> --}}
										</ul>
									</div>
								</td>
							</tr>
						</tbody>
						<?php $i++;?>
					@endforeach
				</table>
			</div>

			<div class="row">
				<div class="col-sm-12 text-center">
					@if($BranchComposer['widget_data']['branchlist']['branch-pagination']->total()>0)
						<p>Menampilkan {!!$BranchComposer['widget_data']['branchlist']['branch-display']['from']!!} - {!!$BranchComposer['widget_data']['branchlist']['branch-display']['to']!!}</p>
						{!!$BranchComposer['widget_data']['branchlist']['branch-pagination']->appends(Input::all())->render()!!}
					@else
						Tidak ada data
					@endif
				</div>
			</div>

			<div class="clearfix">&nbsp;</div>
		@endif
		</div>
		</div>
	@overwrite
@else
	@section('widget_title')
	@overwrite

	@section('widget_body')
	@overwrite
@endif