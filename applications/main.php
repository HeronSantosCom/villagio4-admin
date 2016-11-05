<?php

class main extends logon {

    public function __construct() {
        parent::__construct();
        $this->titulo(false);
        if (parent::meu_id()) {
            if (!self::is_index()) {
                if (!self::is_xhr()) {
                    knife::redirect("/index.html");
                }
            }
            $this->layout();
        } else {
            $this->session_msgbox();
            switch ($_SERVER["REDIRECT_URL"]) {
                case "/redefinir.html":
                    $this->redefinir_senha();
                    break;
                case "/entrar.html":
                    break;
                default:
                    knife::redirect("entrar.html");
                    break;
            }
        }
    }

    public static function encode($string) {
        $md5 = new md5();
        return $md5->set($string);
    }

    public static function decode($string) {
        $md5 = new md5();
        return $md5->get($string);
    }

    private static function is_index() {
        return ($_SERVER["REDIRECT_URL"] == "/index.html");
    }

    private static function is_xhr() {
        return ($_SERVER["REDIRECT_URL"] == "/xhr.html");
    }

    protected function msgbox($msgbox) {
        $this->msgbox = $msgbox;
    }

    protected function session_msgbox($msgbox = false) {
        if ($msgbox) {
            $_SESSION["msgbox"] = $msgbox;
        } else {
            if (!empty($_SESSION["msgbox"])) {
                $this->msgbox($_SESSION["msgbox"]);
                unset($_SESSION["msgbox"]);
            }
        }
    }

    protected function aplicacao($aplicacao) {
        $aplicacao = "layout_{$aplicacao}";
        if (!class_exists($aplicacao, false) and file_exists(knife::application("{$aplicacao}.php"))) {
            new $aplicacao();
            if (defined("app_layout_error")) {
                return false;
            }
        }
        return true;
    }

    protected function titulo($titulo) {
        $this->page_titulo = name;
        if ($titulo) {
            $this->layout_titulo = $titulo;
            $this->page_titulo .= " // " . $this->layout_titulo;
        }
    }

    protected function nome($nome) {
        $this->layout_nome = $nome;
    }

    protected function icone($icone) {
        $this->layout_icone = $icone;
    }

    protected function css($css) {
        if (file_exists(knife::source($css))) {
            $this->layout_css = $css;
        }
    }

    protected function modulo($modulo) {
        $this->layout_modulo = knife::html($modulo);
    }

    protected function navbar($modulos) {
        $arr_navbar = array();
        if (is_array($modulos)) {
            foreach ($modulos as $key => $row) {
                if ($row["exibe"]) {
                    $arr_navbar[] = $row;
                }
            }
        }
        $this->layout_navbar = $arr_navbar;
    }

    private function layout() {
        $layout = (!empty($_GET["m"]) ? $_GET["m"] : "index");
        $modulo = dao_modulo::pegar($layout, logon::meu_id_tipo_usuario());
        if ($modulo) {
            $this->navbar(dao_modulo::listar(logon::meu_id_tipo_usuario()));
            if ($layout != "index") {
                $this->nome(($modulo["nome"] ? $modulo["nome"] : $modulo["titulo"]));
                $this->titulo(($modulo["titulo"] ? $modulo["titulo"] : $modulo["nome"]));
            }
            $this->icone($modulo["icone"]);
            if (self::is_xhr()) {
                return $this->aplicacao($layout);
            } else {
                if (file_exists(knife::source("layout/main/{$layout}.html"))) {
                    if ($this->aplicacao($layout)) {
                        $this->css("css/pages/{$layout}.css");
                        return $this->modulo("layout/main/{$layout}.html");
                    }
                }
                return $this->modulo("layout/main/erro.html");
            }
        }
        knife::redirect("error.html");
    }

}