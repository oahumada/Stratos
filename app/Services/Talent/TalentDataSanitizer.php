<?php

namespace App\Services\Talent;

use App\Helpers\RutGenerator;
use Illuminate\Support\Str;

class TalentDataSanitizer
{
    /**
     * Sanitiza y normaliza un RUT chileno.
     * Soporta formatos: 12.345.678-9, 12345678-9, 123456789, 12345678K, etc.
     */
    public static function sanitizeRut(?string $rut): ?string
    {
        $result = null;

        if ($rut) {
            // Limpiar puntos y guiones, dejar solo números y K
            $clean = strtoupper(preg_replace('/[^0-9Kk]/', '', $rut));
            
            if (!empty($clean)) {
                // Si tiene longitud suficiente para ser un RUT (7-9 caracteres)
                if (strlen($clean) >= 7) {
                    $body = substr($clean, 0, -1);
                    $dv = substr($clean, -1);
                    $result = $body . '-' . $dv;
                } else {
                    $result = $clean;
                }
            }
        }

        return $result;
    }

    /**
     * Normaliza nombres y apellidos.
     * Maneja casos donde el apellido viene junto o en campos separados.
     */
    public static function sanitizeNames(array $row): array
    {
        $firstName = $row['first_name'] ?? ($row['nombres'] ?? '');
        $lastName = $row['last_name'] ?? ($row['apellidos'] ?? '');
        
        // Si no hay apellidos pero el nombre parece contenerlos (espacios)
        if (empty($lastName) && !empty($firstName)) {
            $parts = explode(' ', trim($firstName));
            if (count($parts) > 1) {
                // Asumimos que los últimos 2 son apellidos (Paterno + Materno)
                if (count($parts) >= 3) {
                    $lastName = implode(' ', array_slice($parts, -2));
                    $firstName = implode(' ', array_slice($parts, 0, -2));
                } else {
                    $lastName = $parts[1];
                    $firstName = $parts[0];
                }
            }
        }

        return [
            'first_name' => Str::title(trim($firstName, " \t\n\r\0\x0B\"'")),
            'last_name' => Str::title(trim($lastName, " \t\n\r\0\x0B\"'")),
        ];
    }

    /**
     * Normaliza una cadena que represente una fecha en formato d/m/Y a Y-m-d.
     */
    public static function normalizeDate(?string $value): ?string
    {
        if (is_string($value) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    /**
     * Normaliza recursivamente un array de datos.
     */
    public static function normalizeArray(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return self::normalizeArray($value);
            }

            return self::normalizeDate($value);
        }, $data);
    }
}
