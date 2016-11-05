<?php

class logon extends app {

    public function __construct() {
        if (isset($_GET["sair"]) || isset($_GET["reset"])) {
            $this->logoff();
        }
        $this->login();
    }

    private function login() {
        if (empty($_SESSION["logon"])) {
            $this->extract(self::cookie());
            $this->extract($_POST);
            if ($this->usuario && $this->senha) {
                if (self::reboot(false, $this->usuario, $this->senha, $this->relembrar)) {
                    return true;
                }
                $this->msgbox("O nome de usuário ou a senha inserida está(ão) incorreto(s).");
            }
            return false;
        }
        return $_SESSION["logon"];
    }

    protected function redefinir_senha() {
        $this->extract($_POST);
        if ($this->usuario) {
            $senha = substr(sha1(uniqid($this->usuario . mt_rand(), true)), 0, 8);
            $this->msgbox("Ocorreu um erro ao redefinir sua senha, tente novamente mais tarde!");
            if (dao_usuario::redefinir_senha(strtolower($this->usuario), $senha)) {
                $usuario = dao_usuario::pegar(false, $this->usuario, $senha);
                if ($usuario) {
                    $this->session_msgbox("Sua senha foi redefinida com sucesso.");
                    if (spool::usuario_senha_redefinida($usuario["nome"], $usuario["email"], false, $senha)) {
                        $this->session_msgbox("Sua senha foi redefinida com sucesso e sua nova credencial foi enviada para seu e-mail.");
                    }
                    knife::redirect("/entrar.html");
                    return true;
                }
            }
        }
        return false;
    }

    protected static function logoff() {
        $_COOKIE = false;
        $_SESSION = false;
        setcookie("logon", false, time() + 60 * 60 * 24 * 100, "/");
        session_unset();
        session_destroy();
        return true;
    }

    protected static function cookie($dao = false) {
        if ($dao) {
            return setcookie("logon", base64_encode(serialize($dao)), time() + 60 * 60 * 24 * 100, "/");
        }
        return (!empty($_COOKIE["logon"]) ? unserialize(base64_decode($_COOKIE["logon"])) : false);
    }

    protected static function session() {
        return (!empty($_SESSION["logon"]) ? $_SESSION["logon"] : false);
    }

    public static function reboot($id, $usuario, $senha, $relembrar) {
        $_SESSION["logon"] = dao_usuario::pegar($id, $usuario, $senha);
        if ($_SESSION["logon"]) {
            if ($relembrar) {
                self::cookie(array("usuario" => $usuario, "senha" => $senha, "relembrar" => $relembrar));
            }
            return $_SESSION["logon"];
        }
        return false;
    }

    public static function __callStatic($name, $arguments) {
        return (!empty($_SESSION["logon"][str_replace("meu_", "", $name)]) ? $_SESSION["logon"][str_replace("meu_", "", $name)] : (!empty($arguments[0]) ? $arguments[0] : false));
    }

}