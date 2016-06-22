<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 16:27
 */

namespace Asxer\CryptoApi\Tests;

use Asxer\CryptoApi\Services\EncryptService;

trait EncryptedTestCaseTrait
{
    protected $isEncrypted = false;

    public function encrypted() {
        $this->isEncrypted = true;

        return $this;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if ($this->isEncrypted) {
            $server['PUBLIC_KEY'] = '123';
            $content = app(EncryptService::class)->encryptContent();
        }

        parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
}