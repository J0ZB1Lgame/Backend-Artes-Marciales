<?php

use PHPUnit\Framework\TestCase;

/*
|==========================================================================
| PRUEBAS DE INTEGRACIÓN — RF-AN-06 Generación de Brackets
|
| Autor: Nicolas Espitia
| Requiere: MySQL corriendo en localhost:3306, BD torneo_new importada.
|
| Estas pruebas verifican el flujo completo:
|   1. Leer luchadores activos de la BD real
|   2. Ejecutar BracketService con datos reales
|   3. Persistir los combates generados en la BD
|   4. Registrar el evento en el log de auditoría (POST-03)
|   5. Limpiar los datos de prueba (rollback manual)
|
| Precondiciones:
|   - La BD tiene luchadores con estado='activo' (Goku, Vegeta, etc.)
|   - La BD tiene al menos un torneo registrado
|==========================================================================
*/

require_once __DIR__ . '/../backend/core/Database.php';
require_once __DIR__ . '/../backend/services/BracketService.php';
require_once __DIR__ . '/../backend/models/daos/combate/impl/CombateDAOImpl.php';
require_once __DIR__ . '/../backend/models/daos/luchador/impl/LuchadorDAOImpl.php';
require_once __DIR__ . '/../backend/models/daos/torneo/impl/TorneoDAOImpl.php';

class BracketIntegrationTest extends TestCase {

    private BracketService $bracketService;
    private CombateDAOImpl $combateDAO;
    private LuchadorDAOImpl $luchadorDAO;
    private TorneoDAOImpl $torneoDAO;
    private $conn;

    /** IDs de combates creados durante los tests (para limpieza) */
    private array $combatesCreados = [];

    protected function setUp(): void {
        $this->bracketService = new BracketService();
        $this->combateDAO     = new CombateDAOImpl();
        $this->luchadorDAO    = new LuchadorDAOImpl();
        $this->torneoDAO      = new TorneoDAOImpl();
        $this->conn           = Database::getInstance()->getConnection();
    }

    protected function tearDown(): void {
        // Limpiar combates creados durante los tests (POST-01 reversible)
        foreach ($this->combatesCreados as $id) {
            $this->combateDAO->delete($id);
        }
        $this->combatesCreados = [];
    }

    // =======================================================================
    // GRUPO 1 — Conexión y datos base
    // =======================================================================

    /**
     * @test
     * La conexión a la BD está activa.
     */
    public function testConexionBDActiva(): void {
        $this->assertNotNull($this->conn);
        $this->assertEmpty($this->conn->connect_error);
    }

    /**
     * @test
     * PRE-01: Existen luchadores registrados en la BD.
     */
    public function testExistenLuchadoresRegistrados(): void {
        $total = $this->luchadorDAO->countAll();
        $this->assertGreaterThan(0, (int)$total['total'], 'No hay luchadores en la BD.');
    }

    /**
     * @test
     * PRE-02: Existen luchadores habilitados (estado='activo').
     */
    public function testExistenLuchadoresActivos(): void {
        $activos = $this->luchadorDAO->getActivos();
        $this->assertNotEmpty($activos, 'No hay luchadores activos en la BD.');
        foreach ($activos as $l) {
            $this->assertEquals('activo', $l['estado']);
        }
    }

    /**
     * @test
     * Existe al menos un torneo registrado.
     */
    public function testExisteTorneoRegistrado(): void {
        $total = $this->torneoDAO->countAll();
        $this->assertGreaterThan(0, (int)$total['total'], 'No hay torneos en la BD.');
    }

    // =======================================================================
    // GRUPO 2 — Generación de brackets con datos reales
    // FN-4/5: El sistema ejecuta el algoritmo y genera los brackets
    // =======================================================================

    /**
     * @test
     * FN-4/5: Con luchadores activos reales se generan brackets correctamente.
     */
    public function testGenerarBracketsConLuchadoresReales(): void {
        $activos = $this->luchadorDAO->getActivos();

        $resultado = $this->bracketService->generarBrackets($activos, [
            'id_torneo'    => 2,
            'ronda'        => 'Ronda Test',
            'arena'        => 'Arena Test',
            'fecha_combate' => '2026-12-01'
        ]);

        $this->assertTrue($resultado['success'], $resultado['message']);
        $this->assertNotEmpty($resultado['brackets']);
        $this->assertNotEmpty($resultado['combates']);
    }

