<?php

class Torneo {
    private ?int $idTorneo;
    private string $nombre;
    private ?string $descripcion;
    private ?DateTime $fechaInicio;
    private ?DateTime $fechaFin;
    private string $estado;
    private ?string $ubicacion;
    private ?string $tipo;
    private ?string $premio;
    private ?string $logo;
    private ?int $capacidadMaxima;
    private ?string $reglas;

    public function __construct(
        ?int $idTorneo = null,
        string $nombre = '',
        ?string $descripcion = null,
        ?DateTime $fechaInicio = null,
        ?DateTime $fechaFin = null,
        string $estado = 'activo',
        ?string $ubicacion = null,
        ?string $tipo = null,
        ?string $premio = null,
        ?string $logo = null,
        ?int $capacidadMaxima = null,
        ?string $reglas = null
    ) {
        $this->idTorneo = $idTorneo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->estado = $estado;
        $this->ubicacion = $ubicacion;
        $this->tipo = $tipo;
        $this->premio = $premio;
        $this->logo = $logo;
        $this->capacidadMaxima = $capacidadMaxima;
        $this->reglas = $reglas;
    }

    // Getters
    public function getIdTorneo(): ?int { return $this->idTorneo; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getFechaInicio(): ?DateTime { return $this->fechaInicio; }
    public function getFechaFin(): ?DateTime { return $this->fechaFin; }
    public function getEstado(): string { return $this->estado; }
    public function getUbicacion(): ?string { return $this->ubicacion; }
    public function getTipo(): ?string { return $this->tipo; }
    public function getPremio(): ?string { return $this->premio; }
    public function getLogo(): ?string { return $this->logo; }
    public function getCapacidadMaxima(): ?int { return $this->capacidadMaxima; }
    public function getReglas(): ?string { return $this->reglas; }

    // Setters
    public function setIdTorneo(int $idTorneo): void { $this->idTorneo = $idTorneo; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(?string $descripcion): void { $this->descripcion = $descripcion; }
    public function setFechaInicio(?DateTime $fechaInicio): void { $this->fechaInicio = $fechaInicio; }
    public function setFechaFin(?DateTime $fechaFin): void { $this->fechaFin = $fechaFin; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
    public function setUbicacion(?string $ubicacion): void { $this->ubicacion = $ubicacion; }
    public function setTipo(?string $tipo): void { $this->tipo = $tipo; }
    public function setPremio(?string $premio): void { $this->premio = $premio; }
    public function setLogo(?string $logo): void { $this->logo = $logo; }
    public function setCapacidadMaxima(?int $capacidadMaxima): void { $this->capacidadMaxima = $capacidadMaxima; }
    public function setReglas(?string $reglas): void { $this->reglas = $reglas; }

    // Métodos adicionales
    public function getNombreEstado(): string {
        $estados = [
            'activo' => 'Activo',
            'pendiente' => 'Pendiente',
            'en_curso' => 'En Curso',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado',
            'suspendido' => 'Suspendido'
        ];
        return $estados[$this->estado] ?? $this->estado;
    }

    public function getDuracion(): string {
        if ($this->fechaInicio === null || $this->fechaFin === null) {
            return 'N/A';
        }

        $intervalo = $this->fechaInicio->diff($this->fechaFin);
        $dias = $intervalo->days;
        $horas = $intervalo->h;
        $minutos = $intervalo->i;

        if ($dias > 0) {
            return sprintf("%dd %dh %dm", $dias, $horas, $minutos);
        } elseif ($horas > 0) {
            return sprintf("%dh %dm", $horas, $minutos);
        } else {
            return sprintf("%dm", $minutos);
        }
    }

    public function estaActivo(): bool {
        return $this->estado === 'activo' || $this->estado === 'en_curso';
    }

    public function estaFinalizado(): bool {
        return $this->estado === 'finalizado';
    }

    public function getFechaInicioFormateada(): string {
        return $this->fechaInicio ? $this->fechaInicio->format('d/m/Y H:i') : 'N/A';
    }

    public function getFechaFinFormateada(): string {
        return $this->fechaFin ? $this->fechaFin->format('d/m/Y H:i') : 'N/A';
    }

    public function toArray(): array {
        return [
            'id_torneo' => $this->idTorneo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fechaInicio ? $this->fechaInicio->format('Y-m-d H:i:s') : null,
            'fecha_fin' => $this->fechaFin ? $this->fechaFin->format('Y-m-d H:i:s') : null,
            'estado' => $this->estado,
            'ubicacion' => $this->ubicacion,
            'tipo' => $this->tipo,
            'premio' => $this->premio,
            'logo' => $this->logo,
            'capacidad_maxima' => $this->capacidadMaxima,
            'reglas' => $this->reglas,
            'nombre_estado' => $this->getNombreEstado(),
            'duracion' => $this->getDuracion(),
            'fecha_inicio_formateada' => $this->getFechaInicioFormateada(),
            'fecha_fin_formateada' => $this->getFechaFinFormateada(),
            'esta_activo' => $this->estaActivo(),
            'esta_finalizado' => $this->estaFinalizado()
        ];
    }
}

?>
