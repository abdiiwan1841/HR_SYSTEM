@section('nav_topbar')
	@include('widgets.common.nav_topbar', ['breadcrumb' => [['name' => $data['name'], 'route' => route('hr.organisations.show', [$data['id'], 'org_id' => $data['id']]) ], ['name' => 'Template Dokumen', 'route' => route('hr.documents.index', ['org_id' => $data['id']]) ]]])
@stop

@section('nav_sidebar')
	@include('widgets.common.nav_sidebar', [
		'widget_template'		=> 'plain',
		'widget_title'			=> 'Structure',		
		'widget_title_class'	=> 'text-uppercase ml-10 mt-20',
		'widget_body_class'		=> '',
		'widget_options'		=> [
										'organisation_id'	=> 1,
										'identifier'		=> 1,
										'search'			=> [],
										'sort'				=> [],
										'page'				=> 1,
										'per_page'			=> 12,
									]
	])
@overwrite

@section('content_filter')
@overwrite

@section('content_body')	

	@include('widgets.document.form', [
		'widget_template'	=> 'panel',
		'widget_options'	=> 	[
									'form_url'			=> route('hr.documents.store', ['id' => $id, 'org_id' => $data['id']]),
									'identifier'		=> 1,
									'organisation_id'	=> $data['id'],
									'search'			=> ['id' => $id],
									'sort'				=> [],
									'page'				=> 1,
									'per_page'			=> 1,
								]
	])

@overwrite

@section('content_footer')
@overwrite