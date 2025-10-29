<?php
/**
 * Tickety - Twig Version
 * Entry point for the application
 */

// Suppress error display - log instead
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Start session
session_start();

// Require composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\BodyParsingMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Create app
$app = AppFactory::create();

// Add body parsing middleware
$app->add(new BodyParsingMiddleware());

// Create Twig instance
$twig = Twig::create(__DIR__ . '/../templates', [
    'cache' => false
]);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $twig));

// Simple auth check middleware
$requireAuth = function (Request $request, Response $response, $next) {
    if (!isset($_SESSION['user'])) {
        return $response->withStatus(302)->withHeader('Location', '/sign-in');
    }
    return $next($request, $response);
};

// ============ Public Routes ============

$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/home.html.twig');
});

$app->get('/sign-in', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/sign-in.html.twig', [
        'error' => $_SESSION['error'] ?? null,
        'title' => 'Welcome Back',
        'description' => 'Glad to see you again. Log in to your account.',
        'image' => '/images/icons/profile.svg'
    ]);
});

$app->get('/create-account', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/create-account.html.twig', [
        'error' => $_SESSION['error'] ?? null,
        'title' => 'Create New Account',
        'description' => 'Enter your details to sign up',
        'image' => '/images/icons/profile.svg'
    ]);
});

$app->post('/api/auth/sign-in', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Call the remote Vercel backend
        $ch = curl_init('https://tickety-mauve.vercel.app/api/auth/sign-in');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'password' => $password]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result) {
            $data = json_decode($result, true);
            if ($httpCode === 200 && isset($data['user'])) {
                $_SESSION['user'] = $data['user'];
                $_SESSION['token'] = $data['token'] ?? null;
            }
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($httpCode)
                            ->write($result);
        }

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500)
                        ->write(json_encode(['success' => false, 'message' => 'Backend unavailable']));
    } catch (Exception $e) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500)
                        ->write(json_encode(['success' => false, 'message' => 'Server error']));
    }
});

$app->post('/api/auth/sign-up', function (Request $request, Response $response) {
    try {
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Call the remote Vercel backend
        $ch = curl_init('https://tickety-mauve.vercel.app/api/auth/sign-up');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['name' => $name, 'email' => $email, 'password' => $password]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result) {
            $data = json_decode($result, true);
            if ($httpCode === 200 && isset($data['user'])) {
                $_SESSION['user'] = $data['user'];
                $_SESSION['token'] = $data['token'] ?? null;
            }
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($httpCode)
                            ->write($result);
        }

        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500)
                        ->write(json_encode(['success' => false, 'message' => 'Backend unavailable']));
    } catch (Exception $e) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(500)
                        ->write(json_encode(['success' => false, 'message' => 'Server error']));
    }
});

$app->get('/api/auth/logout', function (Request $request, Response $response) {
    unset($_SESSION['user']);
    return $response->withHeader('Location', '/sign-in')->withStatus(302);
});

// ============ Protected Routes ============

$app->get('/dashboard', function (Request $request, Response $response) {
    // Set test user if not authenticated
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'role' => 'Super Admin'
        ];
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/dashboard.html.twig', [
        'user' => $_SESSION['user'] ?? null,
        'pageTitle' => 'Dashboard'
    ]);
});

$app->get('/ticket', function (Request $request, Response $response) {
    // Set test user if not authenticated
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'role' => 'Super Admin'
        ];
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/tickets.html.twig', [
        'user' => $_SESSION['user'] ?? null,
        'pageTitle' => 'Tickets'
    ]);
});

$app->get('/tickets', function (Request $request, Response $response) {
    // Set test user if not authenticated
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'role' => 'Super Admin'
        ];
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/tickets.html.twig', [
        'user' => $_SESSION['user'] ?? null,
        'pageTitle' => 'Tickets'
    ]);
});

$app->get('/tickets/sales', function (Request $request, Response $response) {
    // Set test user if not authenticated
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [
            'name' => 'Robert Johnson',
            'email' => 'robert@example.com',
            'role' => 'Super Admin'
        ];
    }

    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/tickets-sales.html.twig', [
        'user' => $_SESSION['user'] ?? null,
        'pageTitle' => 'Sales Tickets'
    ]);
});

$app->get('/forgot-password', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pages/forgot-password.html.twig');
});

// Run the app
$app->run();
