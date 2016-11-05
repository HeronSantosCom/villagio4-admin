<?php

class spool_email {

    private static function envia_para_cliente($email, $titulo, $mensagem) {
        $mensagem[] = "";
        $mensagem[] = "Lembre-se, em caso de dúvidas ou sugestões entre em contato conosco através dos nossos canais de atendimento!";
        $mensagem[] = "";
        $mensagem[] = name;
        $mensagem[] = "http://" . domain . "/";
        $mensagem[] = "";
        $mensagem[] = "---";
        $mensagem[] = "Notificação gerada e enviada automaticamente.";
        $mensagem[] = date("r") . " / " . getmypid() . (!empty($_SERVER["REMOTE_ADDR"]) ? " / " . $_SERVER["REMOTE_ADDR"] : false );
        $headers = "From: " . name . " <contato@villagio4.com>\n";
        $headers .= "Reply-To: " . name . " <contato@villagio4.com>\n";
        return knife::mail_utf8($email, '[' . name . '] ' . $titulo, htmlspecialchars(join("\n", $mensagem)), $headers);
    }

    private static function envia_para_gestor($nome, $email, $titulo, $mensagem) {
        $mensagem[] = "";
        $mensagem[] = name;
        $mensagem[] = "http://" . domain . "/";
        $mensagem[] = "";
        $mensagem[] = "---";
        $mensagem[] = "Notificação gerada e enviada automaticamente.";
        $mensagem[] = date("r") . " / " . getmypid() . (!empty($_SERVER["REMOTE_ADDR"]) ? " / " . $_SERVER["REMOTE_ADDR"] : false );
        $headers = "From: " . $nome . " <" . $email . ">\n";
        $headers .= "Reply-To: " . $nome . " <" . $email . ">\n";
        return knife::mail_utf8("contato@villagio4.com", '[' . name . '] ' . $titulo, htmlspecialchars(join("\n", $mensagem)), $headers);
    }

    public static function usuario_senha_redefinida($nome, $email, $senha) {
        $mensagem[] = "Olá {$nome},";
        $mensagem[] = "";
        $mensagem[] = "Como solicitado, estamos enviando sua nova senha de acesso!";
        $mensagem[] = "Por segurança, anote em um local seguro.";
        $mensagem[] = "";
        $mensagem[] = "\tSua nova senha é: {$senha}";
        $mensagem[] = "\tEndereço de acesso: http://" . domain . "/";
        $mensagem[] = "";
        $mensagem[] = "Esta é uma senha gerada aleatóriamente, por favor, altere!";
        return self::envia_para_cliente($email, "Sua senha foi redefinida!", $mensagem);
    }

}