    /**
     * @test
     * SUP-03: El luchador con más victorias queda primero en el bracket.
     */
    public function testLuchadorMasVictoriasQuedaPrimeroEnBracket(): void {
        $activos   = $this->luchadorDAO->getActivos();
        $resultado = $this->bracketService->generarBrackets($activos);

        if ($resultado['success'] && !empty($resultado['brackets'])) {
            $primerBracket = $resultado['brackets'][0];
            $l1Victorias   = (int)$primerBracket['luchador_1']['victorias'];
            $l2Victorias   = (int)$primerBracket['luchador_2']['victorias'];

            // El luchador_1 debe tener >= victorias que luchador_2
            $this->assertGreaterThanOrEqual($l2Victorias, $l1Victorias,
                'El luchador_1 debería tener más o igual victorias que luchador_2.');
        } else {
            $this->markTestSkipped('No hay suficientes luchadores activos para este test.');
        }
    }

    // =======================================================================
    // GRUPO 3 — Persistencia en BD
    // POST-01: Los brackets quedan registrados en el sistema
    // POST-02: Cada combate asociado a dos luchadores con su nivel de poder
    // =======================================================================

    /**
     * @test
     * POST-01/02: Los combates generados se persisten correctamente en la BD.
     */
    public function testCombatesGeneradosSePersistenEnBD(): void {
        $activos = $this->luchadorDAO->getActivos();

        if (count($activos) < 2) {
            $this->markTestSkipped('Se necesitan al menos 2 luchadores activos.');
        }

        $resultado = $this->bracketService->generarBrackets($activos, [
            'id_torneo'    => 2,
            'ronda'        => 'Ronda Integración Test',
            'arena'        => 'Arena Test',
            'fecha_combate' => '2026-12-01'
        ]);

        $this->assertTrue($resultado['success']);

        // Persistir cada combate generado
        foreach ($resultado['combates'] as $combateData) {
            $id = $this->combateDAO->create($combateData);
            $this->assertIsInt($id);
            $this->assertGreaterThan(0, $id);
            $this->combatesCreados[] = $id; // registrar para limpieza
        }

        // Verificar que se pueden recuperar de la BD
        foreach ($this->combatesCreados as $id) {
            $combate = $this->combateDAO->getById($id);
            $this->assertNotNull($combate, "Combate ID {$id} no encontrado en BD.");
            $this->assertEquals('pendiente', $combate['estado']);
            $this->assertNotNull($combate['id_luchador_1']);
            $this->assertNotNull($combate['id_luchador_2']);
        }
    }

    /**
     * @test
     * POST-02: Cada combate persistido tiene dos luchadores distintos.
     */
    public function testCadaCombateTieneDosLuchadoresDistintos(): void {
        $activos = $this->luchadorDAO->getActivos();

        if (count($activos) < 2) {
            $this->markTestSkipped('Se necesitan al menos 2 luchadores activos.');
        }

        $resultado = $this->bracketService->generarBrackets($activos, [
            'id_torneo'    => 2,
            'ronda'        => 'Ronda Distintos Test',
            'fecha_combate' => '2026-12-02'
        ]);

        foreach ($resultado['combates'] as $combateData) {
            $this->assertNotEquals(
                $combateData['id_luchador_1'],
                $combateData['id_luchador_2'],
                'Un luchador no puede enfrentarse a sí mismo.'
            );

            $id = $this->combateDAO->create($combateData);
            $this->combatesCreados[] = $id;
        }
    }

    // =======================================================================
    // GRUPO 4 — Log de auditoría
    // POST-03 / SAL-04: Registro en log con brackets, actor y fecha
    // =======================================================================

    /**
     * @test
     * POST-03/SAL-04: Se puede registrar el evento de generación en el log.
     */
    public function testRegistroEnLogDeAuditoria(): void {
        // Verificar que la tabla log existe y acepta inserts
        $accion  = 'Generación de brackets - Test RF-AN-06 - ' . date('Y-m-d H:i:s');
        $idUsuario = 1; // admin

        $stmt = $this->conn->prepare(
            "INSERT INTO log (accion, fecha, id_usuario) VALUES (?, NOW(), ?)"
        );
        $stmt->bind_param("si", $accion, $idUsuario);
        $ok = $stmt->execute();
        $logId = $this->conn->insert_id;
        $stmt->close();

        $this->assertTrue($ok, 'No se pudo insertar en la tabla log.');
        $this->assertGreaterThan(0, $logId);

        // Verificar que el registro existe
        $stmt2 = $this->conn->prepare("SELECT * FROM log WHERE id_log = ?");
        $stmt2->bind_param("i", $logId);
        $stmt2->execute();
        $result = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        $this->assertNotNull($result);
        $this->assertEquals($accion, $result['accion']);
        $this->assertEquals($idUsuario, (int)$result['id_usuario']);

        // Limpiar el log de prueba
        $this->conn->query("DELETE FROM log WHERE id_log = {$logId}");
    }

    // =======================================================================
    // GRUPO 5 — Integridad estructural (RNF-FIB-06-01)
    // =======================================================================

