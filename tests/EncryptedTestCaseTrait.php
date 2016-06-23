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
        /** @var EncryptService $encryptService */
        $encryptService = app(EncryptService::class);

        if ($this->isEncrypted) {
            $publicKey = $encryptService->generatePublicKey();

            $content = $encryptService->encryptContent($content, $publicKey);

            $server['HTTP_PUBLIC_KEY'] = $publicKey;
        }

        parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    public function decryptResponse() {
        /** @var EncryptService $encryptService */
        $encryptService = app(EncryptService::class);

        $publicKey = $this->response->headers->get('Public-Key');

        $content = $encryptService->decryptContent($this->response->getContent(), $publicKey);

        return response($content, $this->response->getStatusCode(), $this->response->headers->all());
    }
}