<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Message;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      $messages = Message::all();
      \View::share('messages', $messages);
    }
}
