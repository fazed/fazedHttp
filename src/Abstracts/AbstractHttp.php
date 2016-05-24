<?php

namespace Fazed\FazedHttp\Abstracts;

use Fazed\FazedHttp\Traits\HeaderTrait;
use Fazed\FazedHttp\Traits\CookieTrait;
use Fazed\FazedHttp\Contracts\HeaderContract;
use Fazed\FazedHttp\Contracts\CookieContract;

abstract class AbstractHttp implements HeaderContract, CookieContract
{
    use HeaderTrait, CookieTrait;

    protected function __construct()
    {
        // Abstract
    }
}
