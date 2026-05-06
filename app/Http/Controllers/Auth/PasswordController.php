<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Contraseña actualizada exitosamente']);
            }

            return back()->with('status', 'password-updated');
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
    }
}
