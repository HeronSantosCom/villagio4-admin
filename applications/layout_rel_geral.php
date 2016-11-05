<?php

class layout_rel_geral extends main {

    public function __construct() {
        $this->extract($_GET);
        if (logon::meu_id_condominio()) {
            $this->condominio_unico = true;
            $this->id_condominio = logon::meu_id_condominio();
            $this->extract(dao_condominio::pegar($this->id_condominio), "condominio");
        } else {
            $this->condominios = dao_condominio::listar();
        }
        if (!empty($_POST["tipo"])) {
            $this->extract($_POST);
            if ($this->id_condominio) {
                switch ($this->tipo) {
                    case "pdf":
                        if (!class_exists("HTML2PDF", false)) {
                            include path::applications("extra/html2pdf/html2pdf.class.php");
                        }
                        $this->pdf();
                        $content = knife::html("layout/main/relatorios/rel_geral.html");
                        try {
                            $html2pdf = new HTML2PDF('P', 'A4', 'pt');
                            $html2pdf->setDefaultFont('Arial');
                            $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
                            $html2pdf->Output('relatorio_geral_' . getmypid() . '.pdf', 'FD');
                            unlink('relatorio_geral_' . getmypid() . '.pdf');
                            die();
                        } catch (HTML2PDF_exception $e) {
                            $this->msgbox("Erro ao processar relatório: " . $e);
                        }
                        break;
                    case "excel":
                        if (!class_exists("PHPExcel", false)) {
                            include path::applications("extra/PHPExcel.php");
                        }
                        $this->excel();
                        die();
                        break;
                }
            } else {
                $this->msgbox("É necessário especificar o condomínio!");
            }
        }
    }

    private function extrator() {
        if (!$this->condominio_unico) {
            $this->extract(dao_condominio::pegar($this->id_condominio), "condominio");
        }
    }

    private function pdf() {
        $this->extrator();
        $imoveis = dao_imovel::listar($this->id_condominio);
        $moradores = dao_morador::listar(false, $this->id_condominio);
        if ($moradores) {
            foreach ($moradores as $key => $morador) {
                $id_imovel = $morador["id_imovel"];
                if (!empty($imoveis[$id_imovel])) {
                    $imoveis[$id_imovel]["moradores"][$key] = $morador;
                }
            }
        }
        $veiculos = dao_veiculo::listar(false, $this->id_condominio);
        if ($veiculos) {
            foreach ($veiculos as $key => $veiculo) {
                $id_imovel = $veiculo["id_imovel"];
                if (!empty($imoveis[$id_imovel])) {
                    $imoveis[$id_imovel]["veiculos"][$key] = $veiculo;
                }
            }
        }
        $this->imoveis = $imoveis;
    }

    private function excel() {
        $this->extrator();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator(sys)
                ->setLastModifiedBy(name)
                ->setTitle($this->page_titulo)
                ->setSubject($this->page_titulo)
                ->setDescription("{$this->page_titulo} // {$this->condominio_nome}")
                ->setKeywords("office 2007 openxml php")
                ->setCreated(time())
                ->setCategory("Relatório");
        $objPHPExcel->getActiveSheet()->setTitle("Moradores {$this->condominio_nome}");

        // cria as colunas
        $linha = 1;
        $coluna = 0;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Bloco');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Apartamento');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Tipo');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Nome');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Autorização');
        $coluna++;
        $linha++;

        $coluna = 0;
        $moradores = dao_morador::listar(false, $this->id_condominio);
        if ($moradores) {
            foreach ($moradores as $key => $morador) {
                $coluna = 0;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $morador["bloco_imovel"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $morador["apartamento_imovel"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $morador["nome_tipo_morador"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $morador["nome"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, ($morador["id_tipo_morador"] == "3" ? ($morador["permitir_saida"] ? "Sim" : "Não") : ""));
                $coluna++;
                $linha++;
            }
        }

        $objPHPExcel->createSheet(NULL, 1);
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setTitle("Veículos {$this->condominio_nome}");

        $linha = 1;
        $coluna = 0;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Bloco');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Apartamento');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Tipo');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Placa');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Descrição');
        $coluna++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, 'Tag');
        $coluna++;
        $linha++;

        $veiculos = dao_veiculo::listar(false, $this->id_condominio);
        if ($veiculos) {
            foreach ($veiculos as $key => $veiculo) {
                $coluna = 0;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $veiculo["bloco_imovel"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $veiculo["apartamento_imovel"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $veiculo["nome_tipo_veiculo"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $veiculo["placa"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, $veiculo["descricao"]);
                $coluna++;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($coluna, $linha, ($veiculo["tag"] ? "Sim" : "Não") );
                $coluna++;
                $linha++;
            }
        }

        $arquivo = 'relatorio_geral_' . getmypid() . '.xlsx'; //str_replace(".pdf", ".xlsx", $filename);
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $arquivo . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($arquivo);
        echo fread(fopen($arquivo, 'r'), filesize($arquivo));
        unlink($arquivo);
    }

}