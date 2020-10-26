<?php

namespace Francerz\Http\Traits;

use Francerz\Http\Utils\MessageHelper;

trait ResponseTrait
{
    public function isSuccess()
    {
        return MessageHelper::isSuccess($this);
    }
}