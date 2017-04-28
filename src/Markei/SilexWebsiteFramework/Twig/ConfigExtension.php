<?php
namespace Markei\SilexWebsiteFramework\Twig;

use Markei\SilexWebsiteFramework\Application;

class ConfigExtension extends \Twig_Extension
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return __CLASS__;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getGlobals()
     */
    public function getFunctions()
    {
        return array(
            new \Twig_Function('get_config', [$this, 'getConfig'])
        );
    }

    public function getConfig($parameter)
    {
        return isset($this->app[$parameter]) ? $this->app[$parameter] : null;
    }
}