<?php

/**
 * Initialisiert Silex und definiert die Controller
 *
 * @author Markus Tacker <m@coderbyheart.de>
 * @package ReText\Www;
 */

namespace ReText\Www;

use Symfony\Component\HttpFoundation\Request,
Symfony\Component\HttpFoundation\Response,
Symfony\Component\HttpKernel\HttpKernelInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
));

$app->get('/', function() use($app)
{
    $subRequest = Request::create('/index', 'GET');
    return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
});

$app->get('/{page}', function($page) use($app)
{
    if (!in_array($page, array('index', 'contact', 'privacy', 'features', 'pricing'))) $app->abort(404, "Unknown page $page");
    return $app['twig']->render(sprintf('%s.html.twig', $page));
});

$app->run();