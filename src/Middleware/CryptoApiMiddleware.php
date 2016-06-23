<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.06.16
 * Time: 17:27
 */

namespace Asxer\CryptoApi\Middleware;

use Asxer\CryptoApi\Exceptions\PublicKeyNotFoundException;
use Asxer\CryptoApi\Services\EncryptService;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class CryptoApiMiddleware
{
    private $encryptService;

    public function __construct()
    {
        $this->encryptService = app(EncryptService::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $request = $this->encryptService->decryptRequest($request);
        } catch (PublicKeyNotFoundException $e) {
            return response()->json([
                'message' => 'public key  not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->spoofRequest($request);

        /** @var Response $response */
        $response = $next($request);

        return $this->encryptService->encryptResponse($response);
    }

    protected function spoofRequest($request) {
        app()->instance('request', $request);
    }
}