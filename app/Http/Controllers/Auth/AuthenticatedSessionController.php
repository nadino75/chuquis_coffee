<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['user' => Auth::user()->load(['roles:id,name', 'roles.permissions:id,name'])]);
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Las credenciales no coinciden con nuestros registros.',
                    'errors' => ['email' => ['Las credenciales no son válidas.']]
                ], 422);
            }
            throw $e;
        }
    }

    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        }

        return redirect('/');
    }
}
