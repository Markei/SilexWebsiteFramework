# Quick start

## Project layout

    /config
        /app.dev.php
    /src
        /YourNamespace
            /ProjectNamespace
                /Controllers
                /Resources
                    /views
                /Application.php
    /temp
    /vendor
    /wwwroot
        /media
            /src
            /cache
        /assets
            /css
            /img
            /js
            /vendor
        /index.php
        /.htaccess
        /iirf.ini
        /offline.php.disabled
        /robots.txt
    /composer.json
    /composer.lock
    /env.php

## Display a Twig file

Create a Twig file in `/src/YourNamespace/ProjectNamespace/Resources/views/myPage.html.twig` and add the following code to `/src/YourNamespace/ProjectNamespace/Application.php`

    protected function registerRoutes()
    {
        $this->get('/my-page', function (Application $app, Request $request) { return $app['controllers.page']->showPageAction($request, 'myPage.html.twig'); });
        parent::registerRoutes();
    }
    
## Display a Twig file in a directory

Create a Twig file in `/src/YourNamespace/ProjectNamespace/Resources/views/products/firstProduct.html.twig` and add the following code to `/src/YourNamespace/ProjectNamespace/Application.php`

    protected function registerRoutes()
    {
        $this->get('/catalog/first-product/', function (Application $app, Request $request) { return $app['controllers.page']->showPageAction($request, 'firstProduct.html.twig', 'products'); });
        parent::registerRoutes();
    }
    
## Build your own controller

Create your first controller `/src/YourNamespace/ProjectNamespace/Controller/MyController.php`

    <?php
    namespace YourNamespace\ProjectNamespace\Controllers;
    
    use Markei\SilexWebsiteFramework\Controllers\BaseController;
    use Symfony\Component\HttpFoundation\Request;

    class MyController extends BaseController
    {
        public function myAction(Request $request, $param1)
        {
            return $this->twig->render('myAction.html.twig', [$param1]);
        }
    }

Create also a Twig template `/src/YourNamespace/ProjectNamespace/Resources/views/myAction.html.twig`

Register your new controller in `/src/YourNamespace/ProjectNamespace/Application.php`

    protected function registerControllers()
    {
        $this['controllers.mycontroller'] = function ($app) { return new Controller\MyController($app); };
        parent::registerControllers();
    }

Bind a route to your new controller in `/src/YourNamespace/ProjectNamespace/Application.php`

    protected function registerRoutes()
    {
        $this->get('/say/{param1}', function (Application $app, Request $request, $param1) { return $this['controllers.mycontroller']->myAction($request, $param1); });
        parent::registerRoutes();
    }

## Validate configuration

In `/src/YourNamespace/ProjectNamespace/Application.php`

    protected function getRequiredConfigurationFields()
    {
        return array_merge(parent::getRequiredConfigurationFields(), [
            'my-required-config-field1',
            'my-required-config-field-2'
        ]);
    }


## Thumbnails

Inside your Twig template

    <img src="{{ '/media/src/my-image.png'|thumbnail('40x30') }}" alt="">

## Override the default 404 or 500 error page

Create a new Twig file `/src/YourNamespace/ProjectNamespace/Resources/views/Error/notFound.html.twig` (for 404) or `/src/YourNamespace/ProjectNamespace/Resources/views/Error/internalServerError.html.twig` (for 500)

You can also override the complete controller with:

    protected function registerRoutes()
    {
        $this->error(function (NotFoundHttpException $e, $code) { return $this['controllers.my-error-controller']->notFoundAction($e); });
        $this->error(function (\Exception $e, $code) { return $this['controllers.my-error-controller']->internalServerErrorAction($e); });
        parent::registerRoutes();
    }
    
