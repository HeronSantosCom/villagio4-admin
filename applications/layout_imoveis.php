<?php

class layout_imoveis extends main {

    public function __construct() {
        $this->extract($_GET);
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
        } else {
            if (logon::meu_id_condominio()) {
                $_POST["buscador"] = true;
            }
        }
        if (logon::meu_id_condominio()) {
            $this->condominio_unico = true;
            $this->id_condominio = logon::meu_id_condominio();
            $this->extract(dao_condominio::pegar($this->id_condominio), "condominio");
        } else {
            $this->condominios = dao_condominio::listar();
        }
        if (!empty($_POST["buscador"])) {
            $this->extract($_POST);
            $this->imoveis = dao_imovel::listar($this->id_condominio);
            $this->exibir_listagem = true;
        }
    }

    private function abrir($id) {
        $dao = (dao_imovel::pegar($id));
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
            $this->msgbox("Não foi possível salvar o imóvel, verifique os dados e tente novamente!");
            if ($id) {
                $action = dao_imovel::atualizar($id, $this->senha, $this->status);
            } else {
                $action = dao_imovel::cadastrar($this->bloco, $this->apartamento, $this->senha, $this->status, $this->id_condominio);
            }
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Imóvel salvo com sucesso!");
            }
        }
    }

    private function remover($id) {
        $this->msgbox("Não foi possível remover o imóvel, tente novamente!");
        if ($id) {
            $action = dao_imovel::remover($id);
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Imóvel removido com sucesso!");
            }
        }
    }

}