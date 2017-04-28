<?php
namespace Markei\SilexWebsiteFramework\Controllers;

use Markei\SilexWebsiteFramework\Application;

/**
 * @author maartendekeizer
 * @copyright Markei.nl
 * @license MIT
 */
abstract class BaseController
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->twig = $app['twig'];
    }
}