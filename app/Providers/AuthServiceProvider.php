<?php

namespace App\Providers;

use App\Models\Dossier;
use App\Models\Entreprise;
use App\Models\Classeur;
use App\Models\User;
use App\Models\Document;
use App\Policies\DossierPolicy;
use App\Policies\EntreprisePolicy;
use App\Policies\ClasseurPolicy;
use App\Policies\UserPolicy;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Dossier::class => DossierPolicy::class,
        Entreprise::class => EntreprisePolicy::class,
        Classeur::class => ClasseurPolicy::class,
        User::class => UserPolicy::class,
        Document::class => DocumentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

