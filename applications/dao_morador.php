<?php

class dao_morador {

    public static function pegar($id) {
        $db = new mysqlsearch();
        $db->table("morador");
        $db->join("imovel", array("id_imovel", "=", "id"), "LEFT");
        $db->join("condominio", array("id_condominio", "=", "id", 2), "LEFT");
        $db->join("tipo_morador", array("id_tipo_morador", "=", "id", 1), "LEFT");
        $db->column("*", 1);
        $db->column("bloco", 2, "bloco_imovel");
        $db->column("apartamento", 2, "apartamento_imovel");
        $db->column("id", 3, "id_condominio");
        $db->column("nome", 3, "nome_condominio");
        $db->column("nome", 4, "nome_tipo_morador");
        $db->match("id", $id);
        $dao = $db->go();
        if (!empty($dao[0])) {
            return self::hook($dao[0]);
        }
        return false;
    }

    public static function listar($id_imovel = false, $id_condominio = false, $id_tipo_morador = false) {
        $array = false;
        $db = new mysqlsearch();
        $db->table("morador");
        $db->join("imovel", array("id_imovel", "=", "id"), "LEFT");
        $db->join("condominio", array("id_condominio", "=", "id", 2), "LEFT");
        $db->join("tipo_morador", array("id_tipo_morador", "=", "id", 1), "LEFT");
        $db->column("*", 1);
        $db->column("bloco", 2, "bloco_imovel");
        $db->column("apartamento", 2, "apartamento_imovel");
        $db->column("id", 3, "id_condominio");
        $db->column("nome", 3, "nome_condominio");
        $db->column("nome", 4, "nome_tipo_morador");

        if ($id_imovel) {
            $db->match("id_imovel", $id_imovel);
        }

        if ($id_condominio) {
            $db->match("id", $id_condominio, false, false, 3);
        }

        if ($id_tipo_morador) {
            $db->match("id", $id_tipo_morador, false, false, 4);
        }

        $db->order("nome_condominio");
        $db->order("bloco_imovel");
        $db->order("apartamento_imovel");
        $db->order("nome_tipo_morador");
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

    public static function cadastrar($nome, $cpf, $email, $telefone1, $telefone2, $telefone3, $permitir_saida, $id_imovel, $id_tipo_morador) {
        if (!self::verificar($cpf)) {
            $db = new mysqlsave();
            $db->table("morador");
            $db->column("nome", $nome);
            $db->column("cpf", $cpf);
            $db->column("email", $email);
            $db->column("telefone1", $telefone1);
            $db->column("telefone2", $telefone2);
            $db->column("telefone3", $telefone3);
            $db->column("permitir_saida", $permitir_saida);
            $db->column("id_imovel", $id_imovel);
            $db->column("id_tipo_morador", $id_tipo_morador);
            if ($db->go()) {
                return $db->id();
            }
        }
        return false;
    }

    public static function atualizar($id, $nome, $cpf, $email, $telefone1, $telefone2, $telefone3, $permitir_saida, $id_imovel, $id_tipo_morador) {
        if (!self::verificar($cpf, $id)) {
            $db = new mysqlsave();
            $db->table("morador");
            $db->column("nome", $nome);
            $db->column("cpf", $cpf);
            $db->column("email", $email);
            $db->column("telefone1", $telefone1);
            $db->column("telefone2", $telefone2);
            $db->column("telefone3", $telefone3);
            $db->column("permitir_saida", $permitir_saida);
            $db->column("id_imovel", $id_imovel);
            $db->column("id_tipo_morador", $id_tipo_morador);
            $db->match("id", $id);
            return $db->go();
        }
        return false;
    }

    public static function remover($id) {
        $db = new mysqldelete();
        $db->table("morador");
        $db->match("id", $id);
        if ($db->go()) {
            return true;
        }
        return false;
    }

    public static function verificar($cpf, $id = false) {
        $db = new mysqlsearch();
        $db->table("morador");
        $db->column("id");
        $db->match("cpf", $cpf);
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