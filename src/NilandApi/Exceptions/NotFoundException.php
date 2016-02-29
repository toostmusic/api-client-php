<?php

namespace NilandApi\Exceptions;

class NotFoundException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($url)
    {
        return parent::__construct(sprintf(
            'The requested resource "%s" can\'t be found',
            $url
        ));
    }
}
