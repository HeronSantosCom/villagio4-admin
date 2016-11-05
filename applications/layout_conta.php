<?php

class layout_conta extends main {

    public function __construct() {
        if (isset($_POST["email"])) {
            $this->salvar();
        }
        $this->extract(self::session());
    }

    private function salvar() {
        $this->extract($_POST);
        $this->msgbox("Não foi possível alterar sua conta, verifique os dados e tente novamente.");
        $action = dao_usuario::atualizar(logon::meu_id(), $this->nome, logon::meu_email(), $this->senha, logon::meu_status(), logon::meu_id_tipo_usuario(), logon::meu_id_condominio());
        //$action = dao_usuario::atualizar(logon::meu_id(), $this->nome, logon::meu_email(), $this->senha, $this->telefone, logon::meu_bloqueado(), logon::meu_motivo(), 1, 1, logon::meu_plano_id(), logon::meu_grupo_id());
        if ($action) {
            self::reboot(logon::meu_id(), false, false, (self::cookie() ? '1' : '0'));
            $this->msgbox("Sua conta foi alterada com sucesso.");
        }
    }

}