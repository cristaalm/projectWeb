<?php

namespace App\Services;

use App\Exceptions\ContainerException;
use App\Models\Container;
use App\Repositories\ContainerRepository;

class ContainerService
{
    public function __construct(
        private readonly ContainerRepository $containers,
    ) {}

    public function create(array $data): Container
    {
        if ($this->containers->serialNumberExists($data['serial_number'])) {
            throw new ContainerException('Ya existe un contenedor con ese número de serie.', 422);
        }

        return Container::create($data);
    }

    public function update(Container $container, array $data): Container
    {
        if ($this->containers->serialNumberExists($data['serial_number'], $container->id)) {
            throw new ContainerException('Ya existe un contenedor con ese número de serie.', 422);
        }

        $container->update($data);

        return $container;
    }

    /**
     * Rota el token del contenedor: el anterior deja de funcionar de
     * inmediato, así que el equipo físico queda sin acceso hasta que se le
     * cargue el nuevo.
     */
    public function regenerateToken(Container $container): string
    {
        $token = $container->assignNewToken();
        $container->save();

        return $token;
    }

    public function delete(Container $container): void
    {
        $container->delete();
    }
}
