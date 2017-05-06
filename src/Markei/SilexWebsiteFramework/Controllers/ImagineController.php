<?php
namespace Markei\SilexWebsiteFramework\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author maartendekeizer
 * @copyright Markei.nl
 * @license MIT
 */
class ImagineController extends BaseController
{
    public function resizeImageAction(Request $request, $path, $size, $checksum)
    {
        $webPath = $path;
        $path = DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        $fullPath = $this->app['imagine.source_path'] . $path;
        $targetPath = $this->app['imagine.thumbnail_path'] . DIRECTORY_SEPARATOR . $size . DIRECTORY_SEPARATOR . $checksum . DIRECTORY_SEPARATOR . $path;
        $targetDir = substr($targetPath, 0, strrpos($targetPath, DIRECTORY_SEPARATOR));

        if (file_exists($targetDir) === false)
            mkdir($targetDir, 0775, true);

        $expectedChecksum = substr(sha1(str_replace([' '], ['%20'], $webPath) . $size . $this->app['imagine.secret']), 0, 10);
        if ($expectedChecksum !== $checksum)
            throw new NotFoundHttpException('Invalid checksum');

        $sizeArray = explode('x', $size);
        if (count($sizeArray) !== 2)
            throw new HttpException(500, 'Size parameter invalid');

        $imagine = new \Imagine\Gd\Imagine();
        $box = new \Imagine\Image\Box($sizeArray[0], $sizeArray[1]);

        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;

        $imagine
            ->open($fullPath)
            ->thumbnail($box, $mode)
            ->save($targetPath, ['jpeg_quality' => 100]);

        return new RedirectResponse($this->app['imagine.thumbnail_url'] . $size . '/' . $checksum . '/' . $webPath, RedirectResponse::HTTP_TEMPORARY_REDIRECT);
    }
}