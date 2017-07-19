<?php
namespace App\Providers;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
class PasswordlessProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

    }
    /**
     * Regsiter application components
     */
    public function register()
    {
        $this->registerEvents();
    }
    /**
     * Register application event listeners
     */
    public function registerEvents()
    {
        // Delete user tokens after login
        if (config('passwordless.empty_tokens_after_login') === true) {
            Event::listen(
                Authenticated::class,
                function (Authenticated $event) {
                    $event->user->tokens()
                        ->delete();
                }
            );
        }
    }
}
