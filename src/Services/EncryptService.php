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
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request as SymphonyRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EncryptService
{
    private $privateKey;

    public function __construct()
    {
        $privateKey = config('encryption.private_key', null);

        $this->privateKey = hex2bin($privateKey);

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

        $content = $request->getContent();
        $decryptedContent = $this->decryptContent($content, $publicKey);

        return $this->makeJsonRequest($request, $decryptedContent);
    }

    /**
     * Encrypt response.
     *
     * @param  Response  $response
     * @return Response
     */
    public function encryptResponse($response) {
        $publicKey = $this->generatePublicKey();

        $content = $response->getContent();
        $encryptedContent = $this->encryptContent($content, $publicKey);

        return $this->makeJsonResponse($response, $encryptedContent, $publicKey);
    }

    /**
     * Make Json Response.
     *
     * @param  Response  $response
     * @param  string    $encryptedContent
     * @param  string    $publicKey
     * @return Response
     */
    protected function makeJsonResponse($response, $encryptedContent, $publicKey) {
        $headers = $response->headers->all();
        $headers['PUBLIC_KEY'] = $publicKey;

        return response($encryptedContent, $response->getStatusCode(), $headers);
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

    public function decryptContent($content, $publicKey) {
        $plaintext = sodium_crypto_secretbox_open($content, $publicKey, $this->privateKey);

        if ($plaintext === false) {
            throw new CannotEncryptContentException();
        }

        return $plaintext;
    }

    protected function makeJsonRequest($request, $content) {
        $newRequest = SymphonyRequest::create(
            $request->getUri(),
            $request->getMethod(),
            [],
            $request->cookie(),
            [],
            $this->transformHeadersToServerVars($request->headers->all()),
            $content
        );

        return Request::createFromBase($newRequest);
    }

    public function encryptContent($content, $publicKey) {
        return sodium_crypto_secretbox($content, $publicKey, $this->privateKey);
    }

    public function generatePublicKey() {
        return random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param  array  $headers
     * @return array
     */
    protected function transformHeadersToServerVars(array $headers)
    {
        $server = [];
        $prefix = 'HTTP_';

        foreach ($headers as $name => $value) {
            $name = strtr(strtoupper($name), '-', '_');

            if (! Str::startsWith($name, $prefix) && $name != 'CONTENT_TYPE') {
                $name = $prefix.$name;
            }

            $server[$name] = $value;
        }

        return $server;
    }
}