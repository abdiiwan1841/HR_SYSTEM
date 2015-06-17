@extends('widget_templates.'.($widget_template ? $widget_template : 'plain'))

@section('widget_title')
<h1> Kalender </h1>
@overwrite

@section('widget_body')
	<div class="clearfix">&nbsp;</div>
	{!! Form::open(['url' => $CalendarComposer['widget_data']['calendarlist']['form_url'], 'class' => 'form-horizontal']) !!}	
		<div class="form-group">
			<div class="col-md-2">
				<label class="control-label">Nama</label>
			</div>	
			<div class="col-md-10">
				{!!Form::input('text', 'name', $CalendarComposer['widget_data']['calendarlist']['calendar']['name'], ['class' => 'form-control'])!!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-2">
				<label class="control-label">Workdays</label>
			</div>	
			<div class="col-md-10">
				{!!Form::input('text', 'workdays', $CalendarComposer['widget_data']['calendarlist']['calendar']['workdays'], ['class' => 'form-control'])!!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-2">
				<label class="control-label">Start</label>
			</div>	
			<div class="col-md-4">
				{!!Form::input('text', 'start', $CalendarComposer['widget_data']['calendarlist']['calendar']['start'], ['class' => 'form-control'])!!}
			</div>
			<div class="col-md-1 col-md-offset-1">
				<label class="control-label">End</label>
			</div>	
			<div class="col-md-4">
				{!!Form::input('text', 'end', $CalendarComposer['widget_data']['calendarlist']['calendar']['end'], ['class' => 'form-control'])!!}
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12 text-right">
				<a href="{{ $CalendarComposer['widget_data']['calendarlist']['route_back'] }}" class="btn btn-default mr-5">Batal</a>
				<input type="submit" class="btn btn-primary" value="Simpan">
			</div>
		</div>
	{!! Form::close() !!}
@overwrite	