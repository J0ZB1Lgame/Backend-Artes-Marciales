<?php

if (!interface_exists('ICRUD')) {
    interface ICRUD {
        public function crear($entidad);
        public function obtenerPorId($id);
        public function actualizar($entidad);
        public function eliminarPorId($id);
    }
}

?>