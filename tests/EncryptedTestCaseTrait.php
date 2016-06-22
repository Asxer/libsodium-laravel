<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 22.06.16
 * Time: 16:27
 */

namespace Asxer\CryptoApi\Tests;

trait EncryptedTestCaseTrait
{
    protected $isEncrypted = false;

    public function encrypted() {
        $this->isEncrypted = true;

        return $this;
    }
}