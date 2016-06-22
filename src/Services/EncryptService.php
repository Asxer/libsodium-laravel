<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 15:21
 */

namespace Asxer\CryptoApi\Services;

use Asxer\CryptoApi\Exceptions\PublicKeyNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class EncryptService
{
    private $sessionId;

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
        if ($request->hasHeader('Public-Key')) {
            $this->savePublicKeyFromHeader($request);

            return $this->getPublicKeyFromSession($request);
        }

        if ($request->hasHeader('Session-Id')) {
            return $this->getPublicKeyFromSession($request);
        }

        return null;
    }

    /**
     * Save public key from header Public-Key to session with key public-key
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function savePublicKeyFromHeader($request) {
        $encryptedPublicKey = $request->header('Public-Key');

        $publicKey = $this->decryptPublicKey($encryptedPublicKey);

        $this->savePublicKey($publicKey);
    }

    /**
     * Get public key from session
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function getPublicKeyFromSession($request) {
        $sessionId = $request->header('Session-Id');

        session_id($sessionId);

        return session('public-key');
    }

    /**
     * Save public key to session with key public-key
     *
     * @param  string $publicKey
     */
    protected function savePublicKey($publicKey) {
        $this->sessionId = session_id();
        session('public-key', $publicKey);
    }

    /**
     * Decrypt public key which encrypted by RSA-algorithm with private-key
     *
     * @param  string $encryptedPublicKey
     * @return string
     */
    protected function decryptPublicKey($encryptedPublicKey) {

    }
}