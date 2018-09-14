<?php
//packagist illuminate/database, zendframework/zend-diactoros, twig/twig, eloquent, aura/router

//reto: complementar admin.twig para que las rutas estén protegidas cambiar mensaje de protected
//por una respuesta de redireccionamiento a login

ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

//inicializa la sesión
session_start();

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

//accediendo al namespace del framework de eloquent
use Illuminate\Database\Capsule\Manager as Capsule;
//accediendo al namespace del framework aura/router
use Aura\Router\RouterContainer;
use Zend\Diactoros\Response\RedirectResponse;

//objeto capsule para acceder a sus metodos
$capsule = new Capsule;

//accediendo y pasando variables al metodo addconnection para conectarse a la bd
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => getenv('DB_HOST'),
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

//creando un objeto request de ServerRequestInterface con las variables superglobales 
//para el uso de ellas
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
//creando un contenedor de rutas
$routerContainer = new RouterContainer();

//accediendo para la obtencion de un mapa del contenedor de rutas
$map = $routerContainer->getMap();

//creando mapas de las rutas que se desean
$map->get('php', '/php/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'IndexAction'
    ]);
$map->get('addJobs', '/php/jobs/add', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
    ]);
$map->get('addProjects', '/php/projects/add', [
    'controller' => 'App\Controllers\ProjectsController',
    'action' => 'getAddProjectsAction',
    'auth' => true
    ]);

$map->get('addUsers', '/php/users/add', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getAddUserAction',
    'auth' => true
]);

$map->get('loginForm', '/php/login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLoginAction'
]);

$map->get('admin', '/php/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndexAction',
    'auth' => true
]);

$map->get('logout', '/php/logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogoutAction'
]);

$map->post('saveJobs', '/php/jobs/add', [
        'controller' => 'App\Controllers\JobsController',
        'action' => 'getAddJobAction'
        ]);

$map->post('saveProjects', '/php/projects/add', [
        'controller' => 'App\Controllers\ProjectsController',
        'action' => 'getAddProjectsAction'
        ]);

$map->post('auth', '/php/auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLoginAction'
]);

$map->post('saveUsers', '/php/users/add', [
    'controller' => 'App\Controllers\UserController',
    'action' => 'getAddUserAction', 
]);

//obteniendo el objeto creado en el contenedor de rutas con todas las rutas y los handlers
$matcher = $routerContainer->getMatcher();

//pasando el objeto request al metodo match
$route = $matcher->match($request);

//si la ruta no existe, se despliega el mensaje, y si existe, accederá al archivo que 
//pasamos como parametro handler
if (!$route) {
    echo 'No route';
}else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $needsAuth = $handlerData['auth'] ?? false;

    $sessionUserId = $_SESSION['userID'] ?? null;

    if ($needsAuth && !$sessionUserId) {
        header("Location: /php/login");
        die();
    }
    
    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    http_response_code($response->getStatusCode());
    echo $response->getBody();
}