# Diagrama de Clases - TAM
> Generado desde Diagrama_de_Clases_TAM.drawio para uso en IA de VS Code

---

## ENTIDADES DEL DOMINIO

### Luchador
- **Atributos:**
  - -idLuchador: int
  - -nombre: String
  - -especie: String
  - -nivelDePoderKi: float
  - -tecnicaEspecial[]: TecnicaEspecial
  - -origen: String
  - -evaluacionMedica: EvaluacionMedica
  - -documentos[]: DocumentoLegal
  - -combates: HistorialCombate
  - -estado: boolean
- **Métodos:**
  - +asignarEvaluacionMedica(evaluacion: EvaluacionMedica): void
  - +asignarTecnicaEspecial(tecnica: TecnicaEspecial): void
  - +verEstado(): boolean
  - +registrarLuchador(): void
  - +consultarNivelDeKi(): float
  - +consultarEvaluacionMedica(): void
  - +consultarHistorialCombates(): HistorialCombates

---

### EvaluacionMedica
- **Atributos:**
  - +id: int
  - +frecuenciaCardiaca: float
  - +presionArterial: string
  - +nivelKi: float
  - +estadoApto: boolean
- **Métodos:**
  - +registrarEvaluacion(): void
  - +actualizarEvaluacion(): void
  - +verificarAptitud(): boolean

---

### TecnicaEspecial
- **Atributos:**
  - -idTecnicaEspecial: int
  - -tecnica: String
  - -detalle: String
- **Métodos:**
  - +crearTecnica(): void
  - +actualizarTecnica(): void
  - +eliminarTecnica(): void

---

### DocumentoLegal
- **Atributos:**
  - +id: int
  - +tipo: string
  - +estado: string
  - +version: string
- **Métodos:**
  - +cargarDocumento(): void
  - +actualizarEstado(): void
  - +consultarDocumento(): DocumentoLegal
  - +verificarVigencia(): boolean

---

### Combate
- **Atributos:**
  - -idCombate: int
  - -fechaCombate: String
  - -horaCombate: String
  - -luchador1: Luchador
  - -luchador2: Luchador
  - -arena: Arena
  - -resultado: ResultadoCombate
  - -staffCombate[]: StaffCombate
  - -estado: boolean
- **Métodos:**
  - +asignarArena(arena: Arena): void
  - +asignarResultado(resultado: ResultadoCombate): void
  - +asignarStaff(staffCombate: StaffCombate): void
  - +asignarLuchadores(luchador1: Luchador, luchador2: Luchador): void
  - +iniciarCombate(): void
  - +finalizarCombate(): void

---

### ResultadoCombate
- **Atributos:**
  - -idResultadoCombate: int
  - -duracion: int
  - -observaciones: String
  - -tecnicaFinal: TecnicaEspecial
  - -ganador: Luchador
- **Métodos:**
  - +asignarTecnicaFinal(tecnicaFinal: TecnicaEspecial): void
  - +asignarGanador(ganador: Luchador): void
  - +registrarResultado(): void
  - +actualizarResultado(): void
  - +consultarResultado(): void

---

### HistorialCombates
- **Atributos:**
  - -idHistorialCombates: int
  - -combates[]: Combate
  - -observaciones: String
- **Métodos:**
  - +crearHistorial(combate: Combate): void

---

### Arena
- **Atributos:**
  - -idArena: int
  - -nombre: String
  - -capacidad: int
  - -barreraEnergetica: BarreraEnergetica
  - -estado: boolean
- **Métodos:**
  - +asignarIncidente(incidente: IncidenteSeguridad): void
  - +verEstado(): boolean
  - +crearArena(): void
  - +consultarCapacidad(): int

---

### BarreraEnergetica
- **Atributos:**
  - -idBarrera: int
  - -nivelActual: int
  - -capacidadMaxima: int
  - -estado: boolean
- **Métodos:**
  - +monitorear(): void
  - +activarBarrera(): void
  - +desactivarBarrera(): void
  - +consultarNivel(): int
  - +crearAlerta(): void

