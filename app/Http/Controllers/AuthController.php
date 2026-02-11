<?php

namespace App\Http\Controllers;

use App\Models\Patron;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }



    public function login(Request $request)
    {
        try {
            // 1️⃣ Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // 2️⃣ Tentative de connexion
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Email ou mot de passe incorrect',
                ], 401);
            }

            // 3️⃣ Utilisateur connecté
            $user = Auth::user();
            $entrepriseId = Patron::where('email', $user->email)->first();

            // 4️⃣ Nettoyage anciens tokens
            $user->tokens()->delete();

            // 5️⃣ Token Sanctum (API)
            $token = $user->createToken('access_token')->plainTextToken;

            /* ===================================================
           🟢 CAS API (Flutter / mobile)
        =================================================== */
            if ($request->expectsJson()) {
                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'nom' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'entreprise_id' => $entrepriseId ? $entrepriseId->id : null,
                    ]
                ], 200);
            }

            /* ===================================================
           🟢 CAS WEB
        =================================================== */
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Bienvenue Admin');
            } else {

                return redirect()->route('dashboard')
                    ->with('success', 'Connexion réussie');
            }
        } catch (\Throwable $e) {
            Log::error('Login Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Erreur interne du serveur',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                // Supprimer tous les tokens de l'utilisateur
                $user->tokens()->delete();
                Auth::logout();


                /* ===================================================
           🟢 CAS API (Flutter / mobile)
        =================================================== */
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Déconnexion réussie',
                    ], 200);
                }

                /* ===================================================
           🟢 CAS WEB
        =================================================== */
                $request->session()->invalidate();
                return redirect()->route('login')
                    ->with('success', 'Déconnexion réussie');
            } else {
                return response()->json([
                    'message' => 'Utilisateur non authentifié',
                ], 401);
            }
        } catch (\Throwable $e) {
            Log::error('Logout Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erreur interne du serveur',
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
