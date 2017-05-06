<?php
namespace Markei\SilexWebsiteFramework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Markei\SilexWebsiteFramework\Controllers\PageController;
use Markei\SilexWebsiteFramework\Controllers\ImagineController;
use Markei\SilexWebsiteFramework\Controllers\ErrorController;
use Markei\SilexWebsiteFramework\Twig\ImagineExtension;
use Markei\SilexWebsiteFramework\Twig\ConfigExtension;
use Silex\Application;

/**
 * @author maartendekeizer
 * @copyright Markei.nl
 * @license MIT
 */
class Application extends \Silex\Application
{
    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->validateConfiguration($values);
        parent::__construct($values);
    }

    /**
     * Validates the configuration
     * Checks if all the fields are present
     * @param array $config
     */
    protected function validateConfiguration($config)
    {
        $requiredFields = $this->getRequiredConfigurationFields();
        $missingFields = [];
        foreach ($requiredFields as $requiredField) {
            if (array_key_exists($requiredField, $config) === false) {
                $missingFields[] = $requiredField;
            }
        }
        if (count($missingFields) > 0) {
            throw new \RuntimeException('Some fields are not configured: ' . implode(', ', $missingFields));
        }
    }

    /**
     * Get a list of required fields in the configuration
     * @return string[]
     */
    protected function getRequiredConfigurationFields()
    {
        return [
                'debug',
                'app.path',
                'app.cache',
                'imagine.source_path',
                'imagine.source_url',
                'imagine.thumbnail_path',
                'imagine.thumbnail_url',
                'imagine.secret',
                'smtp.host',
                'smtp.port',
                'smtp.username',
                'smtp.password',
                'smtp.encryption',
                'smtp.auth_mode',
                'twig.path',
                'twig.form.templates',
                'twig.options',
                'form.secret',
                'translation.locale'
            ];
    }

    /**
     * (non-PHPdoc)
     * @see \Silex\Application::boot()
     */
    public function boot()
    {
        parent::boot();
        $this->bootTwig();
        $this->bootForm();
        $this->bootTranslation();
        $this->bootSwiftmailer();
        $this->registerControllers();
        $this->registerRoutes();
    }

    /**
     * Boots the Twig Service Provider
     */
    protected function bootTwig()
    {
        $options = array(
            'twig.path' => is_array($this['twig.path']) ? $this['twig.path'] : [$this['twig.path']],
            'twig.form.templates' => $this['twig.form.templates'],
            'twig.options' => $this['twig.options']
        );
        $options['twig.path'][] = __DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views';
        if ($this['debug'] === false) {
            $options['twig.options']['cache'] = $this['app.cache'] . DIRECTORY_SEPARATOR . 'twig';
        }
        $this->register(new TwigServiceProvider(), $options);
        $this->extend('twig', function (\Twig_Environment $twig, Application $app) {
            $twig->addExtension(new ImagineExtension($app));
            $twig->addExtension(new ConfigExtension($app));
            return $twig;
        });
    }

    /**
     * Boots the Form Service Provider and Validator Service Provider
     */
    protected function bootForm()
    {
        $this->register(new FormServiceProvider(), array('form.secret' => $this['form.secret']));
        $this->register(new ValidatorServiceProvider(), array());
    }

    /**
     * Boots the Translation Service Provider
     */
    protected function bootTranslation()
    {
        $this->register(new TranslationServiceProvider(), array('locale' => $this['translation.locale']));
    }

    /**
     * Boots the Swiftmailer Service Provider
     */
    protected function bootSwiftmailer()
    {
        $this->register(new \Silex\Provider\SwiftmailerServiceProvider(), array(
                'swiftmailer.use_spool' => false,
                'swiftmailer.options' => array(
                        'host' => $this['smtp.host'],
                        'port' => $this['smtp.port'],
                        'username' => $this['smtp.username'],
                        'password' => $this['smtp.password'],
                        'encryption' => $this['smtp.encryption'],
                        'auth_mode' => $this['smtp.auth_mode']
                    )
            ));
    }

    /**
     * Register the controllers in the container
     */
    protected function registerControllers()
    {
        $this['controllers.page'] = function ($app) { return new PageController($app); };
        $this['controllers.imagine'] = function ($app) { return new ImagineController($app); };
        $this['controllers.error'] = function ($app) { return new ErrorController($app); };
    }

    /**
     * Register the routes and link them to the controller actions
     */
    protected function registerRoutes()
    {
        $this->get($this['imagine.thumbnail_url'] . '{size}/{checksum}/{path}', function (Application $app, Request $request, $path, $size, $checksum) { return $app['controllers.imagine']->resizeImageAction($request, $path, $size, $checksum); })->assert('path', '.+');
        $this->get('', function (Application $app, Request $request) { return $app['controllers.page']->showHomePageAction($request); });
        $this->error(function (NotFoundHttpException $e, $code) { return $this['controllers.error']->notFoundAction($e); });
        $this->error(function (\Exception $e, $code) { return $this['controllers.error']->internalServerErrorAction($e); });
    }
}