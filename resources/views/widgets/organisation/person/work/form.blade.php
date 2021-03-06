@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))
@if (!$widget_error_count)
	@section('widget_title')
		<h1> {{ is_null($id) ? 'Tambah Pekerjaan ' : 'Ubah Pekerjaan '}} "{{$person['name']}}" </h1> 
	@overwrite

	@section('widget_body')
		<div class="clearfix">&nbsp;</div>
		{!! Form::open(['url' => $WorkComposer['widget_data']['worklist']['form_url'], 'class' => 'form no_enter']) !!}	
			<div class="form-group">
				<label class="control-label">Posisi</label>
				@include('widgets.organisation.branch.chart.select', [
					'widget_options'		=> 	[
													'chartlist'			=>
													[
														'organisation_id'	=> $data['id'],
														'search'			=> ['notadmin' => true, 'withattributes' => ['branch'], 'child' => (Session::get('user.menuid')==4 ? [Session::get('user.chartpath'), Session::get('user.workid')] : '' )],
														'sort'				=> ['name' => 'asc'],
														'page'				=> 1,
														'per_page'			=> 100,
														'chart_id'			=> $WorkComposer['widget_data']['worklist']['work']['chart_id'],
														'tabindex'			=> 1,
														'class_name'		=> 'select_chart'
													]
												]
				])
			</div>
			<div class="form-group">
				<label class="control-label">Calendar</label>
				@include('widgets.organisation.calendar.select', [
					'widget_options'		=> 	[
													'calendarlist'			=>
													[
														'organisation_id'	=> $data['id'],
														'search'			=> [],
														'sort'				=> ['name' => 'asc'],
														'page'				=> 1,
														'per_page'			=> 100,
														'calendar_id'		=> $WorkComposer['widget_data']['worklist']['work']['calendar_id'],
														'tabindex'			=> 2,
														'class_name'		=> 'select_follow'
													]
												]
				])
			</div>
			<div class="form-group">
				<label class="control-label">Status</label>
				<?php 
					if (Session::get('user.menuid')==1)
					{
						$status_work = ['admin' => 'Admin', 'contract' => 'Kontrak', 'internship' => 'Magang', 'probation' => 'Probation', 'permanent' => 'Tetap', 'others' => 'Lainnya'];
					}
					else 
					{
						$status_work = ['contract' => 'Kontrak', 'internship' => 'Magang', 'probation' => 'Probation', 'permanent' => 'Tetap', 'others' => 'Lainnya'];
					}
				?>
				{!!Form::select('status', $status_work, $WorkComposer['widget_data']['worklist']['work']['status'], ['class' => 'form-control select2', 'tabindex' => 3]) !!}
			</div>
			@if (!$id)
				<div class="form-group">
					<label class="control-label">Cuti</label>
					<select name="workleave_id" class="form-control select2 select-chart-workleave" tabindex="4"></select>
				</div>
			@endif
			<div class="form-group mt-30 mb-30">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('is_absence', '1', $WorkComposer['widget_data']['worklist']['work']['is_absence'], ['class' => '', 'tabindex' => '5']) !!} Tidak Perlu Absen
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label">Grade</label>
				{!!Form::input('text', 'grade', $WorkComposer['widget_data']['worklist']['work']['grade'], ['class' => 'form-control', 'tabindex' => '6'])!!}							
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="control-label">Start</label>
						@if (isset($WorkComposer['widget_data']['worklist']['work']['start'])&&($WorkComposer['widget_data']['worklist']['work']['start']!=null))
							<?php $date_start = date('d-m-Y', strtotime($WorkComposer['widget_data']['worklist']['work']['start'])); ?>
						@else
							<?php $date_start = null; ?>
						@endif
						{!!Form::input('text', 'start', $date_start, ['class' => 'form-control date-mask', 'tabindex' => 7])!!}
					</div>	
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="control-label">End</label>
						@if (isset($WorkComposer['widget_data']['worklist']['work']['end'])&&($WorkComposer['widget_data']['worklist']['work']['end']!=null))
							<?php $date_end = date('d-m-Y', strtotime($WorkComposer['widget_data']['worklist']['work']['end'])); ?>
						@else
							<?php $date_end = null; ?>
						@endif
						{!!Form::input('text', 'end', $date_end, ['class' => 'form-control date-mask', 'tabindex' => 8])!!}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label">Alasan Berhenti</label>
				{!!Form::textarea('reason_end_job', $WorkComposer['widget_data']['worklist']['work']['reason_end_job'], ['class' => 'form-control', 'tabindex' => 9])!!}
			</div>


			<div class="form-group text-right">
				<a href="{{ $WorkComposer['widget_data']['worklist']['route_back'] }}" class="btn btn-default mr-5" tabindex="11">Batal</a>
				<input type="submit" class="btn btn-primary" value="Simpan" tabindex="10">
			</div>
		{!! Form::close() !!}
	@overwrite	
@else
	@section('widget_title')
	@overwrite

	@section('widget_body')
	@overwrite
@endif