# SilexWebsiteFramework

The Markei.nl SilexWebsiteFramework configures all the standard vendors you need to build a site with Silex: Twig, Symfony Forms, Imagine and Swiftmailer

## Quick start

Create your own Application.php and start registering your routes.

	<?php
	namespace Acme\AcmeWebsite;

	use Markei\SilexWebsiteFramework\Application as BaseApplication;
	use Symfony\Component\HttpFoundation\Request;

	class Application extends BaseApplication
	{
	    protected function getRequiredConfigurationFields()
	    {
		return array_merge(parent::getRequiredConfigurationFields(), []);
	    }

	    protected function registerRoutes()
	    {
		$this->get('/first-page', function (Application $app, Request $request) { return $app['controllers.page']->showPageAction($request, 'first-page'); });
		$this->get('/second-page', function (Application $app, Request $request) { return $app['controllers.page']->showPageAction($request, 'second-page'); });
		parent::registerRoutes();
	    }

	    protected function registerControllers()
	    {
		parent::registerControllers();
	    }
	}