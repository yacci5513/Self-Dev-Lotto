<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 인증되지 않은 사용자를 로그인 페이지로 리다이렉트
        $middleware->redirectGuestsTo('/login');
        
        // Sanctum 미들웨어 설정
        $middleware->alias([
            'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // AuthenticationException 처리
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
            Log::debug('Unauthenticated access attempt from IP: ' . $request->ip());
            return redirect()->guest(route('login'));
        });

        // NotFoundHttpException 처리
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
            Log::debug('404 error from IP: ' . $request->ip());
            return redirect()->route('login');
        });

        // MethodNotAllowedHttpException 처리
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'message' => 'Method not allowed.'
            ], 405);
        });

        // ValidationException 처리
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        });

        // ModelNotFoundException 처리
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Model not found.'
                ], 404);
            }
            return redirect()->route('login');
        });

        // RouteNotFoundException 처리
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Route not found.'
                ], 404);
            }
            return redirect()->route('login');
        });

        // QueryException 처리
        $exceptions->render(function (QueryException $e, Request $request) {
            $response = [
                'message' => 'Database query error',
            ];
            
            if (!app()->environment('production')) {
                $response['details'] = $e->getMessage();
            }
            
            Log::error('Database error: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json($response, 500);
            }
            
            return back()->with('error', '서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
        });
    })->create();
