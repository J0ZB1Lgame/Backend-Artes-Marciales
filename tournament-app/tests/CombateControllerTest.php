<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/*
|==========================================================================
| PRUEBAS UNITARIAS — CombateController
| Requerimiento: RF-AN-06 – Generación Automática de Encuentros (Brackets)
|
| Autor: Nicolas Espitia
| Estrategia: Mock del DAO para aislar el Controller de la BD.
|             Se usa ReflectionClass para inyectar el mock sin constructor.
|
| Cobertura:
|   - create()  → validaciones de entrada (SAL-03 parcial)
|   - getAll()  → listado de combates (SAL-01)
|   - getById() → combate individual
|   - update()  → actualización de estado
|   - delete()  → eliminación
|   - search()  → búsqueda
|   - countAll()→ conteo
|==========================================================================
*/

require_once __DIR__ . '/../backend/api/controllers/combate/CombateController.php';

class CombateControllerTest extends TestCase {

    private CombateController $controller;
    private MockObject $daoMock;

    protected function setUp(): void {
        // Crear mock del DAO sin necesitar conexión real
        $this->daoMock = $this->createMock(CombateDAOImpl::class);

        // Instanciar Controller sin ejecutar su constructor (evita conexión a BD)
        $reflection = new ReflectionClass(CombateController::class);
        $this->controller = $reflection->newInstanceWithoutConstructor();

        // Inyectar el mock via reflexión
        $prop = $reflection->getProperty('combateDAO');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $this->daoMock);
    }

    // =======================================================================
    // GRUPO 1 — getAll()
    // SAL-01: Visualización de los emparejamientos generados
    // =======================================================================

    /**
     * @test
     * SAL-01: getAll retorna success=true con lista de combates.
     */
    public function testGetAllRetornaListaDeCombates(): void {
        $this->daoMock->method('getAll')->willReturn([
            ['id_combate' => 1, 'id_torneo' => 2, 'id_luchador_1' => 1, 'id_luchador_2' => 3, 'estado' => 'pendiente', 'ronda' => 'Cuartos de final'],
            ['id_combate' => 2, 'id_torneo' => 2, 'id_luchador_1' => 2, 'id_luchador_2' => 4, 'estado' => 'pendiente', 'ronda' => 'Cuartos de final'],
        ]);

        $result = $this->controller->getAll();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']);
    }

    /**
     * @test
     * SAL-01: getAll con lista vacía retorna success=true y data vacío.
     */
    public function testGetAllListaVaciaRetornaSuccessTrue(): void {
        $this->daoMock->method('getAll')->willReturn([]);

        $result = $this->controller->getAll();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertEmpty($result['data']);
    }

    /**
     * @test
     * getAll maneja excepción del DAO y retorna success=false.
     */
    public function testGetAllManejaExcepcionDelDAO(): void {
        $this->daoMock->method('getAll')
            ->willThrowException(new Exception('Error de conexión'));

        $result = $this->controller->getAll();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error de conexión', $result['message']);
    }

    // =======================================================================
    // GRUPO 2 — getById()
    // =======================================================================

    /**
     * @test
     * getById retorna el combate cuando existe.
     */
    public function testGetByIdRetornaCombateCuandoExiste(): void {
        $combate = ['id_combate' => 7, 'id_torneo' => 2, 'id_luchador_1' => 1, 'id_luchador_2' => 2, 'estado' => 'pendiente', 'ronda' => 'Final'];
        $this->daoMock->method('getById')->with(7)->willReturn($combate);

        $result = $this->controller->getById(7);

        $this->assertTrue($result['success']);
        $this->assertEquals(7, $result['data']['id_combate']);
        $this->assertEquals('Final', $result['data']['ronda']);
    }

    /**
     * @test
     * getById retorna success=false cuando el combate no existe.
     */
    public function testGetByIdRetornaFalseCuandoNoExiste(): void {
        $this->daoMock->method('getById')->with(999)->willReturn(null);

        $result = $this->controller->getById(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Combate no encontrado', $result['message']);
    }

    // =======================================================================
    // GRUPO 3 — create()
    // POST-02: cada combate debe tener id_torneo, id_luchador_1, id_luchador_2
    // =======================================================================

    /**
     * @test
     * POST-02: create exitoso con datos completos retorna success=true e ID.
     */
    public function testCreateExitosoConDatosCompletos(): void {
        $this->daoMock->method('create')->willReturn(8);

        $data = [
            'id_torneo'        => 2,
            'id_luchador_1'    => 1,
            'id_luchador_2'    => 3,
            'ganador_id'       => null,
            'estado'           => 'pendiente',
            'ronda'            => 'Cuartos de final',
            'fecha_combate'    => '2026-07-15',
            'arena'            => 'Ring Central',
            'observaciones'    => 'Generado automáticamente por BracketService',
            'duracion_segundos' => 0,
            'puntos_luchador_1' => 0,
            'puntos_luchador_2' => 0
        ];

        $result = $this->controller->create($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(8, $result['id']);
        $this->assertStringContainsString('creado', $result['message']);
    }

    /**
     * @test
     * SAL-03 (parcial): create falla si falta id_torneo.
     */
    public function testCreateFallaSinIdTorneo(): void {
        $data = ['id_luchador_1' => 1, 'id_luchador_2' => 2];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Torneo requerido', $result['message']);
    }

    /**
     * @test
     * SAL-03 (parcial): create falla si falta id_luchador_1.
     */
    public function testCreateFallaSinLuchador1(): void {
        $data = ['id_torneo' => 2, 'id_luchador_2' => 3];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Luchador 1 requerido', $result['message']);
    }

    /**
     * @test
     * SAL-03 (parcial): create falla si falta id_luchador_2.
     */
    public function testCreateFallaSinLuchador2(): void {
        $data = ['id_torneo' => 2, 'id_luchador_1' => 1];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('Luchador 2 requerido', $result['message']);
    }

    /**
     * @test
     * create falla con datos completamente vacíos.
     */
    public function testCreateFallaConDatosVacios(): void {
        $result = $this->controller->create([]);

        $this->assertFalse($result['success']);
    }

    /**
     * @test
     * create maneja excepción del DAO.
     */
    public function testCreateManejaExcepcionDelDAO(): void {
        $this->daoMock->method('create')
            ->willThrowException(new Exception('Duplicate entry'));

        $data = ['id_torneo' => 2, 'id_luchador_1' => 1, 'id_luchador_2' => 3];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Duplicate entry', $result['message']);
    }

    // =======================================================================
    // GRUPO 4 — update()
    // FN-10: El sistema mantiene los brackets disponibles para modificación
    // =======================================================================

    /**
     * @test
     * FN-10: update exitoso cuando el combate existe.
     */
    public function testUpdateExitosoSiCombateExiste(): void {
        $combateExistente = ['id_combate' => 1, 'estado' => 'pendiente'];
        $this->daoMock->method('getById')->willReturn($combateExistente);
        $this->daoMock->method('update')->willReturn(true);

        $result = $this->controller->update(1, ['estado' => 'en_curso', 'id_torneo' => 2, 'id_luchador_1' => 1, 'id_luchador_2' => 3]);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('actualizado', $result['message']);
    }

    /**
     * @test
     * update falla si el combate no existe.
     */
    public function testUpdateFallaSiCombateNoExiste(): void {
        $this->daoMock->method('getById')->willReturn(null);

        $result = $this->controller->update(999, ['estado' => 'finalizado']);

        $this->assertFalse($result['success']);
        $this->assertEquals('Combate no encontrado', $result['message']);
    }

    // =======================================================================
    // GRUPO 5 — delete()
    // ALT-01: cancelación de brackets
    // =======================================================================

    /**
     * @test
     * ALT-01: delete exitoso cuando el combate existe.
     */
    public function testDeleteExitosoSiCombateExiste(): void {
        $combateExistente = ['id_combate' => 1, 'estado' => 'pendiente'];
        $this->daoMock->method('getById')->willReturn($combateExistente);
        $this->daoMock->method('delete')->willReturn(true);

        $result = $this->controller->delete(1);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('eliminado', $result['message']);
    }

    /**
     * @test
     * delete falla si el combate no existe.
     */
    public function testDeleteFallaSiCombateNoExiste(): void {
        $this->daoMock->method('getById')->willReturn(null);

        $result = $this->controller->delete(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Combate no encontrado', $result['message']);
    }

    // =======================================================================
    // GRUPO 6 — search()
    // =======================================================================

    /**
     * @test
     * search retorna resultados cuando encuentra coincidencias.
     */
    public function testSearchRetornaResultadosCuandoEncuentra(): void {
        $this->daoMock->method('search')->with('Final')->willReturn([
            ['id_combate' => 7, 'ronda' => 'Final', 'estado' => 'pendiente']
        ]);

        $result = $this->controller->search('Final');

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    /**
     * @test
     * search retorna array vacío cuando no hay coincidencias.
     */
    public function testSearchRetornaVacioCuandoNoEncuentra(): void {
        $this->daoMock->method('search')->with('XYZ')->willReturn([]);

        $result = $this->controller->search('XYZ');

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['data']);
    }

    // =======================================================================
    // GRUPO 7 — countAll()
    // =======================================================================

    /**
     * @test
     * countAll retorna el total correcto de combates.
     */
    public function testCountAllRetornaTotalCorrecto(): void {
        $this->daoMock->method('countAll')->willReturn(['total' => 7]);

        $result = $this->controller->countAll();

        $this->assertTrue($result['success']);
        $this->assertEquals(7, $result['data']['total']);
    }
}
