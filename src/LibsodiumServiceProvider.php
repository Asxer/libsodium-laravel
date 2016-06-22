<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 16:54
 */

namespace Asxer\CryptoApi;

use Illuminate\Support\ServiceProvider;

class LibsodiumServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('encryption.php'),
        ]);
    }

    public function register()
    {

    }
}