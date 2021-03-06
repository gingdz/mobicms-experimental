<?php

/**
 * Common constants
 */
defined('DEBUG') || define('DEBUG', false);
defined('MOBICMS') or die('Error: restricted access');
define('START_MEMORY', memory_get_usage());                    // Profiling memory usage
define('START_TIME', microtime(true));                         // Profiling generation time
const DS = DIRECTORY_SEPARATOR;                                // Directory Separator alias

/**
 * Pathes
 */

define('ROOT_PATH', dirname(__DIR__) . DS);                    // Defines the root directory
const CACHE_PATH = __DIR__ . DS . 'cache' . DS;                // Path to the system cache files
const LOG_PATH = __DIR__ . DS . 'logs' . DS;                   // Path to the LOG files
const LOCALE_PATH = __DIR__ . DS . 'locale' . DS;              // Path to the language files
const MODULE_PATH = ROOT_PATH . 'modules' . DS;                // Path to the modiles
const THEMES_PATH = ROOT_PATH . 'themes' . DS;                 // Path to the Templates
const FILES_PATH = ROOT_PATH . 'uploads' . DS;                 // Path to the Upload files
const ASSETS_PATH = ROOT_PATH . 'assets' . DS;                 // Path to the Upload files

// Define some PHP settings
mb_internal_encoding('UTF-8');
date_default_timezone_set('UTC');
ini_set('session.use_cookies', true);
ini_set('session.use_trans_sid', '0');
ini_set('session.use_only_cookies', true);
ini_set('session.gc_probability', '1');
ini_set('session.gc_divisor', '100');

// Autoloading classes
require __DIR__ . DS . 'vendor/autoload.php';

if (DEBUG) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    ini_set('error_log', LOG_PATH . 'errors-' . date('Y-m-d') . '.log');
    new Mobicms\Exception\Handler\Handler;
} else {
    ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'Off');
}

use Mobicms\Checkpoint\Facade;
use Mobicms\Environment\Vars;  //TODO: доработать, или удалить сервис
use Mobicms\HtmlFilter\Filter; //TODO: доработать, или удалить сервис
use Mobicms\HtmlFilter\Purify; //TODO: доработать, или удалить сервис
use Mobicms\i18n\Translate;
use Mobicms\Utility\Image;
use Mobicms\Ext\Session\PdoSessionHandler;
use Zend\Http\PhpEnvironment\Request;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\SessionManager;

use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

/**
 * Class App
 *
 * @method Image        image($file, array $arguments = [], $isModule = false, $imgTag = true)
 * @method Filter       filter($string) //TODO: доработать, или удалить сервис
 * @method Translate    lng()
 * @method Purify       purify($string) //TODO: доработать, или удалить сервис
 * @method              redirect($url) Closure function
 * @method Request      request()
 * @method              uri()
 * @method Facade       user()
 * @method Vars         vars() //TODO: удалить
 */
class App extends Mobicms\Ioc\Container
{
    private static $container;

    /**
     * @return ServiceManager
     */
    public static function getContainer()
    {
        if (null === self::$container) {
            $config = [];

            // Read configuration
            foreach (Glob::glob(__DIR__ . '/config/' . '{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
                $config = ArrayUtils::merge($config, include $file);
            }

            $container = new ServiceManager;
            (new Zend\ServiceManager\Config($config['dependencies']))->configureServiceManager($container);
            $container->setService('config', $config);
            self::$container = $container;
        }

        return self::$container;
    }
}

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

$app = App::getInstance();
$di = App::getDiInstance();

// Initialize the Request
$app->newInstance('request', Request::class);

/**
 * Shutdown handler
 */
register_shutdown_function(function () use ($container) {
    echo $container->get(Mobicms\Api\ViewInterface::class)->render();
});

// Starting the Session and register instance
$sessCfg = new Zend\Session\Config\StandardConfig;
$sessCfg->setOptions([
    'name'                => 'mobicms',
    'remember_me_seconds' => 1800,
    'use_cookies'         => true,
    'cookie_httponly'     => true,
]);
$sessManager = new SessionManager($sessCfg, new SessionArrayStorage, $di->newInstance(PdoSessionHandler::class));
$di->instanceManager()->addSharedInstance($sessManager, SessionManager::class);
$app->setService('session', new Zend\Session\Container('app', $sessManager));


// Initialize the User
$app->newInstance('user', Facade::class);

// Registering lazy loading services
$app->lazyLoad('image', Image::class, false);
$app->lazyLoad('filter', Filter::class, false); //TODO: доработать, или удалить сервис
$app->lazyLoad('lng', Translate::class);
$app->lazyLoad('purify', Purify::class);        //TODO: доработать, или удалить сервис
$app->lazyLoad('vars', Vars::class);            //TODO: удалить сервис

// Redirect to given URL
$app->setCallable('redirect', function ($url) use ($app) {
    ob_end_clean();
    http_response_code(302);
    header('Location: ' . $url[0]);
});

// Get Uri path
$app->setCallable('uri', function () use ($app) {
    return $app->request()->getUri()->getPath();
});

// i18n initialization
$i18n = $app->get('lng');

/**
 * Translate a message
 *
 * @param string $message
 * @param string $domain
 * @return string
 */
function _s($message, $domain = 'default')
{
    global $i18n;

    return $i18n->translateSystem($message, $domain);
}

/**
 * The plural version of _s()
 *
 * @param string $singular
 * @param string $plural
 * @param int    $count
 * @param string $domain
 * @return string
 */
function _sp($singular, $plural, $count, $domain = 'default')
{
    global $i18n;

    return $i18n->translateSystemPlural($singular, $plural, $count, $domain);
}

/**
 * Translate module
 *
 * @param string $message
 * @param string $domain
 * @return string
 */
function _m($message, $domain = 'default')
{
    global $i18n;

    return $i18n->translateModule($message, $domain);
}

/**
 * Plural version of _m()
 *
 * @param string      $singular
 * @param string      $plural
 * @param string      $count
 * @param null|string $domain
 * @return string
 */
function _mp($singular, $plural, $count, $domain = 'default')
{
    global $i18n;

    return $i18n->translateModulePlural($singular, $plural, $count, $domain);
}

// Output buffering
ob_start();
