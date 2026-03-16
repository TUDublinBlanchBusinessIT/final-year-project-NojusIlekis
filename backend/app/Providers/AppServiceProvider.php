<?php

namespace App\Providers;

use App\Models\Child;
use App\Models\Message;
use App\Models\User;
use App\Policies\ChildPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Child::class, ChildPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        View::composer('layouts.navigation', function ($view) {
            $unreadCount = 0;
            if (auth()->check()) {
                $unreadCount = Message::where('receiver_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
            }
            $view->with('unreadMessageCount', $unreadCount);
        });
    }
}
