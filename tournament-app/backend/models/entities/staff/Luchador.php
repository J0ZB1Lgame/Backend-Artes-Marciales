<?php

class Luchador {
    private ?int $idLuchador;
    private string $nombre;
    private ?string $apellido;
    private ?string $tipoDocumento;
    private ?string $numeroDocumento;
    private ?int $edad;
    private ?string $genero;
    private ?string $categoria;
    private ?float $peso;
    private ?string $telefono;
    private ?string $email;
    private int $victorias;
    private int $derrotas;
    private string $estado;
    private ?string $foto;
    private ?DateTime $fechaRegistro;

    public function __construct(
        ?int $idLuchador = null,
        string $nombre = '',
        ?string $apellido = null,
        ?string $tipoDocumento = null,
        ?string $numeroDocumento = null,
        ?int $edad = null,
        ?string $genero = null,
        ?string $categoria = null,
        ?float $peso = null,
        ?string $telefono = null,
        ?string $email = null,
        int $victorias = 0,
        int $derrotas = 0,
        string $estado = 'activo',
        ?string $foto = null,
        ?DateTime $fechaRegistro = null
    ) {
        $this->idLuchador = $idLuchador;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipoDocumento = $tipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->edad = $edad;
        $this->genero = $genero;
        $this->categoria = $categoria;
        $this->peso = $peso;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->victorias = $victorias;
        $this->derrotas = $derrotas;
        $this->estado = $estado;
        $this->foto = $foto;
        $this->fechaRegistro = $fechaRegistro;
    }

    // Getters
    public function getIdLuchador(): ?int { return $this->idLuchador; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellido(): ?string { return $this->apellido; }
    public function getTipoDocumento(): ?string { return $this->tipoDocumento; }
    public function getNumeroDocumento(): ?string { return $this->numeroDocumento; }
    public function getEdad(): ?int { return $this->edad; }
    public function getGenero(): ?string { return $this->genero; }
    public function getCategoria(): ?string { return $this->categoria; }
    public function getPeso(): ?float { return $this->peso; }
    public function getTelefono(): ?string { return $this->telefono; }
    public function getEmail(): ?string { return $this->email; }
    public function getVictorias(): int { return $this->victorias; }
    public function getDerrotas(): int { return $this->derrotas; }
    public function getEstado(): string { return $this->estado; }
    public function getFoto(): ?string { return $this->foto; }
    public function getFechaRegistro(): ?DateTime { return $this->fechaRegistro; }

    // Setters
    public function setIdLuchador(int $idLuchador): void { $this->idLuchador = $idLuchador; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellido(?string $apellido): void { $this->apellido = $apellido; }
    public function setTipoDocumento(?string $tipoDocumento): void { $this->tipoDocumento = $tipoDocumento; }
    public function setNumeroDocumento(?string $numeroDocumento): void { $this->numeroDocumento = $numeroDocumento; }
    public function setEdad(?int $edad): void { $this->edad = $edad; }
    public function setGenero(?string $genero): void { $this->genero = $genero; }
    public function setCategoria(?string $categoria): void { $this->categoria = $categoria; }
    public function setPeso(?float $peso): void { $this->peso = $peso; }
    public function setTelefono(?string $telefono): void { $this->telefono = $telefono; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setVictorias(int $victorias): void { $this->victorias = $victorias; }
    public function setDerrotas(int $derrotas): void { $this->derrotas = $derrotas; }
    public function setEstado(string $estado): void { $this->estado = $estado; }
    public function setFoto(?string $foto): void { $this->foto = $foto; }
    public function setFechaRegistro(?DateTime $fechaRegistro): void { $this->fechaRegistro = $fechaRegistro; }

    // Métodos adicionales
    public function getNombreCompleto(): string {
        return trim($this->nombre . ' ' . ($this->apellido ?? ''));
    }

    public function getRendimiento(): float {
        $totalCombates = $this->victorias + $this->derrotas;
        return $totalCombates > 0 ? ($this->victorias / $totalCombates) * 100 : 0;
    }

    public function toArray(): array {
        return [
            'id_luchador' => $this->idLuchador,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'tipo_documento' => $this->tipoDocumento,
            'numero_documento' => $this->numeroDocumento,
            'edad' => $this->edad,
            'genero' => $this->genero,
            'categoria' => $this->categoria,
            'peso' => $this->peso,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'victorias' => $this->victorias,
            'derrotas' => $this->derrotas,
            'estado' => $this->estado,
            'foto' => $this->foto,
            'fecha_registro' => $this->fechaRegistro ? $this->fechaRegistro->format('Y-m-d H:i:s') : null
        ];
    }
}

?>