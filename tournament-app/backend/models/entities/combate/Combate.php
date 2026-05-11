<?php

class Combate {
    private ?int $idCombate;
    private ?int $idTorneo;
    private ?int $idLuchador1;
    private ?int $idLuchador2;
    private ?int $ganadorId;
    private string $estado;
    private ?string $ronda;
    private ?DateTime $fechaCombate;
    private ?string $arena;
    private ?string $observaciones;
    private ?int $duracionSegundos;
    private int $puntosLuchador1;
    private int $puntosLuchador2;

    public function __construct(
        ?int $idCombate = null,
        ?int $idTorneo = null,
        ?int $idLuchador1 = null,
        ?int $idLuchador2 = null,
        ?int $ganadorId = null,
        string $estado = 'pendiente',
        ?string $ronda = null,
        ?DateTime $fechaCombate = null,
        ?string $arena = null,
        ?string $observaciones = null,
        ?int $duracionSegundos = null,
        int $puntosLuchador1 = 0,
        int $puntosLuchador2 = 0
    ) {
        $this->idCombate = $idCombate;
        $this->idTorneo = $idTorneo;
        $this->idLuchador1 = $idLuchador1;
        $this->idLuchador2 = $idLuchador2;
        $this->ganadorId = $ganadorId;
        $this->estado = $estado;
        $this->ronda = $ronda;
        $this->fechaCombate = $fechaCombate;
        $this->arena = $arena;
        $this->observaciones = $observaciones;
        $this->duracionSegundos = $duracionSegundos;
        $this->puntosLuchador1 = $puntosLuchador1;
        $this->puntosLuchador2 = $puntosLuchador2;
    }

    // Getters
    public function getIdCombate(): ?int { return $this->idCombate; }
    public function getIdTorneo(): ?int { return $this->idTorneo; }
    public function getIdLuchador1(): ?int { return $this->idLuchador1; }
    public function getIdLuchador2(): ?int { return $this->idLuchador2; }
    public function getGanadorId(): ?int { return $this->ganadorId; }
    public function getEstado(): string { return $this->estado; }
    public function getRonda(): ?string { return $this->ronda; }
    public function getFechaCombate(): ?DateTime { return $this->fechaCombate; }
    public function getArena(): ?string { return $this->arena; }
    public function getObservaciones(): ?string { return $this->observaciones; }
    public function getDuracionSegundos(): ?int { return $this->duracionSegundos; }
    public function getPuntosLuchador1(): int { return $this->puntosLuchador1; }
    public function getPuntosLuchador2(): int { return $this->puntosLuchador2; }

    // Setters
    public function setIdCombate(int $idCombate): void { $this->idCombate = $idCombate; }
    public function setIdTorneo(?int $idTorneo): void { $this->idTorneo = $idTorneo; }
    public function setIdLuchador1(?int $idLuchador1): void { $this->idLuchador1 = $idLuchador1; }
    public function setIdLuchador2(?int $idLuchador2): void { $this->idLuchador2 = $idLuchador2; }
    public function setGanadorId(?int $ganadorId): void { $this->ganadorId = $ganadorId; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
    public function setRonda(?string $ronda): void { $this->ronda = $ronda; }
    public function setFechaCombate(?DateTime $fechaCombate): void { $this->fechaCombate = $fechaCombate; }
    public function setArena(?string $arena): void { $this->arena = $arena; }
    public function setObservaciones(?string $observaciones): void { $this->observaciones = $observaciones; }
    public function setDuracionSegundos(?int $duracionSegundos): void { $this->duracionSegundos = $duracionSegundos; }
    public function setPuntosLuchador1(int $puntosLuchador1): void { $this->puntosLuchador1 = $puntosLuchador1; }
    public function setPuntosLuchador2(int $puntosLuchador2): void { $this->puntosLuchador2 = $puntosLuchador2; }

    // Métodos adicionales
    public function getNombreEstado(): string {
        $estados = [
            'pendiente' => 'Pendiente',
            'en_curso' => 'En Curso',
            'finalizado' => 'Finalizado',
            'suspendido' => 'Suspendido',
            'cancelado' => 'Cancelado'
        ];
        return $estados[$this->estado] ?? $this->estado;
    }

    public function getDuracionFormateada(): string {
        if ($this->duracionSegundos === null) return 'N/A';
        
        $minutos = floor($this->duracionSegundos / 60);
        $segundos = $this->duracionSegundos % 60;
        
        if ($minutos > 0) {
            return sprintf("%d:%02d", $minutos, $segundos);
        }
        
        return sprintf("0:%02d", $segundos);
    }

    public function getResultado(): string {
        if ($this->ganadorId === null) return 'Sin definir';
        if ($this->ganadorId === $this->idLuchador1) return 'Luchador 1';
        if ($this->ganadorId === $this->idLuchador2) return 'Luchador 2';
        return 'Empate';
    }

    public function toArray(): array {
        return [
            'id_combate' => $this->idCombate,
            'id_torneo' => $this->idTorneo,
            'id_luchador_1' => $this->idLuchador1,
            'id_luchador_2' => $this->idLuchador2,
            'ganador_id' => $this->ganadorId,
            'estado' => $this->estado,
            'ronda' => $this->ronda,
            'fecha_combate' => $this->fechaCombate ? $this->fechaCombate->format('Y-m-d H:i:s') : null,
            'arena' => $this->arena,
            'observaciones' => $this->observaciones,
            'duracion_segundos' => $this->duracionSegundos,
            'puntos_luchador_1' => $this->puntosLuchador1,
            'puntos_luchador_2' => $this->puntosLuchador2,
            'nombre_estado' => $this->getNombreEstado(),
            'duracion_formateada' => $this->getDuracionFormateada(),
            'resultado' => $this->getResultado()
        ];
    }
}

?>