---

### IncidenteSeguridad
- **Atributos:**
  - -idIncidente: int
  - -tipoIncidente: String
  - -descripcion: String
  - -fechaIncidente: String
  - -estado: String
- **Métodos:**
  - +registrarIncidente(): void
  - +actualizarIncidente(): void
  - +eliminarIncidente(): int

---

### Torneo
- **Atributos:**
  - -idTorneo: int
  - -nombre: String
  - -estado: boolean
  - -reglas: ReglasTorneo
  - -cronograma: Cronograma
  - -inventarioActivos: InventarioActivosTech
- **Métodos:** getters/setters

---

### Bracket
- **Atributos:**
  - -idBracket: int
  - -rondas: int
- **Métodos:** getters/setters

---

### Cronograma
- **Atributos:**
  - -idCronograma: int
  - -fechaEvento: String
- **Métodos:** getters/setters

---

### ReglasTorneo
- **Atributos:**
  - -idRegla: int
  - -tiempoLimite: int
  - -fechaInicio: String
  - -fechaFin: String
  - -estado: boolean
- **Métodos:** getters/setters

---

### Modalidades
- **Atributos:**
  - -idModalidad: int
  - -modoJuego: String
  - -estado: boolean
- **Métodos:** getters/setters

---

### TableroActividad
- **Atributos:**
  - -idTablero: int
  - -fecha: String
  - -fechaFin: String
  - -estado: boolean
- **Métodos:**
  - +actualizarTablero(): void
  - +iniciarTransmisión(): void

---

### ActivoTecnologico
- **Atributos:**
  - -idActivo: int
  - -nombre: String
  - -descripcion: String
  - -tipo: String
  - -ubicacion: String
  - -fechaRegistro: String
  - -estado: boolean
- **Métodos:**
  - +crearActivo(): void
  - +actualizarEstado(): void
  - +consultarActivo(): void

---

### InventarioActivosTech
- **Atributos:**
  - -idActivo: int
  - -fechaActualizacion: String
  - -activos[]: ActivosTecnologicos
  - -estado: boolean
- **Métodos:**
  - +registrarActivoAInventario(): void
  - +retirarActivoDeInventario(): void
  - +buscarPorTipo(): void
  - +actualizarEstado(): void
  - +consultarInventario(): void
  - +generarReporteInventario(): void

---

## SEGURIDAD / USUARIOS

### <<abstract>> Staff
- **Atributos:**
  - +id: int
  - +nombre: string
  - +turno: String
  - +usuario: Usuario
  - +estado: boolean
  - +cargo: boolean
- **Métodos:**
  - +registrarMiembro(): void
  - +asignarTurno(): void
  - +modificarTurno(): void

### StaffCombates (extiende Staff)
- **Atributos:**
  - -combate: Combate
  - -estado: string

### StaffTorneo (extiende Staff)

---

### Usuario
- **Atributos:**
  - -id: int
  - -username: string
  - -password: string
  - -estado: string
  - -sesiones[]: Sesion
- **Métodos:**
  - +actualizarDatos(): void
  - +cambiarPassword(): void

### RolUsuario
- **Atributos:**
  - +id: int
  - +nombre: string
  - +descripcion: string

### Permiso
- **Atributos:**
  - +id: int
  - +nombre: string
- **Métodos:**
  - +crearPermiso(): void

### Sesion
- **Atributos:**
  - -idSesion: int
  - -fechaInicio: String
  - -fechaFin: String
  - -estado: boolean
- **Métodos:**
  - +iniciarSesion(username, password): void
  - +cerrarSesion(): void

### Log
- **Atributos:**
  - -id: int
  - -accion: string
  - -fecha: datetime
- **Métodos:**
  - +registrarEvento(): void
  - +consultarLog(): void

---

## ALERTAS

