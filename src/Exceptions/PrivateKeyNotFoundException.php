<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 16:01
 */

namespace Asxer\CryptoApi\Exceptions;

use Exception;

class PrivateKeyNotFoundException extends Exception
{
    protected $message = 'Private key not provided';
}