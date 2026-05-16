<?php

use PHPUnit\Framework\TestCase;

/*
|==========================================================================
| PRUEBAS UNITARIAS — BracketService
| Requerimiento: RF-AN-06 – Generación Automática de Encuentros (Brackets)
| Identificador: RF-06-01 – Generar Emparejamientos
|
| Autor: Nicolas Espitia
| Cobertura:
|   - Flujo normal (FN): generación exitosa de brackets
|   - Camino alternativo ALT-01: cancelación / sin participantes
|   - Camino alternativo ALT-02: desempate por historial
|   - Camino de excepción: participantes insuficientes (SAL-03)
|   - Postcondición POST-02: cada combate asociado a dos luchadores
|   - RNF-FIB-06-01: integridad estructural del bracket
|==========================================================================
*/

require_once __DIR__ . '/../backend/services/BracketService.php';

class BracketServiceTest extends TestCase {

    private BracketService $service;

    // -----------------------------------------------------------------------
    // Fixtures reutilizables
    // -----------------------------------------------------------------------

    /** Luchadores activos con victorias distintas */
    private function luchadores4(): array {
        return [
            ['id_luchador' => 1, 'nombre' => 'Goku',    'victorias' => 45, 'derrotas' => 2,  'estado' => 'activo'],
            ['id_luchador' => 2, 'nombre' => 'Vegeta',  'victorias' => 38, 'derrotas' => 7,  'estado' => 'activo'],
            ['id_luchador' => 3, 'nombre' => 'Gohan',   'victorias' => 20, 'derrotas' => 3,  'estado' => 'activo'],
            ['id_luchador' => 4, 'nombre' => 'Piccolo', 'victorias' => 30, 'derrotas' => 5,  'estado' => 'activo'],
        ];
    }

    /** Luchadores con victorias iguales para probar desempate */
    private function luchadoresEmpate(): array {
        return [
            ['id_luchador' => 10, 'nombre' => 'A', 'victorias' => 20, 'derrotas' => 5,  'estado' => 'activo'],
            ['id_luchador' => 11, 'nombre' => 'B', 'victorias' => 20, 'derrotas' => 2,  'estado' => 'activo'],
            ['id_luchador' => 12, 'nombre' => 'C', 'victorias' => 20, 'derrotas' => 8,  'estado' => 'activo'],
            ['id_luchador' => 13, 'nombre' => 'D', 'victorias' => 20, 'derrotas' => 1,  'estado' => 'activo'],
        ];
    }

    /** Mezcla de activos e inactivos */
    private function luchadoresMixtos(): array {
        return [
            ['id_luchador' => 1, 'nombre' => 'Goku',   'victorias' => 45, 'derrotas' => 2, 'estado' => 'activo'],
            ['id_luchador' => 2, 'nombre' => 'Yamcha', 'victorias' => 12, 'derrotas' => 14,'estado' => 'inactivo'],
            ['id_luchador' => 3, 'nombre' => 'Gohan',  'victorias' => 20, 'derrotas' => 3, 'estado' => 'activo'],
            ['id_luchador' => 4, 'nombre' => 'Freezer','victorias' => 1000,'derrotas' => 1,'estado' => 'inactivo'],
        ];
    }

    protected function setUp(): void {
        $this->service = new BracketService();
    }

    // =======================================================================
    // GRUPO 1 — filtrarHabilitados()
    // PRE-01 / PRE-02: solo participantes con estado = 'activo'
    // =======================================================================

    /**
     * @test
     * PRE-02: Solo los luchadores con estado 'activo' deben incluirse.
     */
    public function testFiltrarHabilitadosExcluyeInactivos(): void {
        $resultado = $this->service->filtrarHabilitados($this->luchadoresMixtos());

        $this->assertCount(2, $resultado);
        foreach ($resultado as $l) {
            $this->assertEquals('activo', $l['estado']);
        }
    }

