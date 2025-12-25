<?php

namespace App\Http\Controllers;

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
            // 1. Validation
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

            // 2. Tentative login
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Email ou mot de passe incorrect',
                ], 401);
            }

            // 3. Utilisateur connecté
            $user = Auth::user();

            // 4. Supprimer anciens tokens (optionnel mais conseillé)
            $user->tokens()->delete();

            // 5. Créer token Sanctum
            $token = $user->createToken('access_token')->plainTextToken;

            // 6. Réponse complète pour Flutter

             // --- Si c'est une requête API
            if ($request->expectsJson()) {
                // Générer un token (ex: Laravel Sanctum) 
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'nom' => $user->name,
                        'email' => $user->email,
                    ]
                ], 200);
            }

            // --- Sinon c'est une requête web
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Connexion réussie');

        } catch (\Throwable $e) {
            Log::error('Login Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

                return response()->json([
                    'message' => 'Déconnexion réussie',
                ], 200);
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
