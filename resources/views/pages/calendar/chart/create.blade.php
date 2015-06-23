@section('nav_topbar')
	@include('widgets.common.nav_topbar', 
	['breadcrumb' => [
						['name' => $data['name'], 'route' => route('hr.organisations.show', [$data['id'], 'org_id' => $data['id']]) ], 
						['name' => $calendar['name'], 'route' => route('hr.calendars.show', ['id' => $calendar['id'], 'cal_id' => $calendar['id'],'org_id' => $data['id'] ])], 
						['name' => 'Jabatan', 'route' => route('hr.calendar.charts.index', ['id' => $id, 'cal_id' => $calendar['id'], 'org_id' => $data['id'] ])], 
						['name' => (is_null($id) ? 'Tambah' : 'Ubah '), 'route' => (is_null($id) ? route('hr.calendar.charts.create', ['org_id' => $data['id'], 'cal_id' => $calendar['id']]) : route('hr.calendar.charts.edit', ['org_id' => $data['id'], 'cal_id' => $calendar['id'], 'id' => $id]) )]
					]
	])
@stop

@section('nav_sidebar')
	@include('widgets.common.nav_sidebar', [
		'widget_template'		=> 'plain',
		'widget_title'			=> 'Structure',		
		'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
		'widget_body_class'		=> '',
		'widget_options'		=> 	[
										'sidebar'				=> 
										[
											'search'			=> [],
											'sort'				=> [],
											'page'				=> 1,
											'per_page'			=> 100,
										]
									]
	])
@overwrite

@section('content_filter')
@overwrite

@section('content_body')	
	@include('widgets.calendar.chart.form', [
		'widget_template'	=> 'panel',
		'widget_options'	=> 	[
									'followlist'			=>
									[
										'form_url'			=> route('hr.calendar.charts.store', ['id' => $id, 'cal_id' => $calendar['id'], 'org_id' => $data['id']]),
										'organisation_id'	=> $data['id'],
										'search'			=> ['id' => $id ,'withattributes' => ['calendar']],
										'sort'				=> [],
										'page'				=> 1,
										'per_page'			=> 1,
										'route_back'	 	=> route('hr.calendar.charts.index', [$calendar['id'], 'org_id' => $data['id'], 'cal_id' => $calendar['id']])
									]
								]
	])

@overwrite

@section('content_footer')
@overwrite