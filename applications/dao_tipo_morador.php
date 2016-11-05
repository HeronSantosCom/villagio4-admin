<?php

class dao_tipo_morador {

    public static function pegar($id) {
        $db = new mysqlsearch();
        $db->table("tipo_morador");
        $db->column("*");
        $db->match("id", $id);
        $dao = $db->go();
        if ($dao) {
            return $dao[0];
        }
        return false;
    }

    public static function listar() {
        $db = new mysqlsearch();
        $db->table("tipo_morador");
        $db->column("*");
        $db->order("nome");
        $dao = $db->go();
        if ($dao) {
            return $dao;
        }
        return false;
    }

}