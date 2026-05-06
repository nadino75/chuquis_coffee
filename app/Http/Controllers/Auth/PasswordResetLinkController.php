<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $request->validate(['email' => ['required', 'email']]);

            $status = Password::sendResetLink($request->only('email'));

            if ($request->expectsJson() || $request->is('api/*')) {
                return $status == Password::RESET_LINK_SENT
                    ? response()->json(['message' => __($status)])
                    : response()->json(['message' => __($status), 'errors' => ['email' => [__($status)]]], 422);
            }

            return $status == Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
    }
}