### <<abstract>> Alerta
- **Atributos:**
  - -idAlerta: int
  - -fechaAlerta: String
  - -horaAlerta: String
  - -descripcion: String
  - -estado: boolean
- **Métodos:**
  - +activarAlerta(): void
  - +resolverAlerta(): void

### AlertaBarrera (extiende Alerta)
- **Atributos:**
  - -idBarrera: int
  - -nivelActual: int
- **Métodos:**
  - +notificar(): void

### AlertaAccesoFallido (extiende Alerta)
- **Atributos:**
  - -idUsuario: int
  - -intentosFallidos: int
- **Métodos:**
  - +bloquearAcceso(): void

---

## CAPA DE PERSISTENCIA (DAOs)

### Interface genérica
```
<<interface>> ICRUD<T>
  +crear(objeto: T): void
  +obtenerPorId(id: int): T
  +actualizar(objeto: T): void
  +eliminarPorId(id: int): void
```

### DAOs por entidad (interface + implementación)

| Interfaz                  | Implementación              |
|---------------------------|-----------------------------|
| ILuchadorDAO              | LuchadorDAOImpl             |
| ICombateDAO               | CombateDAOImpl              |
| IResultadoCombateDAO      | ResultadoCombateDAOImpl     |
| IHistorialCombatesDAO     | HistorialCombatesDAOImpl    |
| IEvaluacionMedicaDAO      | EvaluacionMedicaDAOImpl     |
| ITecnicaEspecialDAO       | TecnicaEspecialDAOImpl      |
| IDocumentoLegalDAO        | DocumentoLegalDAOImpl       |
| ITorneoDAO                | TorneoDAOImpl               |
| IBracketDAO               | BracketDAOImpl              |
| ICronogramaDAO            | CronogramaDAOImpl           |
| IReglasTorneoDAO          | ReglasTorneoDAOImpl         |
| IModalidadesDAO           | ModalidadesDAOImpl          |
| IStaffTorneoDAO           | StaffTorneoDAOImpl          |
| IUsuarioDAO               | IUsuarioDAOImpl             |

Todos los DAOImpl tienen:
- -connection: Connection
- Implementan los 4 métodos CRUD de su interfaz correspondiente

---

## CONTROLLERS

| Controller              | Métodos principales                                                                 |
|-------------------------|-------------------------------------------------------------------------------------|
| TorneoController        | inscribirLuchador, asignarActivos, asignarCronograma, asignarStaff, asignarModalidades, iniciarTorneo, finalizarTorneo |
| BracketController       | asignarCombate(combate: Combate)                                                    |
| ModalidadesController   | establecerReglasTorneo(reglasTorneo: ReglasTorneo)                                 |
| CronogramaController    | —                                                                                   |
| ReglasTorneoController  | —                                                                                   |
| StaffTorneoController   | —                                                                                   |

---

## RELACIONES CLAVE

- **Luchador** tiene una **EvaluacionMedica** (1:1)
- **Luchador** tiene muchas **TecnicaEspecial** (1:N)
- **Luchador** tiene muchos **DocumentoLegal** (1:N)
- **Luchador** tiene un **HistorialCombates** (1:1)
- **Combate** tiene dos **Luchador** (luchador1, luchador2)
- **Combate** ocurre en una **Arena** (1:1)
- **Combate** genera un **ResultadoCombate** (1:1)
- **Arena** tiene una **BarreraEnergetica** (1:1)
- **BarreraEnergetica** genera **AlertaBarrera**
- **Torneo** contiene **Bracket**, **Cronograma**, **ReglasTorneo**, **InventarioActivosTech**
- **Cronograma** contiene **Bracket**
- **RolUsuario** contiene muchos **Permiso**
- **Usuario** tiene muchas **Sesion**
- **Staff** (abstracta) es extendida por **StaffCombates** y **StaffTorneo**
- **Alerta** (abstracta) es extendida por **AlertaBarrera** y **AlertaAccesoFallido**
- Todos los DAOImpl implementan su interfaz DAO correspondiente