    /**
     * @test
     * PRE-02: Lista vacía devuelve array vacío.
     */
    public function testFiltrarHabilitadosListaVacia(): void {
        $resultado = $this->service->filtrarHabilitados([]);
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * @test
     * PRE-02: Todos inactivos devuelve array vacío.
     */
    public function testFiltrarHabilitadosTodosInactivos(): void {
        $inactivos = [
            ['id_luchador' => 1, 'nombre' => 'X', 'victorias' => 5, 'derrotas' => 0, 'estado' => 'inactivo'],
            ['id_luchador' => 2, 'nombre' => 'Y', 'victorias' => 3, 'derrotas' => 1, 'estado' => 'inactivo'],
        ];
        $resultado = $this->service->filtrarHabilitados($inactivos);
        $this->assertEmpty($resultado);
    }

    // =======================================================================
    // GRUPO 2 — ordenarPorNivelDePoder()
    // SUP-03: victorias DESC, desempate derrotas ASC (ALT-02)
    // =======================================================================

    /**
     * @test
     * SUP-03: El luchador con más victorias debe quedar primero.
     */
    public function testOrdenarPorNivelDePoderVictoriasDesc(): void {
        $ordenados = $this->service->ordenarPorNivelDePoder($this->luchadores4());

        $this->assertEquals(1, $ordenados[0]['id_luchador']); // Goku: 45 victorias
        $this->assertEquals(2, $ordenados[1]['id_luchador']); // Vegeta: 38
        $this->assertEquals(4, $ordenados[2]['id_luchador']); // Piccolo: 30
        $this->assertEquals(3, $ordenados[3]['id_luchador']); // Gohan: 20
    }

    /**
     * @test
     * ALT-02: Con victorias iguales, el que tiene menos derrotas va primero.
     */
    public function testOrdenarDesempatePorDerrotasAsc(): void {
        $ordenados = $this->service->ordenarPorNivelDePoder($this->luchadoresEmpate());

        // Todos tienen 20 victorias; orden esperado por derrotas ASC: D(1), B(2), A(5), C(8)
        $this->assertEquals(13, $ordenados[0]['id_luchador']); // D: 1 derrota
        $this->assertEquals(11, $ordenados[1]['id_luchador']); // B: 2 derrotas
        $this->assertEquals(10, $ordenados[2]['id_luchador']); // A: 5 derrotas
        $this->assertEquals(12, $ordenados[3]['id_luchador']); // C: 8 derrotas
    }

    /**
     * @test
     * SUP-03: Un solo luchador devuelve lista de un elemento sin error.
     */
    public function testOrdenarUnSoloLuchador(): void {
        $uno = [['id_luchador' => 1, 'nombre' => 'Solo', 'victorias' => 10, 'derrotas' => 0, 'estado' => 'activo']];
        $resultado = $this->service->ordenarPorNivelDePoder($uno);
        $this->assertCount(1, $resultado);
    }

    // =======================================================================
    // GRUPO 3 — emparejar()
    // POST-02: cada combate asociado a dos luchadores distintos
    // =======================================================================

    /**
     * @test
     * FN-5: Con 4 luchadores se generan exactamente 2 brackets.
     */
    public function testEmparejarCuatroLuchadoresGeneraDosEncuentros(): void {
        $ordenados = $this->service->ordenarPorNivelDePoder($this->luchadores4());
        $brackets  = $this->service->emparejar($ordenados);

        $this->assertCount(2, $brackets);
    }

    /**
     * @test
     * POST-02: El más fuerte enfrenta al más débil (bracket balanceado).
     */
    public function testEmparejarMasFuerteVsMasDebil(): void {
        $ordenados = $this->service->ordenarPorNivelDePoder($this->luchadores4());
        $brackets  = $this->service->emparejar($ordenados);

        // Bracket 0: Goku (1.°) vs Gohan (último)
        $this->assertEquals(1, $brackets[0]['luchador_1']['id_luchador']); // Goku
        $this->assertEquals(3, $brackets[0]['luchador_2']['id_luchador']); // Gohan

        // Bracket 1: Vegeta (2.°) vs Piccolo (3.°)
        $this->assertEquals(2, $brackets[1]['luchador_1']['id_luchador']); // Vegeta
        $this->assertEquals(4, $brackets[1]['luchador_2']['id_luchador']); // Piccolo
    }

    /**
     * @test
     * FN-5: Con número impar de luchadores, el del medio queda como bye.
     */
    public function testEmparejarNumeroimparGeneraBye(): void {
        $tres = [
            ['id_luchador' => 1, 'nombre' => 'A', 'victorias' => 30, 'derrotas' => 0, 'estado' => 'activo'],
            ['id_luchador' => 2, 'nombre' => 'B', 'victorias' => 20, 'derrotas' => 1, 'estado' => 'activo'],
            ['id_luchador' => 3, 'nombre' => 'C', 'victorias' => 10, 'derrotas' => 2, 'estado' => 'activo'],
        ];
        $brackets = $this->service->emparejar($tres);

        $this->assertCount(2, $brackets);                          // 1 combate + 1 bye
        $this->assertTrue($brackets[1]['bye'] ?? false);           // el último es bye
        $this->assertNull($brackets[1]['luchador_2']);              // sin rival
    }

    /**
     * @test
     * POST-02: Con 2 luchadores se genera exactamente 1 bracket con ambos luchadores.
     */
    public function testEmparejarDosLuchadoresGeneraUnEncuentro(): void {
        $dos = [
            ['id_luchador' => 1, 'nombre' => 'A', 'victorias' => 10, 'derrotas' => 0, 'estado' => 'activo'],
            ['id_luchador' => 2, 'nombre' => 'B', 'victorias' => 5,  'derrotas' => 1, 'estado' => 'activo'],
        ];
        $ordenados = $this->service->ordenarPorNivelDePoder($dos);
        $brackets  = $this->service->emparejar($ordenados);

        $this->assertCount(1, $brackets);
        $this->assertNotNull($brackets[0]['luchador_1']);
        $this->assertNotNull($brackets[0]['luchador_2']);
        // El más fuerte (A: 10 victorias) queda como luchador_1
        $this->assertEquals(1, $brackets[0]['luchador_1']['id_luchador']);
        $this->assertEquals(2, $brackets[0]['luchador_2']['id_luchador']);
    }

    // =======================================================================
    // GRUPO 4 — generarBrackets() — flujo completo
    // =======================================================================

    /**
     * @test
     * FN-4/5: Flujo normal — genera brackets con éxito y retorna SAL-01/SAL-02.
     */
    public function testGenerarBracketsFlujoCorrecto(): void {
        $parametros = [
            'id_torneo'    => 1,
            'ronda'        => 'Cuartos de final',
            'arena'        => 'Ring Central',
            'fecha_combate' => '2026-07-15'
        ];

        $resultado = $this->service->generarBrackets($this->luchadores4(), $parametros);

        $this->assertTrue($resultado['success']);
        $this->assertStringContainsString('correctamente', $resultado['message']); // SAL-02
        $this->assertNotEmpty($resultado['brackets']);                              // SAL-01
        $this->assertNotEmpty($resultado['combates']);
    }

    /**
     * @test
     * SAL-03: Participantes insuficientes — retorna error con mensaje claro.
     */
    public function testGenerarBracketsParticipantesInsuficientes(): void {
        $soloUno = [
            ['id_luchador' => 1, 'nombre' => 'Goku', 'victorias' => 45, 'derrotas' => 2, 'estado' => 'activo']
        ];

        $resultado = $this->service->generarBrackets($soloUno);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('insuficientes', strtolower($resultado['message'])); // SAL-03
        $this->assertEmpty($resultado['brackets']);
        $this->assertEmpty($resultado['combates']);
    }

    /**
     * @test
     * SAL-03: Lista vacía — retorna error.
     */
    public function testGenerarBracketsListaVacia(): void {
        $resultado = $this->service->generarBrackets([]);

        $this->assertFalse($resultado['success']);
        $this->assertEmpty($resultado['brackets']);
    }

    /**
     * @test
     * ALT-01: Todos inactivos — equivale a cancelación, retorna error.
     */
    public function testGenerarBracketsConSoloInactivosRetornaError(): void {
        $inactivos = [
            ['id_luchador' => 1, 'nombre' => 'X', 'victorias' => 10, 'derrotas' => 0, 'estado' => 'inactivo'],
            ['id_luchador' => 2, 'nombre' => 'Y', 'victorias' => 8,  'derrotas' => 1, 'estado' => 'inactivo'],
        ];

        $resultado = $this->service->generarBrackets($inactivos);

        $this->assertFalse($resultado['success']);
    }

    /**
     * @test
     * PRE-02: Solo los activos participan aunque haya inactivos en la lista.
     */
    public function testGenerarBracketsIgnoraInactivos(): void {
        $resultado = $this->service->generarBrackets($this->luchadoresMixtos());

        // Solo 2 activos → 1 combate
        $this->assertTrue($resultado['success']);
        $this->assertCount(1, $resultado['combates']);
    }

    // =======================================================================
    // GRUPO 5 — construirCombates()
    // POST-02: estructura correcta de cada combate generado
    // =======================================================================

    /**
     * @test
     * POST-02: Cada combate tiene id_luchador_1, id_luchador_2 y estado 'pendiente'.
     */
    public function testConstruirCombatesEstructuraCorrecta(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'Goku'],
                'luchador_2' => ['id_luchador' => 3, 'nombre' => 'Gohan']
            ]
        ];
        $parametros = ['id_torneo' => 2, 'ronda' => 'Semifinal', 'arena' => 'Ring A', 'fecha_combate' => '2026-07-16'];

        $combates = $this->service->construirCombates($brackets, $parametros);

        $this->assertCount(1, $combates);
        $this->assertEquals(1, $combates[0]['id_luchador_1']);
        $this->assertEquals(3, $combates[0]['id_luchador_2']);
        $this->assertEquals('pendiente', $combates[0]['estado']);
        $this->assertEquals(2, $combates[0]['id_torneo']);
        $this->assertEquals('Semifinal', $combates[0]['ronda']);
        $this->assertNull($combates[0]['ganador_id']);
    }

    /**
     * @test
     * POST-02: Los byes no generan combates.
     */
    public function testConstruirCombatesOmiteByes(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'A'],
                'luchador_2' => ['id_luchador' => 3, 'nombre' => 'C']
            ],
            [
                'luchador_1' => ['id_luchador' => 2, 'nombre' => 'B'],
                'luchador_2' => null,
                'bye'        => true
            ]
        ];

        $combates = $this->service->construirCombates($brackets);

        $this->assertCount(1, $combates); // solo el combate real
    }

    /**
     * @test
     * POST-02: El campo 'observaciones' indica generación automática.
     */
    public function testConstruirCombatesObservacionesAutomaticas(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'A'],
                'luchador_2' => ['id_luchador' => 2, 'nombre' => 'B']
            ]
        ];

        $combates = $this->service->construirCombates($brackets);

        $this->assertStringContainsString('automáticamente', $combates[0]['observaciones']);
    }

    // =======================================================================
    // GRUPO 6 — validarIntegridadBracket()
    // RNF-FIB-06-01: Integridad estructural de los brackets generados
    // =======================================================================

    /**
     * @test
     * RNF-FIB-06-01: Bracket válido — sin errores.
     */
    public function testValidarIntegridadBracketValido(): void {
        $ordenados = $this->service->ordenarPorNivelDePoder($this->luchadores4());
        $brackets  = $this->service->emparejar($ordenados);

        $validacion = $this->service->validarIntegridadBracket($brackets);

        $this->assertTrue($validacion['valido']);
        $this->assertEmpty($validacion['errores']);
    }

    /**
     * @test
     * RNF-FIB-06-01: Un luchador no puede enfrentarse a sí mismo.
     */
    public function testValidarIntegridadBracketLuchadorContraSiMismo(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 5, 'nombre' => 'Krillin'],
                'luchador_2' => ['id_luchador' => 5, 'nombre' => 'Krillin'] // mismo ID
            ]
        ];

        $validacion = $this->service->validarIntegridadBracket($brackets);

        $this->assertFalse($validacion['valido']);
        $this->assertNotEmpty($validacion['errores']);
    }

    /**
     * @test
     * RNF-FIB-06-01: Un luchador no puede aparecer en dos combates distintos.
     */
    public function testValidarIntegridadBracketLuchadorDuplicado(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'Goku'],
                'luchador_2' => ['id_luchador' => 2, 'nombre' => 'Vegeta']
            ],
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'Goku'], // duplicado
                'luchador_2' => ['id_luchador' => 3, 'nombre' => 'Gohan']
            ]
        ];

        $validacion = $this->service->validarIntegridadBracket($brackets);

        $this->assertFalse($validacion['valido']);
        $this->assertNotEmpty($validacion['errores']);
    }

    /**
     * @test
     * RNF-FIB-06-01: Los byes no generan errores de integridad.
     */
    public function testValidarIntegridadBracketByeEsValido(): void {
        $brackets = [
            [
                'luchador_1' => ['id_luchador' => 1, 'nombre' => 'A'],
                'luchador_2' => ['id_luchador' => 3, 'nombre' => 'C']
            ],
            [
                'luchador_1' => ['id_luchador' => 2, 'nombre' => 'B'],
                'luchador_2' => null,
                'bye'        => true
            ]
        ];

        $validacion = $this->service->validarIntegridadBracket($brackets);

        $this->assertTrue($validacion['valido']);
    }

    // =======================================================================
    // GRUPO 7 — Casos límite y robustez
    // =======================================================================

    /**
     * @test
     * Exactamente 2 participantes activos — mínimo válido.
     */
    public function testGenerarBracketsMinimoDosParticipantes(): void {
        $dos = [
            ['id_luchador' => 1, 'nombre' => 'A', 'victorias' => 10, 'derrotas' => 0, 'estado' => 'activo'],
            ['id_luchador' => 2, 'nombre' => 'B', 'victorias' => 5,  'derrotas' => 1, 'estado' => 'activo'],
        ];

        $resultado = $this->service->generarBrackets($dos);

        $this->assertTrue($resultado['success']);
        $this->assertCount(1, $resultado['combates']);
    }

    /**
     * @test
     * 8 participantes activos generan exactamente 4 combates (bracket completo).
     */
    public function testGenerarBracketsOchoParticipantesGeneraCuatroCombates(): void {
        $ocho = [];
        for ($i = 1; $i <= 8; $i++) {
            $ocho[] = [
                'id_luchador' => $i,
                'nombre'      => "Luchador{$i}",
                'victorias'   => $i * 5,
                'derrotas'    => $i,
                'estado'      => 'activo'
            ];
        }

        $resultado = $this->service->generarBrackets($ocho);

        $this->assertTrue($resultado['success']);
        $this->assertCount(4, $resultado['combates']);
        $this->assertCount(4, $resultado['brackets']);
    }

    /**
     * @test
     * El número de combates generados es correcto para N par de participantes.
     */
    public function testNumeroCombatesEsNDivididoPorDos(): void {
        for ($n = 2; $n <= 10; $n += 2) {
            $participantes = [];
            for ($i = 1; $i <= $n; $i++) {
                $participantes[] = [
                    'id_luchador' => $i,
                    'nombre'      => "L{$i}",
                    'victorias'   => $i,
                    'derrotas'    => 0,
                    'estado'      => 'activo'
                ];
            }
            $resultado = $this->service->generarBrackets($participantes);
            $this->assertCount($n / 2, $resultado['combates'], "Fallo con N={$n}");
        }
    }

    /**
     * @test
     * SUP-03: El resultado incluye la clave 'brackets' con la estructura correcta.
     */
    public function testResultadoTieneClaveBracketsYCombates(): void {
        $resultado = $this->service->generarBrackets($this->luchadores4());

        $this->assertArrayHasKey('success',  $resultado);
        $this->assertArrayHasKey('message',  $resultado);
        $this->assertArrayHasKey('brackets', $resultado);
        $this->assertArrayHasKey('combates', $resultado);
    }
}
