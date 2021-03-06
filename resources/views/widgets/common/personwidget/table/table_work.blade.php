@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@if (!$widget_error_count)
	<?php
		$WorkComposer['widget_data']['widgetlist']['work-pagination']->setPath(route('hr.person.works.index'));
	?>

	@section('widget_title')
		<h4> {!! $widget_title or 'Pekerjaan' !!} </h4>
		<small>Total data {{$WorkComposer['widget_data']['widgetlist']['work-pagination']->total()}}</small>

		@if(isset($WorkComposer['widget_data']['widgetlist']['active_filter']) && !is_null($WorkComposer['widget_data']['widgetlist']['active_filter']))
			 <div class="clearfix">&nbsp;</div>
			@foreach($WorkComposer['widget_data']['widgetlist']['active_filter'] as $key => $value)
				<span class="active-filter">{{$value}}</span>
			@endforeach
		@endif
	@overwrite

	@section('widget_body')	
		@if(isset($WorkComposer['widget_data']['widgetlist']['work']))
			<div class="clearfix">&nbsp;</div>
			<table class="table table-hover table-affix">
				<thead>
					<tr class="row">
						<th class="">No</th>
						<th class="">Perusahaan</th>
						<th class="">Jabatan (Status)</th>
						<th class="" colspan="2">Lama Bekerja</th>
						<th class="">&nbsp;</th>
					</tr>
				</thead>
				<?php $i = $WorkComposer['widget_data']['widgetlist']['work-display']['from'];?>
				<tbody>
					@forelse($WorkComposer['widget_data']['widgetlist']['work'] as $key => $value)
						<tr class="row">
							<td class="">
								{{$i}}
							</td>
							@if($value['chart'])
								<td class="">
									{{$value['chart']['branch']['organisation']['name']}} Cabang {{$value['chart']['branch']['name']}}
								</td>
								<td class="">
									{{$value['chart']['name']}} Departemen {{$value['chart']['tag']}} ({{$value['status']}})
								</td>
							@else
								<td class="">
									{{$value['organisation']}}
								</td>
								<td class="">
									{{$value['position']}} ({{$value['status']}})
								</td>
							@endif
							<td class="" colspan="2">
								{{ date('d-m-Y', strtotime($value['start'])) }} - 
								@if(is_null($value['end']))
									Sekarang
								@else
									{{ date('d-m-Y', strtotime($value['end'])) }}
								@endif
							</td>
							<td class="text-right">
								@if((int)Session::get('user.menuid') <= 2)
									<a href="javascript:;" class="btn btn-default" data-toggle="modal" data-target="#delete" data-delete-action="{{ route('hr.person.works.delete', [$value['id'], 'org_id' => $data['id'], 'person_id' => $person['id']]) }}"><i class="fa fa-trash"></i></a>
								@endif
								@if((int)Session::get('user.menuid')<=3)
									<a @if($value['chart']) href="{{route('hr.person.works.edit', [$value['id'], 'org_id' => $data['id'], 'person_id' => $person['id'], 'prev' => false])}}" @else href="{{route('hr.person.works.edit', [$value['id'], 'org_id' => $data['id'], 'person_id' => $person['id'], 'prev' => true])}}" @endif class="btn btn-default"><i class="fa fa-pencil"></i></a>
								@endif
							</td>
						</tr>
						<?php $i++;?>
					@empty
						<tr>
							<td class="text-center" colspan="5">Tidak ada data</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		@endif
	@overwrite
@else
	@section('widget_title')
	@overwrite

	@section('widget_body')
	@overwrite
@endif
