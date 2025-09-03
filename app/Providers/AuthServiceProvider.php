<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Aliziodev\LaravelTaxonomy\Models\Taxonomy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\TaxonomyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected string $redirectTo = '/app/login';

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Taxonomy::class => TaxonomyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
