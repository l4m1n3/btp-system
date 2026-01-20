<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use Illuminate\Support\Facades\Log;
use App\Models\Chantier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PatronController extends Controller
{
    public function dashboardAdmin()
    {
        $user = Auth::user();
        return view('admin.dashboard');
    }
    public function dashboardClient()
    {
        $user = Auth::user();
        $patron = Patron::where('email', $user->email)->first();
        // dd($patron->id);

        $chantiers = Chantier::with('entreprises')
            ->whereHas('entreprises', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->paginate(10);
        $Nbrchantiers = Chantier::with('entreprises')
            ->whereHas('entreprises', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->count();

        // dd($Nbrchantiers);
        return view('users.dashboard', compact('user', 'chantiers', 'patron','Nbrchantiers'));
    }


    public function companies()
    {
        $companies = Patron::orderBy('nom')->paginate(10);

        return view('admin.entreprise', compact('companies'));
    }
    public function details($id)
    {
        $company = Patron::find($id);
        $chantiers = Chantier::where('entreprise_id', $id)->paginate(5);
        // dd($chantiers);
        return view('admin.detail_entreprise', compact('company', 'chantiers'));
    }
    public function index(Request $request)
    {
        $patrons = Patron::all();
        if ($request->expectsJson()) {
            // Générer un token (ex: Laravel Sanctum)
            $token = $patrons->createToken('api-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'entreprises' => $patrons
            ], 201);
        }

        // --- Sinon c'est une requête web
        $request->session()->regenerate();
        return view('admin.entreprise', compact('patrons'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:255',
                'email' => 'required|email',
                'telephone' => 'nullable|string|max:20',
                'abonnement' => 'required',
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date',
            ]);

            $patron = Patron::create($request->all());

            $password = $request->nom . '1234';
            $user = User::create([
                'name' => $request->nom,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            // $actif = true;
            if ($request->expectsJson()) {
                // Générer un token (ex: Laravel Sanctum)
                $token = $patron->createToken('api-token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'entreprises' => $patron
                ], 201);
            }

            // --- Sinon c'est une requête web
            $request->session()->regenerate();
            return redirect()->back()->with('success', 'Entreprise créée avec succès');
        } catch (\Throwable $th) {
            Log::error('Entreprise Creation Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
    public function show($id)
    {
        $patron = Patron::find($id);
        return response()->json($patron);
    }
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nom' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:entreprises,email,' . $id,
                'telephone' => 'nullable|string|max:20',
                'abonnement' => 'in:gratuit,mensuel,annuel',
                'date_debut' => 'nullable|date',
                'date_fin' => 'nullable|date',
            ]);

            $patron = Patron::find($id);
            if (!$patron) {
                return response()->json(['message' => 'Patron not found'], 404);
            }

            $patron->update($request->all());

            if ($request->expectsJson()) {
                // Générer un token (ex: Laravel Sanctum)
                $token = $patron->createToken('api-token')->plainTextToken;

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'entreprises' => $patron
                ], 201);
            }

            // --- Sinon c'est une requête web
            $request->session()->regenerate();
            return redirect()->intended('/entreprise')->with('success', 'Entreprise modifiée avec succès');
        } catch (\Throwable $th) {
            Log::error('Patron Update Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            $patron = Patron::find($id);
            if (!$patron) {
                return response()->json(['message' => 'Patron not found'], 404);
            }

            $patron->delete();
            return response()->json(['message' => 'Patron deleted successfully']);
        } catch (\Throwable $th) {
            Log::error('Patron Deletion Error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }
}
