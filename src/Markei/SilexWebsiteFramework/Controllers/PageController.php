<?php
namespace Markei\SilexWebsiteFramework\Controllers;

use Markei\SilexWebsiteFramework\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PageController extends BaseController
{
    /**
     * List of valid page characters
     * @var array
     */
    private $valid_chars = array();

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->valid_chars = array(
            'a' => true, 'b' => true, 'c' => true, 'd' => true, 'e' => true, 'f' => true, 'g' => true, 'h' => true, 'i' => true, 'j' => true, 'k' => true, 'l' => true, 'm' => true, 'n' => true, 'o' => true, 'p' => true, 'q' => true, 'r' => true, 's' => true, 't' => true, 'u' => true, 'v' => true, 'w' => true, 'x' => true, 'y' => true, 'z' => true,
            'A' => true, 'B' => true, 'C' => true, 'D' => true, 'E' => true, 'F' => true, 'G' => true, 'H' => true, 'I' => true, 'J' => true, 'K' => true, 'L' => true, 'M' => true, 'N' => true, 'O' => true, 'P' => true, 'Q' => true, 'R' => true, 'S' => true, 'T' => true, 'U' => true, 'V' => true, 'W' => true, 'X' => true, 'Y' => true, 'Z' => true,
            '0' => true, '1' => true, '2' => true, '3' => true, '4' => true, '5' => true, '6' => true, '7' => true, '8' => true, '9' => true,
            '-' => true, '_' => true
        );
    }

    public function showPageAction(Request $request, $page, $directory = null)
    {
        try {
            $template_name = ($directory !== null ? $this->sanitizeFileName($directory) . '/' : '') . $this->sanitizeFileName($page) . '.html.twig';
            $template = $this->twig->loadTemplate($template_name);
            return $template->render(array());
        }
        catch (\Twig_Error_Loader $e) {
            throw new NotFoundHttpException('File not found: ' . ($directory !== null ? $this->sanitizeFileName($directory) . '/' : '') . $this->sanitizeFileName($page) . '.html.twig');
        }
        catch (\Twig_Error_Syntax $e) {
            throw new HttpException(500, 'Twig syntax error: ' . $e->getMessage());
        }
    }

    public function showHomePageAction(Request $request)
    {
        return $this->twig->render('index.html.twig');
    }

    protected function sanitizeFileName($evil_file_name)
    {
        $evil_file_name = str_split($evil_file_name);
        $nice_file_name = array();
        foreach ($evil_file_name as $char)
        {
            if (isset($this->valid_chars[$char]) === true)
            {
                $nice_file_name[] = $char;
            }
        }
        return implode('', $nice_file_name);
    }
}