<?php
interface ICRUD {
    public function crear($objeto);
    public function obtenerPorId($id);
    public function actualizar($objeto);
    public function eliminarPorId($id);
}
?>