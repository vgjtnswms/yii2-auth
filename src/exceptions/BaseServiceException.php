<?php

namespace vs\yii2\auth\exceptions;

/**
 * Undocumented class
 */
class BaseServiceException extends \Exception
{

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getErrors()
    {
        return ['errorMsg' => $this->message];
    }
}