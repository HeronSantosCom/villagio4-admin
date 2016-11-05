<?php

class dao_condominio {

    public static function pegar($id) {
        $db = new mysqlsearch();
        $db->table("condominio");
        $db->column("*");
        $db->match("id", $id);
        $dao = $db->go();
        if (!empty($dao[0])) {
            return self::hook($dao[0]);
        }
        return false;
    }

    public static function listar() {
        $array = false;
        $db = new mysqlsearch();
        $db->table("condominio");
        $db->column("*");
        $db->order("nome");
        $dao = $db->go();
        if ($dao) {
            foreach ($dao as $row) {
                $array[$row["id"]] = self::hook($row);
            }
        }
        return $array;
    }

    private static function hook($row) {
        return $row;
    }

    public static function cadastrar($nome, $endereco, $status) {
        $db = new mysqlsave();
        $db->table("condominio");
        $db->column("nome", $nome);
        $db->column("endereco", $endereco);
        $db->column("status", $status);
        if ($db->go()) {
            return $db->id();
        }
        return false;
    }

    public static function atualizar($id, $nome, $endereco, $status) {
        $db = new mysqlsave();
        $db->table("condominio");
        $db->column("nome", $nome);
        $db->column("endereco", $endereco);
        $db->column("status", $status);
        $db->match("id", $id);
        return $db->go();
    }

    public static function remover($id) {
        $db = new mysqldelete();
        $db->table("condominio");
        $db->match("id", $id);
        if (self::remover_relacionados) {
            if ($db->go()) {
                return true;
            }
        }
        return false;
    }

}