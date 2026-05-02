<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoteAuth
{

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        set_time_limit(120);
        
        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        // Seguridad: Hacemos la petición HTTP al servicio de Auth
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)
                ->acceptJson()
                ->timeout(10) 
                ->connectTimeout(5)
                ->get('http://localhost:8000/api/user-verify');

            if ($response->successful() && $response->json('valid')) {
                $request->merge(['authenticated_user' => $response->json('user')]);
                return $next($request);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Servicio de autenticación no disponible'], 503);
        }

        return response()->json(['error' => 'Token inválido o expirado'], 401);
    }

}
