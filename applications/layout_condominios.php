<?php

class layout_condominios extends main {

    public function __construct() {
        if (isset($_GET["cadastrar"]) || isset($_GET["editar"]) || isset($_GET["remover"])) {
            $this->formulario = true;
            if (!empty($_GET["id"])) {
                if (!$this->abrir($_GET["id"])) {
                    define("app_layout_error", true);
                    return false;
                }
            }
            if (isset($_GET["remover"])) {
                $this->remover = true;
            }
            if (isset($_POST["id"])) {
                $this->salvar($_POST["id"]);
            }
        }
        if (logon::meu_id_condominio()) {
            $this->condominios = array(dao_condominio::pegar(logon::meu_id_condominio()));
        } else {
            $this->condominios = dao_condominio::listar();
        }
    }

    private function abrir($id) {
        $dao = (dao_condominio::pegar($id));
        if ($dao) {
            $this->extract($dao);
            return true;
        }
        return false;
    }

    private function salvar($id) {
        $this->extract($_POST);
        if ($this->remover) {
            if ($id) {
                $this->remover($_POST["id"]);
            }
        } else {
            $this->msgbox("Não foi possível salvar o condomínio, verifique os dados e tente novamente!");
            if ($id) {
                $action = dao_condominio::atualizar($id, $this->nome, $this->endereco, $this->status);
            } else {
                $action = dao_condominio::cadastrar($this->nome, $this->endereco, $this->status);
            }
            if ($action) {
                unset($this->formulario);
                $this->msgbox("Condomínio salvo com sucesso!");
            }
        }
    }

    private function remover($id) {
        $this->msgbox("Não foi possível remover o condomínio, tente novamente!");
        if ($id) {
            $action = dao_condominio::remover($id);
            if ($action) {
                unset($this->formulario);
                $this->msgbox("Condomínio removido com sucesso!");
            }
        }
    }

}