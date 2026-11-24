<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\MaterialNoDisponibleException;
use App\Exceptions\LimitePrestamosException;
use App\Repos\MaterialRepo;

class PrestamoService
{
    public function __construct(private MaterialRepo $repo) {}

    /**
     * Intenta prestar un material a un usuario.
     *
     * @throws MaterialNoDisponibleException si el material ya está prestado
     * @throws \App\Exceptions\MaterialNoEncontradoException si el id no existe
     */
    public function prestar(int $materialId, int $usuarioId): void
    {
        $material = $this->repo->findById($materialId);

        if (!$material->isDisponible()) {
            throw new MaterialNoDisponibleException($material->getTitulo());
        }

        // si la consulta que hacemos en MaterialRepo da 3 o mas lanza la excepcion.
        $total = $this->repo->contarPrestamosActivos($usuarioId);

        if ($total >= 3) {
            throw new LimitePrestamosException();
        }

        $material->prestar();
        $this->repo->setDisponible($materialId, false);
        $this->repo->registrarPrestamo($materialId, $usuarioId);

        $material->log(
            "Préstamo de '{$material->getTitulo()}' al usuario {$usuarioId}"
        );
    }
}