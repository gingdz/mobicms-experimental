<?php
/*
 * mobiCMS Content Management System (http://mobicms.net)
 *
 * For copyright and license information, please see the LICENSE.md
 * Installing the system or redistributions of files must retain the above copyright notice.
 *
 * @link        http://mobicms.net mobiCMS Project
 * @copyright   Copyright (C) mobiCMS Community
 * @license     LICENSE.md (see attached file)
 */

namespace Mobicms\Routing;

use Mobicms\Api\RouterInterface;

/**
 * Class Router
 *
 * @package Mobicms\Routing
 * @author  Oleg (AlkatraZ) Kasyanov <dev@mobicms.net>
 * @version v.2.0.0 2015-08-07
 */
class Router implements RouterInterface
{
    private $routes;

    private $path = [];
    private $pathQuery = [];
    private $module = null;
    private $protectedPath = [
        'assets',
        'classes',
        'includes',
        'locale',
        'templates',
    ];

    public $dir = '';

    public function __construct(array $routes)
    {
        $this->routes = $routes;
        $uri = trim(urldecode(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL)), '/');
        $uri = substr($uri, strlen(trim(dirname(filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING)), DIRECTORY_SEPARATOR)), 400);

        if ($pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $this->path = array_filter(explode('/', trim($uri, '/')));
        $this->module = $this->getModule();
        $this->dir = $routes[$this->module];
    }

    public function dispatch()
    {
        $file = 'index.php';
        $dir = MODULE_PATH . $this->dir . DS;
        $path = array_slice($this->path, 1);
        $i = 0;

        foreach ($path as $val) {
            if (in_array($val, $this->protectedPath)) {
                break;
            }

            if (is_dir($dir . $val)) {
                // Если существует директория
                $dir .= $val . DS;
            } else {
                if (pathinfo($val, PATHINFO_EXTENSION) == 'php' && is_file($dir . $val)) {
                    // Если вызван PHP файл
                    $file = $val;
                    ++$i;
                }

                break;
            }

            ++$i;
        }

        // Разделяем URI на Path и Query
        $this->path = array_slice($this->path, 0, $i + 1);
        $this->pathQuery = array_slice($path, $i);

        $this->includeFile($dir . $file);
    }

    /**
     * @return string
     */
    public function getCurrentModule()
    {
        return $this->module;
    }

    /**
     * @param null|string $key
     * @return array|bool
     */
    public function getQuery($key = null)
    {
        if ($key === null) {
            return $this->pathQuery;
        } else {
            return isset($this->pathQuery[$key]) ? $this->pathQuery[$key] : false;
        }
    }

    /**
     * @return string
     */
    private function getModule()
    {
        $module = !empty($this->path) ? $this->path[0] : 'home';

        if (!isset($this->routes[$module]) || !$this->checkModule($module)) {
            $module = '404';
        }

        return $module;
    }

    /**
     * @param string $module
     * @return bool
     */
    private function checkModule($module)
    {
        if (is_dir(MODULE_PATH . $this->routes[$module])
            && is_file(MODULE_PATH . $this->routes[$module] . DS . 'index.php')
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $file
     */
    private function includeFile($file)
    {
        include_once $file;
    }
}
