<?php

/*
|--------------------------------------------------------------------------
| BracketService — RF-AN-06
|--------------------------------------------------------------------------
| Generación Automática de Encuentros (Brackets)
|
| Criterios de emparejamiento (SUP-03):
|   1. Nivel de poder (victorias) — criterio principal
|   2. Historial de combates (victorias / derrotas) — desempate
|   3. Reglas del torneo (modalidad configurada)
|
| Precondiciones (PRE-01, PRE-02):
|   - Participantes registrados y habilitados (estado = 'activo')
|
| Postcondiciones (POST-01, POST-02, POST-03):
|   - Brackets registrados en la tabla combates
|   - Cada combate asociado a dos luchadores con su nivel de poder
|   - Registro en log de auditoría
|--------------------------------------------------------------------------
*/

class BracketService {

    /*
    |--------------------------------------------------------------------------
    | Mínimo de participantes para generar brackets
    |--------------------------------------------------------------------------
    */
    const MIN_PARTICIPANTES = 2;

    /*
    |--------------------------------------------------------------------------
    | Generar emparejamientos
    |
    | @param  array  $participantes  Lista de luchadores habilitados.
    |                                Cada elemento debe tener:
    |                                  id_luchador, nombre, victorias, derrotas, estado
    | @param  array  $parametros     Parámetros opcionales del torneo:
    |                                  id_torneo, ronda, arena, fecha_combate
    | @return array  [
    |                  'success'  => bool,
    |                  'message'  => string,
    |                  'brackets' => array   (pares generados),
    |                  'combates' => array   (datos listos para INSERT)
    |                ]
    |--------------------------------------------------------------------------
    */
    public function generarBrackets(array $participantes, array $parametros = []): array {

        // --- PRE-01 / PRE-02: validar participantes habilitados ---
        $habilitados = $this->filtrarHabilitados($participantes);

        // --- SAL-03: insuficientes participantes ---
        if (count($habilitados) < self::MIN_PARTICIPANTES) {
            return [
                'success'  => false,
                'message'  => 'Participantes insuficientes para generar brackets. Se requieren al menos ' . self::MIN_PARTICIPANTES . '.',
                'brackets' => [],
                'combates' => []
            ];
        }

        // --- Ordenar por nivel de poder (victorias DESC), desempate por derrotas ASC ---
        $ordenados = $this->ordenarPorNivelDePoder($habilitados);

        // --- Generar pares de emparejamiento ---
        $brackets = $this->emparejar($ordenados);

        // --- Construir datos para INSERT en combates ---
        $combates = $this->construirCombates($brackets, $parametros);

        return [
            'success'  => true,
            'message'  => 'Brackets generados correctamente. Total de encuentros: ' . count($brackets) . '.',
            'brackets' => $brackets,
            'combates' => $combates
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Filtrar participantes habilitados (estado = 'activo')
    |--------------------------------------------------------------------------
    */
    public function filtrarHabilitados(array $participantes): array {
        return array_values(
            array_filter($participantes, function ($p) {
                return isset($p['estado']) && $p['estado'] === 'activo';
            })
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ordenar por nivel de poder
    |
    | Criterio principal : victorias DESC
    | Criterio desempate : derrotas ASC  (ALT-02)
    |--------------------------------------------------------------------------
    */
    public function ordenarPorNivelDePoder(array $participantes): array {
        usort($participantes, function ($a, $b) {
            $vicA = (int)($a['victorias'] ?? 0);
            $vicB = (int)($b['victorias'] ?? 0);

            if ($vicA !== $vicB) {
                return $vicB - $vicA; // mayor victorias primero
            }

            // Desempate: menos derrotas primero (ALT-02)
            $derA = (int)($a['derrotas'] ?? 0);
            $derB = (int)($b['derrotas'] ?? 0);
            return $derA - $derB;
        });

        return $participantes;
    }

    /*
    |--------------------------------------------------------------------------
    | Emparejar luchadores
    |
    | Algoritmo: el 1.° vs el último, el 2.° vs el penúltimo, etc.
    | Garantiza encuentros equilibrados (el más fuerte vs el más débil).
    | Si el número es impar, el luchador del medio queda como "bye" (sin rival).
    |--------------------------------------------------------------------------
    */
    public function emparejar(array $ordenados): array {
        $brackets = [];
        $n        = count($ordenados);
        $mitad    = (int)floor($n / 2);

        for ($i = 0; $i < $mitad; $i++) {
            $brackets[] = [
                'luchador_1' => $ordenados[$i],
                'luchador_2' => $ordenados[$n - 1 - $i]
            ];
        }

        // Si número impar, el del medio queda libre (bye)
        if ($n % 2 !== 0) {
            $brackets[] = [
                'luchador_1' => $ordenados[$mitad],
                'luchador_2' => null,  // bye
                'bye'        => true
            ];
        }

        return $brackets;
    }

    /*
    |--------------------------------------------------------------------------
    | Construir array de datos para INSERT en tabla combates
    |--------------------------------------------------------------------------
    */
    public function construirCombates(array $brackets, array $parametros = []): array {
        $combates = [];

        $idTorneo    = $parametros['id_torneo']    ?? null;
        $ronda       = $parametros['ronda']        ?? 'Ronda 1';
        $arena       = $parametros['arena']        ?? null;
        $fechaCombate = $parametros['fecha_combate'] ?? null;

        foreach ($brackets as $bracket) {
            // Saltar byes
            if (!empty($bracket['bye']) || $bracket['luchador_2'] === null) {
                continue;
            }

            $combates[] = [
                'id_torneo'        => $idTorneo,
                'id_luchador_1'    => $bracket['luchador_1']['id_luchador'],
                'id_luchador_2'    => $bracket['luchador_2']['id_luchador'],
                'ganador_id'       => null,
                'estado'           => 'pendiente',
                'ronda'            => $ronda,
                'fecha_combate'    => $fechaCombate,
                'arena'            => $arena,
                'observaciones'    => 'Generado automáticamente por BracketService',
                'duracion_segundos' => 0,
                'puntos_luchador_1' => 0,
                'puntos_luchador_2' => 0
            ];
        }

        return $combates;
    }

    /*
    |--------------------------------------------------------------------------
    | Validar integridad estructural del bracket (RNF-FIB-06-01)
    |
    | Verifica que:
    |   - No haya luchadores duplicados en el mismo bracket
    |   - Cada combate tenga exactamente dos luchadores distintos
    |--------------------------------------------------------------------------
    */
    public function validarIntegridadBracket(array $brackets): array {
        $errores    = [];
        $apariciones = [];

        foreach ($brackets as $idx => $bracket) {
            if (!empty($bracket['bye'])) {
                continue;
            }

            $id1 = $bracket['luchador_1']['id_luchador'] ?? null;
            $id2 = $bracket['luchador_2']['id_luchador'] ?? null;

            if ($id1 === null || $id2 === null) {
                $errores[] = "Bracket #{$idx}: luchador con ID nulo.";
                continue;
            }

            if ($id1 === $id2) {
                $errores[] = "Bracket #{$idx}: un luchador no puede enfrentarse a sí mismo (ID {$id1}).";
            }

            // Detectar duplicados entre brackets
            if (in_array($id1, $apariciones)) {
                $errores[] = "Bracket #{$idx}: luchador ID {$id1} aparece en más de un combate.";
            }
            if (in_array($id2, $apariciones)) {
                $errores[] = "Bracket #{$idx}: luchador ID {$id2} aparece en más de un combate.";
            }

            $apariciones[] = $id1;
            $apariciones[] = $id2;
        }

        return [
            'valido'  => empty($errores),
            'errores' => $errores
        ];
    }
}
