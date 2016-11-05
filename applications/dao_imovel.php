<?php

class dao_imovel {

    public static function pegar($id) {
        $db = new mysqlsearch();
        $db->table("imovel");
        $db->join("condominio", array("id_condominio", "=", "id"), "LEFT");
        $db->column("*", 1);
        $db->column("nome", 2, "nome_condominio");
        $db->column("endereco", 2, "endereco_condominio");
        $db->match("id", $id);
        $dao = $db->go();
        if (!empty($dao[0])) {
            return self::hook($dao[0]);
        }
        return false;
    }

    public static function listar($id_condominio = false, $bloco = false, $apartamento = false) {
        $array = false;
        $db = new mysqlsearch();
        $db->table("imovel");
        $db->join("condominio", array("id_condominio", "=", "id"), "LEFT");
        $db->join("morador", array("id", "=", "id_imovel", 1), "LEFT");
        $db->column("*", 1);
        $db->column("id", 1, "id_group_imovel");
        $db->column("nome", 2, "nome_condominio");
        $db->column("COUNT(`t3`.`id`)", false, "total_moradores");

        if ($id_condominio) {
            $db->match("id_condominio", $id_condominio);
        }

        if ($bloco) {
            $db->match("bloco", $bloco);
        }

        if ($apartamento) {
            $db->match("apartamento", $apartamento);
        }

        $db->group(2);
        $db->order("bloco");
        $db->order("apartamento");
        $dao = $db->go();
        if ($dao) {
            foreach ($dao as $row) {
                $array[$row["id"]] = self::hook($row);
            }
        }
        return $array;
    }

    private static function hook($row) {
        $row["imovel"] = "{$row["bloco"]} / {$row["apartamento"]}";
        return $row;
    }

    public static function cadastrar($bloco, $apartamento, $senha, $status, $id_condominio) {
        if (!self::verificar($id_condominio, $bloco, $apartamento)) {
            $db = new mysqlsave();
            $db->table("imovel");
            $db->column("bloco", $bloco);
            $db->column("apartamento", $apartamento);
            $db->column("senha", md5($senha));
            $db->column("status", $status);
            $db->column("id_condominio", $id_condominio);
            if ($db->go()) {
                return $db->id();
            }
        }
        return false;
    }

    public static function atualizar($id, $senha, $status) {
        $db = new mysqlsave();
        $db->table("imovel");
        if ($senha) {
            $db->column("senha", md5($senha));
        }
        $db->column("status", $status);
        $db->match("id", $id);
        return $db->go();
    }

    public static function remover($id) {
        $db = new mysqldelete();
        $db->table("imovel");
        $db->match("id", $id);
        if ($db->go()) {
            return true;
        }
        return false;
    }

    public static function verificar($id_condominio, $bloco, $apartamento) {
        $db = new mysqlsearch();
        $db->table("imovel");
        $db->column("id");
        $db->match("id_condominio", $id_condominio);
        $db->match("bloco", $bloco);
        $db->match("apartamento", $apartamento);
//        if ($id) {
//            $db->match("id", $id, "AND", true);
//        }
        $dao = $db->go();
        if (!empty($dao[0])) {
            return true;
        }
        return false;
    }

}