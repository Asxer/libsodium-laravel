<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 16:03
 */

namespace Asxer\CryptoApi\Exceptions;

use Exception;

class CannotEncryptContentException extends Exception
{
    protected $message = 'Can not encrypt content';
}