    /**
     * @test
     * RNF-FIB-06-01: Los brackets generados con datos reales son estructuralmente válidos.
     */
    public function testBracketsRealesSonEstructuralmenteValidos(): void {
        $activos   = $this->luchadorDAO->getActivos();
        $resultado = $this->bracketService->generarBrackets($activos);

        if (!$resultado['success']) {
            $this->markTestSkipped('No hay suficientes luchadores activos.');
        }

        $validacion = $this->bracketService->validarIntegridadBracket($resultado['brackets']);

        $this->assertTrue($validacion['valido'],
            'Errores de integridad: ' . implode(', ', $validacion['errores']));
    }

    /**
     * @test
     * RNF-FIB-06-01: Ningún luchador aparece en más de un combate del mismo bracket.
     */
    public function testNingunLuchadorAparaceEnDosCombates(): void {
        $activos   = $this->luchadorDAO->getActivos();
        $resultado = $this->bracketService->generarBrackets($activos);

        if (!$resultado['success']) {
            $this->markTestSkipped('No hay suficientes luchadores activos.');
        }

        $idsUsados = [];
        foreach ($resultado['combates'] as $combate) {
            $id1 = $combate['id_luchador_1'];
            $id2 = $combate['id_luchador_2'];

            $this->assertNotContains($id1, $idsUsados, "Luchador ID {$id1} aparece en más de un combate.");
            $this->assertNotContains($id2, $idsUsados, "Luchador ID {$id2} aparece en más de un combate.");

            $idsUsados[] = $id1;
            $idsUsados[] = $id2;
        }
    }

    // =======================================================================
    // GRUPO 6 — Camino de excepción (SAL-03)
    // =======================================================================

    /**
     * @test
     * SAL-03: Si se filtra a 0 activos, el sistema notifica el error.
     */
    public function testSistemaNotificaParticipantesInsuficientes(): void {
        // Simular que no hay activos pasando solo inactivos
        $soloInactivos = [
            ['id_luchador' => 99, 'nombre' => 'Test', 'victorias' => 0, 'derrotas' => 0, 'estado' => 'inactivo']
        ];

        $resultado = $this->bracketService->generarBrackets($soloInactivos);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('insuficientes', strtolower($resultado['message']));
    }

    // =======================================================================
    // GRUPO 7 — Consultas DAO de combates (getByTorneo, getHistorial)
    // POST-01: Brackets disponibles para gestión en módulo de cronograma
    // =======================================================================

    /**
     * @test
     * POST-01: getByTorneo retorna los combates del torneo 2 (datos de prueba).
     */
    public function testGetByTorneoRetornaCombatesDelTorneo(): void {
        $combates = $this->combateDAO->getByTorneo(2);

        $this->assertIsArray($combates);
        // El torneo 2 tiene combates en los datos de prueba del SQL
        $this->assertNotEmpty($combates, 'El torneo 2 debería tener combates registrados.');

        foreach ($combates as $c) {
            $this->assertEquals(2, (int)$c['id_torneo']);
        }
    }

    /**
     * @test
     * POST-01: getHistorialLuchador retorna combates del luchador 1 (Goku).
     */
    public function testGetHistorialLuchadorRetornaCombates(): void {
        $historial = $this->combateDAO->getHistorialLuchador(1);

        $this->assertIsArray($historial);
        $this->assertNotEmpty($historial, 'Goku debería tener historial de combates.');

        foreach ($historial as $c) {
            $estaEnCombate = ((int)$c['id_luchador_1'] === 1 || (int)$c['id_luchador_2'] === 1);
            $this->assertTrue($estaEnCombate, 'El historial contiene combates de otro luchador.');
        }
    }

    /**
     * @test
     * getActivos retorna solo combates con estado='activo' (si existen).
     */
    public function testGetActivosRetornaSoloCombatesActivos(): void {
        $activos = $this->combateDAO->getActivos();

        $this->assertIsArray($activos);
        foreach ($activos as $c) {
            $this->assertEquals('activo', $c['estado']);
        }
    }

    /**
     * @test
     * getFinalizados retorna solo combates con estado='finalizado'.
     */
    public function testGetFinalizadosRetornaSoloCombatesFinalizados(): void {
        $finalizados = $this->combateDAO->getFinalizados();

        $this->assertIsArray($finalizados);
        $this->assertNotEmpty($finalizados, 'Deberían existir combates finalizados en los datos de prueba.');

        foreach ($finalizados as $c) {
            $this->assertEquals('finalizado', $c['estado']);
        }
    }

    /**
     * @test
     * countByEstado retorna el conteo correcto para 'pendiente'.
     */
    public function testCountByEstadoRetornaConteoCorrectoPendiente(): void {
        $resultado = $this->combateDAO->countByEstado('pendiente');

        $this->assertArrayHasKey('total', $resultado);
        $this->assertGreaterThanOrEqual(0, (int)$resultado['total']);
    }
}
