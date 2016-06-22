<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 10:54
 */

namespace Asxer\CryptoApi\Exceptions;

use Exception;

class PublicKeyNotFoundException extends Exception
{
    protected $message = 'Public key not found';
}