<?php

namespace Fazed\FazedHttp;

trait SecuredTrait
{
    /**
     * Disable the SSL check for the request.
     *
     * @return $this
     */
    public function disableSslChecks()
    {
        $this->setOptions([
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);

        return $this;
    }   
}
