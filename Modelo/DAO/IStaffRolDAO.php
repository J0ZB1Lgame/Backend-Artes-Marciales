<?php
interface IStaffRolDAO {
    public function asignar($id_staff, $id_tipo_rol);
    public function revocar($id_staff, $id_tipo_rol);
    public function listarRolesPorStaff($id_staff);
    public function listarTodos();
}
?>