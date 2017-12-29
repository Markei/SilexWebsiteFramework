<?php
namespace Markei\SilexWebsiteFramework\Controllers;

/**
 * @author maartendekeizer
 * @copyright Markei.nl
 * @license MIT
 */
class ErrorController extends BaseController
{
    /**
     * @param \Exception $e
     * @return string
     */
    public function notFoundAction(\Exception $e)
    {
        $parameters = array(
                'message' => $e->getMessage(),
                'debug' => $this->app['debug']
            );

        return $this->twig->render('Error/notFound.html.twig', $parameters);
    }

    /**
     * @param \Exception $e
     * @return string
     */
    public function internalServerErrorAction(\Exception $e)
    {
        $parameters = array(
                'message' => $e->getMessage(),
                'debug' => $this->app['debug'],
                'trace' => $e->getTraceAsString()
            );

        return $this->twig->render('Error/internalServerError.html.twig', $parameters);
    }
}