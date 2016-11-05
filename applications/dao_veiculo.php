<?php

class dao_veiculo {

    public static function pegar($id) {
        $db = new mysqlsearch();
        $db->table("veiculo");
        $db->join("imovel", array("id_imovel", "=", "id"), "LEFT");
        $db->join("condominio", array("id_condominio", "=", "id", 2), "LEFT");
        $db->join("tipo_veiculo", array("id_tipo_veiculo", "=", "id", 1), "LEFT");
        $db->column("*", 1);
        $db->column("bloco", 2, "bloco_imovel");
        $db->column("apartamento", 2, "apartamento_imovel");
        $db->column("id", 3, "id_condominio");
        $db->column("nome", 3, "nome_condominio");
        $db->column("nome", 4, "nome_tipo_veiculo");
        $db->match("id", $id);
        $dao = $db->go();
        if (!empty($dao[0])) {
            return self::hook($dao[0]);
        }
        return false;
    }

    public static function listar($id_imovel = false, $id_condominio = false, $id_tipo_veiculo = false) {
        $array = false;
        $db = new mysqlsearch();
        $db->table("veiculo");
        $db->join("imovel", array("id_imovel", "=", "id"), "LEFT");
        $db->join("condominio", array("id_condominio", "=", "id", 2), "LEFT");
        $db->join("tipo_veiculo", array("id_tipo_veiculo", "=", "id", 1), "LEFT");
        $db->column("*", 1);
        $db->column("bloco", 2, "bloco_imovel");
        $db->column("apartamento", 2, "apartamento_imovel");
        $db->column("id", 3, "id_condominio");
        $db->column("nome", 3, "nome_condominio");
        $db->column("nome", 4, "nome_tipo_veiculo");

        if ($id_imovel) {
            $db->match("id_imovel", $id_imovel);
        }

        if ($id_condominio) {
            $db->match("id", $id_condominio, false, false, 3);
        }

        if ($id_tipo_veiculo) {
            $db->match("id", $id_tipo_veiculo, false, false, 4);
        }

        $db->order("nome_condominio");
        $db->order("bloco_imovel");
        $db->order("apartamento_imovel");
        $db->order("nome_tipo_veiculo");
        $db->order("placa");
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

    public static function cadastrar($placa, $descricao, $tag, $id_imovel, $id_tipo_veiculo) {
        if (!self::verificar($placa)) {
            $db = new mysqlsave();
            $db->table("veiculo");
            $db->column("placa", strtoupper($placa));
            $db->column("descricao", $descricao);
            $db->column("tag", $tag);
            $db->column("id_imovel", $id_imovel);
            $db->column("id_tipo_veiculo", $id_tipo_veiculo);
            if ($db->go()) {
                return $db->id();
            }
        }
        return false;
    }

    public static function atualizar($id, $placa, $descricao, $tag, $id_tipo_veiculo) {
        if (!self::verificar($placa, $id)) {
            $db = new mysqlsave();
            $db->table("veiculo");
            $db->column("placa", strtoupper($placa));
            $db->column("descricao", $descricao);
            $db->column("tag", $tag);
            $db->column("id_tipo_veiculo", $id_tipo_veiculo);
            $db->match("id", $id);
            return $db->go();
        }
        return false;
    }

    public static function remover($id) {
        $db = new mysqldelete();
        $db->table("veiculo");
        $db->match("id", $id);
        if ($db->go()) {
            return true;
        }
        return false;
    }

    public static function verificar($placa, $id = false) {
        $db = new mysqlsearch();
        $db->table("veiculo");
        $db->column("id");
        $db->match("placa", $placa);
        if ($id) {
            $db->match("id", $id, false, true);
        }
        $dao = $db->go();
        if (!empty($dao[0])) {
            return true;
        }
        return false;
    }

}