<?php

class dao_modulo {

    public static function pegar($layout, $id_tipo_usuario) {
        $db = new mysqlsearch();
        $db->table("modulo");
        $db->column("*");
        $db->match("layout", $layout);
        $db->morethan("id_tipo_usuario", $id_tipo_usuario);
        $db->match("status", "1");
        $dao = $db->go();
        if ($dao) {
            return $dao[0];
        }
        return false;
    }

    public static function listar($id_tipo_usuario) {
        $db = new mysqlsearch();
        $db->table("modulo");
        $db->column("*");
        $db->order("prioridade");
        $db->morethan("id_tipo_usuario", $id_tipo_usuario);
        $db->match("status", "1");
        $dao = $db->go();
        if ($dao) {
            return self::getOrganizarModulos($dao, $id_tipo_usuario);
        }
        return false;
    }

    private static function getOrganizarModulos($array, $id_tipo_usuario) {
        if (is_array($array)) {
            $menu = false;
            foreach ($array as $row) {
                $row["sub"] = false;
                if (!$row["href"]) {
                    $row["href"] = "javascript:;";
                    if ($row["layout"]) {
                        $row["href"] = "?m={$row["layout"]}";
                    }
                }
                $menu[$row["id"]] = $row;
            }
            if ($menu) {
                foreach ($menu as $row) {
                    if ($row["id_modulo"]) {
                        $menu[$row["id_modulo"]]["sub"][$row["id"]] = true;
                    }
                }
                $_ENV["menu"] = $menu;
                $menu = false;
                foreach ($_ENV["menu"] as $idx => $rows) {
                    if (empty($rows["id_modulo"])) {
                        if (!empty($rows["id"])) {
                            $menu[$idx] = self::getSubsModulos($rows["id"]);
                        }
                    }
                }
                $_ENV["menu"] = $menu;
            }
        }
        return (!empty($_ENV["menu"]) ? $_ENV["menu"] : false);
    }

    private static function getSubsModulos($idx) {
        $array = false;
        if (!empty($_ENV["menu"][$idx])) {
            $array = $_ENV["menu"][$idx];
            $subs = (!empty($array["sub"]) ? $array["sub"] : false);
            if ($subs) {
                foreach ($subs as $subs_idx => $value) {
                    $array["sub"][$subs_idx] = self::getSubsModulos($subs_idx);
                }
            }
        }
        return $array;
    }

}