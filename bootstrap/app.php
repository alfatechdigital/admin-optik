<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    
        // Tambahkan ini!
        $middleware->trustHosts(at: ['127.0.0.1']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', '_token']))
                ->with('error', 'Sesi Anda telah berakhir (CSRF Token mismatch). Silakan coba lagi.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) {
            $code = $e->getStatusCode();
            $friendlyCodes = [400, 401, 402, 405, 406, 409, 410, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 423, 424, 425, 426];
            
            if (in_array($code, $friendlyCodes)) {
                return response()->view('errors.generic', ['code' => $code], $code);
            }
        });
    })->create();
