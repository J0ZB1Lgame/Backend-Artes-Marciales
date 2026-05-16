<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/*
|--------------------------------------------------------------------------
| PRUEBAS UNITARIAS — StaffTorneoController
| Módulo 2 - Gestión del Staff
| Fernando Ubaque
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../backend/api/controllers/staff/StaffTorneoController.php';

class StaffTorneoControllerTest extends TestCase {

    private StaffTorneoController $controller;
    private MockObject $daoMock;

    protected function setUp(): void {
        // Creamos un mock del DAO para aislar el Controller de la BD
        $this->daoMock = $this->createMock(StaffTorneoDAOImpl::class);
        
        // Instanciamos el Controller sin ejecutar su constructor para evitar conexión a BD
        $reflection = new ReflectionClass(StaffTorneoController::class);
        $this->controller = $reflection->newInstanceWithoutConstructor();

        // Inyectamos el mock del DAO via reflexión
        $prop = $reflection->getProperty('staffTorneoDAO');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $this->daoMock);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — getAll()
    |--------------------------------------------------------------------------
    */

    public function testGetAllRetornaSuccessTrue(): void {
        $this->daoMock->method('getAll')->willReturn([
            ['id_staff' => 1, 'nombre' => 'Carlos', 'apellido' => 'López'],
            ['id_staff' => 2, 'nombre' => 'Ana',    'apellido' => 'Gómez'],
        ]);

        $result = $this->controller->getAll();

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']);
    }

    public function testGetAllRetornaArregloVacioCuandoNoHayStaff(): void {
        $this->daoMock->method('getAll')->willReturn([]);

        $result = $this->controller->getAll();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(0, $result['data']);
    }

    public function testGetAllRetornaSuccessFalseAnteExcepcion(): void {
        $this->daoMock->method('getAll')
            ->willThrowException(new Exception('Error de conexión'));

        $result = $this->controller->getAll();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error de conexión', $result['message']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — getById()
    |--------------------------------------------------------------------------
    */

    public function testGetByIdRetornaMiembroCuandoExiste(): void {
        $staffEsperado = ['id_staff' => 1, 'nombre' => 'Carlos', 'apellido' => 'López'];
        $this->daoMock->method('getById')->with(1)->willReturn($staffEsperado);

        $result = $this->controller->getById(1);

        $this->assertTrue($result['success']);
        $this->assertEquals('Carlos', $result['data']['nombre']);
    }

    public function testGetByIdRetornaFalseCuandoNoExiste(): void {
        $this->daoMock->method('getById')->with(999)->willReturn(null);

        $result = $this->controller->getById(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Miembro no encontrado', $result['message']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — create()
    |--------------------------------------------------------------------------
    */

    public function testCreateExitosoConDatosValidos(): void {
        $this->daoMock->method('create')->willReturn(5);

        $data = [
            'nombre'   => 'Luis',
            'apellido' => 'Martínez',
            'email'    => 'luis@test.com',
        ];

        $result = $this->controller->create($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(5, $result['id']);
        $this->assertStringContainsString('creado', $result['message']);
    }

    public function testCreateFallaSinNombre(): void {
        $data = ['apellido' => 'Martínez'];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('El nombre es obligatorio', $result['message']);
    }

    public function testCreateFallaSinApellido(): void {
        $data = ['nombre' => 'Luis'];

        $result = $this->controller->create($data);

        $this->assertFalse($result['success']);
        $this->assertEquals('El apellido es obligatorio', $result['message']);
    }

    public function testCreateFallaSinDatos(): void {
        $result = $this->controller->create([]);

        $this->assertFalse($result['success']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — update()
    |--------------------------------------------------------------------------
    */

    public function testUpdateExitosoSiMiembroExiste(): void {
        $staffExistente = ['id_staff' => 1, 'nombre' => 'Carlos'];
        $this->daoMock->method('getById')->willReturn($staffExistente);
        $this->daoMock->method('update')->willReturn(true);

        $result = $this->controller->update(1, ['nombre' => 'Carlos Actualizado', 'apellido' => 'López']);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('actualizado', $result['message']);
    }

    public function testUpdateFallaSiMiembroNoExiste(): void {
        $this->daoMock->method('getById')->willReturn(null);

        $result = $this->controller->update(999, ['nombre' => 'Fantasma']);

        $this->assertFalse($result['success']);
        $this->assertEquals('Miembro no encontrado', $result['message']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — delete()
    |--------------------------------------------------------------------------
    */

    public function testDeleteExitosoSiMiembroExiste(): void {
        $staffExistente = ['id_staff' => 1, 'nombre' => 'Carlos'];
        $this->daoMock->method('getById')->willReturn($staffExistente);
        $this->daoMock->method('delete')->willReturn(true);

        $result = $this->controller->delete(1);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('eliminado', $result['message']);
    }

    public function testDeleteFallaSiMiembroNoExiste(): void {
        $this->daoMock->method('getById')->willReturn(null);

        $result = $this->controller->delete(999);

        $this->assertFalse($result['success']);
        $this->assertEquals('Miembro no encontrado', $result['message']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — search()
    |--------------------------------------------------------------------------
    */

    public function testSearchRetornaResultadosCuandoEncuentra(): void {
        $this->daoMock->method('search')->with('Carlos')->willReturn([
            ['id_staff' => 1, 'nombre' => 'Carlos', 'apellido' => 'López']
        ]);

        $result = $this->controller->search('Carlos');

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function testSearchRetornaVacioCuandoNoEncuentra(): void {
        $this->daoMock->method('search')->with('XYZ')->willReturn([]);

        $result = $this->controller->search('XYZ');

        $this->assertTrue($result['success']);
        $this->assertCount(0, $result['data']);
    }

    /*
    |--------------------------------------------------------------------------
    | PRUEBAS — countAll()
    |--------------------------------------------------------------------------
    */

    public function testCountAllRetornaTotalCorrecto(): void {
        $this->daoMock->method('countAll')->willReturn(['total' => 10]);

        $result = $this->controller->countAll();

        $this->assertTrue($result['success']);
        $this->assertEquals(10, $result['data']['total']);
    }
}