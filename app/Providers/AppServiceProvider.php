<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\Chantier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('fr');
        Model::preventLazyLoading(! $this->app->isProduction());

        // L'administrateur a tous les droits ; un compte désactivé n'en a aucun.
        Gate::before(function (User $user) {
            if (! $user->actif) {
                return false;
            }

            return $user->hasRole(Role::Admin) ? true : null;
        });

        foreach (config('permissions') as $ability => $roles) {
            Gate::define($ability, fn (User $user) => in_array($user->role?->value, $roles, true));
        }

        // Accès à un chantier précis : le chef de chantier ne voit que les siens.
        Gate::define('chantier.voir', function (User $user, Chantier $chantier) {
            if (! Gate::forUser($user)->allows('chantiers.view')) {
                return false;
            }

            return $user->voitTousLesChantiers() || $chantier->chef_chantier_id === $user->id;
        });

        Gate::define('chantier.suivre', function (User $user, Chantier $chantier) {
            return Gate::forUser($user)->allows('chantiers.suivi')
                && ($user->voitTousLesChantiers() || $chantier->chef_chantier_id === $user->id);
        });
    }
}
