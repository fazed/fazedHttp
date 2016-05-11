<?php

namespace Fazed\FazedHttp;

use Fazed\FazedHttp\HeaderTrait;
use Fazed\FazedHttp\CookieTrait;

class AbstractHttp
{
    use HeaderTrait, CookieTrait;

    protected function __construct()
    {
        // Abstract
    }
}
