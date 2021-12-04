<?php

namespace App\Providers;

use App\Models\Dictionary;
use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-word', function (User $user, Word $word) {
            return $user->is_admin || ($user->id === $word->user_id);
        });

        Gate::define('access-user', function (User $user, User $secondUser) {
            return $user->is_admin || ($user->id === $secondUser->id);
        });

        Gate::define('access-dictionary', function (User $user, Dictionary $dictionary) {
            return $user->is_admin || ($user->id === $dictionary->user_id);
        });
    }
}
