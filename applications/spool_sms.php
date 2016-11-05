<?php

class spool_sms {

    private static function envia_sms($celular, $mensagem) {
        $celular = trim(str_replace(array("(", ")", "-", "+", "_", " "), "", $celular));
        if ($celular) {
            $mensagem = "[" . name . "] {$mensagem} " . date("d/m \à\s H:i");
            $db = new mysqlsave();
            $db->table("spool_sms");
            $db->column("numero", $celular);
            $db->column("mensagem", $mensagem);
            return $db->go();
        }
        return true;
    }

    public static function usuario_senha_redefinida($telefone) {
        return self::envia_sms($telefone, "Sua senha foi redefinida e enviada para o seu e-mail.");
    }

}