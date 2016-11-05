<?php

class layout_moradores extends main {

    public function __construct() {
        $this->id_tipo_morador = "3";
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
        }
        if (logon::meu_id_condominio()) {
            $this->condominio_unico = true;
            $this->id_condominio = logon::meu_id_condominio();
            $this->extract(dao_condominio::pegar($this->id_condominio), "condominio");
        } else {
            if (!empty($_POST["id_condominio"])) {
                $this->id_condominio = $_POST["id_condominio"];
            } else {
                if (!empty($_GET["id_condominio"])) {
                    $this->id_condominio = $_GET["id_condominio"];
                }
            }
            $this->condominios = dao_condominio::listar();
        }
        if (!empty($_POST["id_imovel"])) {
            $this->id_imovel = $_POST["id_imovel"];
        } else {
            if (!empty($_GET["id_imovel"])) {
                $this->id_imovel = $_GET["id_imovel"];
            }
        }
        $this->tipo_moradores = dao_tipo_morador::listar();
        $this->imoveis = dao_imovel::listar($this->id_condominio);
        if (!empty($_POST["buscador"])) {
            $this->extract($_POST);
            $this->moradores = dao_morador::listar($this->id_imovel, $this->id_condominio);
            $this->exibir_listagem = true;
        }
    }

    private function abrir($id) {
        $dao = (dao_morador::pegar($id));
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
            $this->msgbox("Não foi possível salvar o morador, verifique os dados e tente novamente!");
            if ($id) {
                $action = dao_morador::atualizar($id, $this->nome, $this->cpf, $this->email, $this->telefone1, $this->telefone2, $this->telefone3, $this->permitir_saida, $this->id_imovel, $this->id_tipo_morador);
            } else {
                $action = dao_morador::cadastrar($this->nome, $this->cpf, $this->email, $this->telefone1, $this->telefone2, $this->telefone3, $this->permitir_saida, $this->id_imovel, $this->id_tipo_morador);
            }
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Morador salvo com sucesso!");
            }
        }
    }

    private function remover($id) {
        $this->msgbox("Não foi possível remover o morador, tente novamente!");
        if ($id) {
            $action = dao_morador::remover($id);
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Morador removido com sucesso!");
            }
        }
    }

}