@section('nav_topbar')
	@include('widgets.common.nav_topbar', 
	['breadcrumb' => [
						['name' => $data['name'], 'route' => route('hr.organisations.show', [$data['id'], 'org_id' => $data['id']]) ], 
						['name' => 'Data Karyawan', 'route' => route('hr.persons.index', ['org_id' => $data['id']]) ],
						['name' => $person['name'], 'route' => route('hr.persons.show', ['id' => $person['id'], 'person_id' => $person['id'],'org_id' => $data['id'] ])], 
						['name' => 'Data Kerabat', 'route' => route('hr.person.relatives.index', ['id' => $person['id'], 'person_id' => $person['id'],'org_id' => $data['id'] ])], 
						['name' => (is_null($id) ? 'Tambah' : 'Ubah '), 'route' => (is_null($id) ? route('hr.person.relatives.create', ['org_id' => $data['id'], 'person_id' => $person['id']]) : route('hr.person.relatives.edit', ['org_id' => $data['id'], 'person_id' => $person['id'], 'id' => $id]) )]
					]
	])
@stop

@section('nav_sidebar')
	@include('widgets.common.nav_sidebar', [
		'widget_template'		=> 'plain_no_title',
		'widget_title'			=> 'Structure',		
		'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
		'widget_body_class'		=> '',
		'widget_options'		=> 	[
										'sidebar'						=> 
										[
											'search'					=> [],
											'sort'						=> [],
											'page'						=> 1,
											'per_page'					=> 100,
											'active_relative_person'	=> 'yes'
										]
									]
	])
@overwrite

@section('content_filter')
@overwrite

@section('content_body')
@if(Input::has('employee'))
	@if(Input::get('employee'))
		@include('widgets.organisation.person.relative.employee.form', [
			'widget_template'	=> 'panel',
			'widget_options'	=> 	[
										'relativelist'			=>
										[
											'form_url'			=> route('hr.person.relatives.store', ['id' => $id, 'person_id' => $person['id'], 'org_id' => $data['id']]),
											'organisation_id'	=> $data['id'],
											'search'			=> ['id' => $id, 'personid' => $person['id'], 'relativeorganisationid' => $data['id']],
											'sort'				=> [],
											'new'				=> (is_null($id) ? true : false),
											'page'				=> 1,
											'per_page'			=> 1,
											'route_back'	 	=> route('hr.person.relatives.index', [$person['id'], 'org_id' => $data['id'], 'person_id' => $person['id']])
										]
									]
		])
	@else
		@include('widgets.organisation.person.relative.form', [
			'widget_template'	=> 'panel',
			'widget_options'	=> 	[
										'relativelist'			=>
										[
											'form_url'			=> route('hr.person.relatives.store', ['id' => $id, 'person_id' => $person['id'], 'org_id' => $data['id']]),
											'organisation_id'	=> $data['id'],
											'search'			=> ['id' => $id, 'personid' => $person['id'], 'relativeorganisationid' => $data['id']],
											'new'				=> (is_null($id) ? true : false),
											'sort'				=> [],
											'page'				=> 1,
											'per_page'			=> 1,
											'route_back'	 	=> route('hr.person.relatives.index', [$person['id'], 'org_id' => $data['id'], 'person_id' => $person['id']])
										]
									]
		])
	@endif
@else
	<div class="clearfix">&nbsp;</div>
	<div class="clearfix">&nbsp;</div>
	<div class="clearfix">&nbsp;</div>
	<div class="clearfix">&nbsp;</div>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			@include('widgets.common.selectblock', [
				'widget_title'			=> 'Pilih Data',
				'widget_template'		=> 'panel',
				'widget_options'		=> 	[
												'url_old'		=> route('hr.person.relatives.create', ['org_id' => $data['id'], 'person_id' => $person['id'], 'employee' => true]),
												'url_new'		=> route('hr.person.relatives.create', ['org_id' => $data['id'], 'person_id' => $person['id'], 'employee' => false]),
												'caption_old'	=> '  Data Lama  ',
												'caption_new'	=> '  Data Baru  ',
											],
				])
		</div>
	</div>
@endif
@overwrite

@section('content_footer')
@overwrite