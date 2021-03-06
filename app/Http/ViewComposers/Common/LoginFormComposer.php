<?php namespace App\Http\ViewComposers\Common;

use Illuminate\Contracts\View\View;
use Illuminate\Support\MessageBag;
use App\Http\ViewComposers\WidgetComposer;
use Input, Validator, App;

class LoginFormComposer extends WidgetComposer {

	protected function setRules($options)
	{
		$widget_rules['form_url']			= ['required', 'url'];				// url for form submit
		$widget_rules['user_id'] 			= ['required', 'alpha_dash'];		// user_id: username/email/etc for user identification
		$widget_rules['user_id_label'] 	= ['required'];						// user_id: label for user_id

		return $widget_rules;
	}

	protected function setData($options)
	{
		
	}
}