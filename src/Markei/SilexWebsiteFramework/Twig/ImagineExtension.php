<?php
namespace Markei\SilexWebsiteFramework\Twig;

use Markei\SilexWebsiteFramework\Application;

class ImagineExtension extends \Twig_Extension
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

    public function getFilters()
    {
        return array(
            new \Twig_Filter('thumbnail', [$this, 'generateThumbnailPath'])
        );
    }

    public function generateThumbnailPath($mediaUrl, $size)
    {
        $base = $this->app['imagine.source_url'];
        if (substr($mediaUrl, 0, strlen($base)) !== $base) {
            exit('a');
            return $mediaUrl;
        }

        $webPath = substr($mediaUrl, strlen($base));

        $checksum = substr(sha1($webPath . $size . $this->app['imagine.secret']), 0, 10);

        return $this->app['imagine.thumbnail_url'] . $size . '/' . $checksum . '/' . $webPath;
    }
}