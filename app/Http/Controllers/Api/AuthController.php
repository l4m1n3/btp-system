<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** POST /api/login — renvoie un jeton Sanctum (un jeton par appareil). */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'appareil' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! $user->actif || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects ou compte désactivé.']);
        }

        $appareil = $data['appareil'] ?? 'mobile';
        $user->tokens()->where('name', $appareil)->delete(); // remplace seulement le jeton de cet appareil
        $user->update(['derniere_connexion' => now()]);

        return response()->json([
            'token' => $user->createToken($appareil)->plainTextToken,
            'type' => 'Bearer',
            'utilisateur' => $this->profil($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->profil($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }

    private function profil(User $user): array
    {
        return [
            'id' => $user->id,
            'nom' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'role_libelle' => $user->role->label(),
            'droits' => collect(array_keys(config('permissions')))->filter(fn ($d) => Gate::forUser($user)->allows($d))->values(),
        ];
    }
}
