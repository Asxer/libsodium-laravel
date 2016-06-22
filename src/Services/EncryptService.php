<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 15:21
 */

namespace Asxer\CryptoApi\Services;

use Asxer\CryptoApi\Exceptions\PublicKeyNotFoundException;
use Asxer\CryptoApi\Exceptions\CannotEncryptContentException;
use Asxer\CryptoApi\Exceptions\PrivateKeyNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Sodium;

class EncryptService
{
    private $privateKey;

    public function __construct()
    {
        $this->privateKey = config('encryption.private_key', null);

        if (empty($this->privateKey)) {
            throw new PrivateKeyNotFoundException();
        }
    }

    /**
     * Decrypt incoming request.
     *
     * @throws PublicKeyNotFoundException
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request
     */
    public function decryptRequest($request) {
        $publicKey = $this->getPublicKey($request);

        if (empty($publicKey)) {
            throw new PublicKeyNotFoundException();
        }

        return $request;
    }

    /**
     * Encrypt response.
     *
     * @param  Response  $response
     * @return Response
     */
    public function encryptResponse($response) {
        return $response;
    }

    /**
     * Get public key from headers or from sessions
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getPublicKey($request) {
        return $request->header('Public-Key', null);
    }

    protected function decryptContent($request, $publicKey) {
        $content = $request->getContent();

        $plaintext = crypto_secretbox_open($content, $publicKey, $this->privateKey);
        if ($plaintext === false) {
            throw new CannotEncryptContentException();
        }
    }

    public function encryptContent($content) {
        return $content;
    }
}