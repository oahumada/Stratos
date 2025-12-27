<?php

// app/Helpers/RutGenerator.php

namespace App\Helpers;

class RutGenerator
{
    public static function generate($length = 8)
    {
        // Generar número base aleatorio
        $numero = str_pad(rand(1, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        
        // Calcular dígito verificador
        $dv = self::calcularDigitoVerificador($numero);
        
        // Retornar RUT completo con formato
        return $numero . '-' . $dv;
    }

    public static function calcularDigitoVerificador($numero)
    {
        // Convierte el número a cadena para poder acceder a sus dígitos
        $numero = (string) $numero;

        // Inicializa variables
        $suma = 0;
        $multiplo = 2;

        // Calcula la suma de los dígitos multiplicados (de derecha a izquierda)
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += $numero[$i] * $multiplo;
            $multiplo = $multiplo < 7 ? $multiplo + 1 : 2;
        }

        // Calcula el resto
        $resto = $suma % 11;

        // Mapea el resultado
        switch (11 - $resto) {
            case 10:
                return 'K';
            case 11:
                return '0';
            default:
                return (string) (11 - $resto);
        }
    }

    public static function validarRut($rut)
    {
        // Eliminar puntos y guión, convertir a mayúsculas
        $rut = strtoupper(preg_replace('/[.-]/', '', $rut));
        
        // Validar formato
        if (!preg_match('/^[0-9]{7,8}[0-9K]$/', $rut)) {
            return false;
        }
        
        // Separar cuerpo y dígito verificador
        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);
        
        // Calcular dígito verificador
        $dvCalculado = self::calcularDigitoVerificador($cuerpo);
        
        // Comparar dígitos verificadores
        return $dv === $dvCalculado;
    }
}
