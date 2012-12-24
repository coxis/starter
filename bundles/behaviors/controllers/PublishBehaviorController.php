<?php
namespace Coxis\Bundles\Behaviors\Controllers;

class PublishBehaviorController extends \Coxis\Core\Controller {
	/**
	@Hook('behaviors_load_publish')
	**/
	public function behaviors_load_publishAction($modelDefinition) {
		$modelDefinition->addProperty('published', array('type'=>'boolean', 'default'=>true));
		if(!\URL::startsWith('admin')) {
			$modelDefinition->hook('getorm', function($chain, $orm) {
				$orm->where(array('published'=>1));
			});
		}
	}
}