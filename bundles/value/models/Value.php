<?php
namespace Coxis\Bundles\Value\Models;

class Value extends \Coxis\Bundles\Value\SingleValue {
	public static $properties = array(
		'key',
		'value'    => array(
			'required'    =>    false,
		),
	);
}