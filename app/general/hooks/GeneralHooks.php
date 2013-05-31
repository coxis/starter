<?php
namespace Coxis\App\General\Hooks;

class GeneralHooks extends \Coxis\Hook\HooksContainer {
	/**
	@Hook('controller_configure')
	*/
	public function pagelayout($controller) {
		HTML::setTitle('Coxis');
		$controller->layout = array('\Coxis\App\General\Controllers\DefaultController', 'layout');

		$controller->addFilter(new \Coxis\Core\Filters\PageLayout);
		$controller->addFilter(new \Coxis\Core\Filters\JSONModels);
	}

	/**
	@Hook('exception_Coxis\Core\Exceptions\NotFoundException')
	*/
	public function hook404Exception($exception) {
		$request = \Request::inst();
		return \Coxis\Core\Controller::run('default', '_404', \Request::inst())->setCode(404);
	}

	/**
	@Hook('output')
	@Priority(1000)
	*/
	public function gzip() {
		if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']) || !strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
			return;
		if(!\Response::getContent())
			return;

		$r = \Response::getContent();
		$output = gzencode($r);
		\Response::setContent($output);
		\Response::setHeader('Content-Encoding', 'gzip');
		\Response::setHeader('Content-Length', strlen($output));
		\Response::setHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		\Response::setHeader('Pragma', 'no-cache');
	}
}