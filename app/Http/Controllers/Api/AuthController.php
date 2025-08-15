<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Resend\Laravel\Facades\Resend;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario y genera código 2FA
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => [
                    'required', 'string', 'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'confirmed'
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
                'code' => 422
            ], 422);
        }

        try {
            $user = DB::transaction(fn () => User::create([
                'name' => $validated['name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]));

            $emailSent = $this->generateTwoFactorCode($user);

            return response()->json([
                'message' => 'Usuario registrado.',
                '2fa_required' => true,
                'email_sent' => $emailSent,
                'code' => 201
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error inesperado al registrar usuario.',
                'code' => 500
            ], 500);
        }
    }

    /**
     * Verifica el código 2FA del usuario
     */
    public function verify2fa(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'two_factor_code' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
                'code' => 422
            ], 422);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
                'code' => 404
            ], 404);
        }

        if ((int)$validated['two_factor_code'] === (int)$user->two_factor_code && Carbon::parse($user->two_factor_expires_at)->isFuture()) {
            $user->update([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
                'email_verified_at' => now()
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => '2FA verificado correctamente.',
                'token' => $token,
                'user' => $user,
                'code' => 200
            ], 200);
        }

        return response()->json([
            'message' => 'Código incorrecto o expirado.',
            'code' => 401
        ], 401);
    }

    /**
     * Genera y envía el código 2FA al usuario
     */
    protected function generateTwoFactorCode(User $user): bool
    {
        $code = random_int(100000, 999999);

        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        Log::info('Código 2FA generado', ['user_id' => $user->id, 'code' => $code]);

        try {
            Resend::emails()->send([
                'from' => env('RESEND_FROM', 'MesaFacil <no-reply@resend.dev>'),
                'to' => [$user->email],
                'subject' => 'Código de verificación 2FA',
                'text' => "Tu código de verificación es: {$code}",
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Error enviando código 2FA: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa el login del usuario y envía código 2FA si es necesario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
                'code' => 401
            ], 401);
        }

        if ($user->two_factor_code && $user->two_factor_expires_at && Carbon::parse($user->two_factor_expires_at)->isFuture()) {
            return response()->json([
                'message' => 'Se requiere verificación de 2FA.',
                '2fa_required' => true,
                'email' => $user->email,
                'code' => 200
            ], 200);
        }

        $this->generateTwoFactorCode($user);

        return response()->json([
            'message' => 'Código 2FA enviado, verifica para continuar.',
            '2fa_required' => true,
            'email' => $user->email,
            'code' => 200
        ], 200);
    }

    /**
     * Reenvía un nuevo código 2FA
     */
    public function resend2fa(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
                'code' => 404
            ], 404);
        }

        if ($this->generateTwoFactorCode($user)) {
            return response()->json([
                'message' => 'Código 2FA reenviado correctamente.',
                'code' => 200
            ], 200);
        }

        return response()->json([
            'message' => 'No se pudo enviar el código 2FA. Intenta más tarde.',
            'code' => 500
        ], 500);
    }

    /**
     * Cierra la sesión del usuario eliminando su token actual
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $token = $user->currentAccessToken();

            if ($token) {
                $token->delete();
                return response()->json([
                    'message' => 'Sesión cerrada correctamente.',
                    'code' => 200
                ], 200);
            }

            return response()->json([
                'message' => 'Token no encontrado o ya revocado.',
                'code' => 400
            ], 400);

        } catch (\Throwable $e) {
            Log::error('Error durante logout: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error inesperado al cerrar sesión.',
                'code' => 500
            ], 500);
        }
    }
}