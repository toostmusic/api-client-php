<?php

namespace NilandApi\Exceptions;

class BadRequestException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($content)
    {
        $errors = json_decode($content, true);

        if (true === is_array($errors)) {
            foreach ($errors as $field => &$error) {
                $error = sprintf('[%s] %s', $field, implode(', ', $error));
            }

            return parent::__construct(implode(' ', $errors));
        }

        return parent::__construct('Unknown error');
    }
}
