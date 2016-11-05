<?php

class spool {

    public static function usuario_senha_redefinida($nome, $email, $telefone, $senha) {
        if (spool_email::usuario_senha_redefinida($nome, $email, $senha)) {
            return spool_sms::usuario_senha_redefinida($telefone);
        }
        return false;
    }

}