<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class LimitePrestamosException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Has alcanzado el límite máximo de 3 préstamos activos.');
    }
}
