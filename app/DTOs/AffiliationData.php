<?php

namespace App\DTOs;

/**
 * Data Transfer Object (DTO) inmutable para el registro de afiliados MLM.
 * Centraliza y valida la información requerida para el registro dual (Binario y Escalonado).
 */
readonly class AffiliationData
{
    /**
     * @param  string  $name  Nombre completo del afiliado.
     * @param  string  $username  Nombre de usuario único en el sistema.
     * @param  string  $email  Correo electrónico único.
     * @param  string  $password  Contraseña en texto plano antes del hash.
     * @param  string|null  $sponsorUsername  Nombre de usuario del patrocinador directo (null solo si es el Nodo Maestro Raíz).
     * @param  string|null  $binaryLeg  Pierna de colocación en el binario: 'L' (Izquierda) o 'R' (Derecha). Null para el Master.
     */
    public function __construct(
        public string $name,
        public string $username,
        public string $email,
        public string $password,
        public ?string $sponsorUsername = null,
        public ?string $binaryLeg = null,
    ) {}

    /**
     * Crea una instancia a partir de un arreglo de entrada (ej: Form Request o Fortify Input).
     *
     * @param  array<string, mixed>  $input
     */
    public static function fromArray(array $input): self
    {
        $leg = isset($input['binary_leg']) && in_array(strtoupper((string) $input['binary_leg']), ['L', 'R'], true)
            ? strtoupper((string) $input['binary_leg'])
            : null;

        $sponsor = ! empty($input['sponsor_username']) ? trim((string) $input['sponsor_username']) : null;

        return new self(
            name: trim((string) $input['name']),
            username: strtolower(trim((string) $input['username'])),
            email: strtolower(trim((string) $input['email'])),
            password: (string) $input['password'],
            sponsorUsername: $sponsor,
            binaryLeg: $leg,
        );
    }
}
