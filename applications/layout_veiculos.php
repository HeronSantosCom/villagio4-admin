<?php

class layout_veiculos extends main {

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
        $this->tipo_veiculos = dao_tipo_veiculo::listar();
        $this->imoveis = dao_imovel::listar($this->id_condominio);
        if (!empty($_POST["buscador"])) {
            $this->extract($_POST);
            $this->veiculos = dao_veiculo::listar($this->id_imovel, $this->id_condominio);
            $this->exibir_listagem = true;
        }
    }

    private function abrir($id) {
        $dao = (dao_veiculo::pegar($id));
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
            $this->msgbox("Não foi possível salvar o veículo, verifique os dados e tente novamente!");
            if ($id) {
                $action = dao_veiculo::atualizar($id, $this->placa, $this->descricao, $this->tag, $this->id_tipo_veiculo);
            } else {
                $action = dao_veiculo::cadastrar($this->placa, $this->descricao, $this->tag, $this->id_imovel, $this->id_tipo_veiculo);
            }
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Veículo salvo com sucesso!");
            }
        }
    }

    private function remover($id) {
        $this->msgbox("Não foi possível remover o veículo, tente novamente!");
        if ($id) {
            $action = dao_veiculo::remover($id);
            if ($action) {
                $_POST["buscador"] = true;
                unset($this->formulario);
                $this->msgbox("Veículo removido com sucesso!");
            }
        }
    }

}