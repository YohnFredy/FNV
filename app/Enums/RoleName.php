<?php

namespace App\Enums;

enum RoleName: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case SUPPORT = 'support';
    case AFFILIATE = 'affiliate';
    case LEADER = 'leader';

    /**
     * Etiqueta amigable y legible para la interfaz.
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::SUPPORT => 'Soporte Técnico',
            self::AFFILIATE => 'Afiliado / Distribuidor',
            self::LEADER => 'Líder de Red',
        };
    }

    /**
     * Descripción detallada del propósito del rol.
     */
    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Acceso total y omnipotente a todos los módulos y configuraciones del sistema.',
            self::ADMIN => 'Gestión operativa completa de usuarios, red, comisiones y configuración.',
            self::SUPPORT => 'Consulta y asistencia de usuarios, pedidos y árbol genealógico sin permisos destructivos.',
            self::AFFILIATE => 'Acceso estándar a la Oficina Virtual, red personal y compras.',
            self::LEADER => 'Afiliado destacado con herramientas de liderazgo y seguimiento de equipo.',
        };
    }

    /**
     * Determina si el rol está protegido y no debe eliminarse.
     */
    public function isProtected(): bool
    {
        return match ($this) {
            self::SUPER_ADMIN, self::ADMIN, self::AFFILIATE => true,
            default => false,
        };
    }
}
