<?php

// api/index.php

// Load Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load Dotenv (untuk memastikan environment variables dimuat dari .env atau Vercel)
// Ini penting agar variabel lingkungan dari Vercel terpakai
if (file_exists(__DIR__ . '/../.env')) {
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();
}

// Create the Laravel application instance
// Kita akan mem-boot aplikasi dengan cara yang sama seperti public/index.php
// tetapi kita akan memastikan bahwa permintaan API ditangani dengan benar.
$app = \Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        // Ini adalah bagian penting. Kita akan menggunakan middleware yang sama
        // seperti di bootstrap/app.php, tetapi kita pastikan tidak ada
        // konflik dengan API.
        $middleware->web(append: [
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, // Ini untuk web
            \Illuminate\Session\Middleware\StartSession::class, // Ini untuk web
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Ini untuk web
        ]);

        // Pastikan middleware group 'api' sudah diatur dengan benar
        // Jika Anda menggunakan Laravel Sanctum, tambahkan EnsureFrontendRequestsAreStateful::class
        // di sini jika aplikasi Anda adalah SPA/frontend yang di-host di domain yang sama
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Jika Anda menggunakan Sanctum untuk API, tambahkan ini:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Middleware alias dari bootstrap/app.php juga perlu diulang di sini
        // agar berfungsi di lingkungan serverless ini.
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'checkRole' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        //
    })->create();

// Handle the incoming request
$request = Illuminate\Http\Request::capture();

// Resolve the HTTP kernel for handling the request
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Send the response
$response = $kernel->handle($request);
$response->send();

// Terminate the kernel
$kernel->terminate($request, $response);