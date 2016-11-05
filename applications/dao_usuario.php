<?php

class dao_usuario {

    public static function pegar($id, $usuario = false, $senha = false) {
        $db = new mysqlsearch();
        $db->table("usuario");
        $db->column("*");
        if ($id) {
            $db->match("id", $id);
        } else {
            $db->match("email", $usuario);
            $db->match("senha", md5($senha));
        }
        $dao = $db->go();
        if (!empty($dao[0])) {
            return self::hook($dao[0]);
        }
        return false;
    }

    public static function listar($id_condominio = false) {
        $array = false;
        $db = new mysqlsearch();
        $db->table("usuario");
        $db->column("*");
        if ($id_condominio) {
            $db->match("id_condominio", $id_condominio);
            $db->match("id", $id_condominio, false, true);
        }
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

    public static function cadastrar($nome, $email, $senha, $id_tipo_usuario, $id_condominio) {
        if (!self::verificar($email)) {
            $db = new mysqlsave();
            $db->table("usuario");
            $db->column("nome", $nome);
            $db->column("email", $email);
            $db->column("senha", md5($senha));
            $db->column("id_tipo_usuario", $id_tipo_usuario);
            $db->column("id_condominio", $id_condominio);
            if ($db->go()) {
                return $db->id();
            }
        }
        return false;
    }

    public static function atualizar($id, $nome, $email, $senha, $status, $id_tipo_usuario, $id_condominio) {
        if (!self::verificar($email, $id)) {
            $db = new mysqlsave();
            $db->table("usuario");
            $db->column("nome", $nome);
            $db->column("email", $email);
            if ($senha) {
                $db->column("senha", md5($senha));
            }
            $db->column("status", $status);
            $db->column("id_tipo_usuario", $id_tipo_usuario);
            $db->column("id_condominio", $id_condominio);
            $db->match("id", $id);
            return $db->go();
        }
        return false;
    }

    public static function redefinir_senha($email, $senha) {
        $db = new mysqlsave();
        $db->table("usuario");
        $db->column("senha", md5($senha));
        $db->match("email", $email);
        return $db->go();
    }

    public static function remover($id) {
        $db = new mysqldelete();
        $db->table("usuario");
        $db->match("id", $id);
        if ($db->go()) {
            return true;
        }
        return false;
    }

    public static function verificar($email, $id = false) {
        if (knife::is_mail($email)) {
            $db = new mysqlsearch();
            $db->table("usuario");
            $db->column("id");
            $db->match("email", $email);
            if ($id) {
                $db->match("id", $id, false, true);
            }
            $dao = $db->go();
            if (!empty($dao[0])) {
                return true;
            }
        }
        return false;
    }

}