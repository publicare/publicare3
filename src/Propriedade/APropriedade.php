<?php

namespace Pbl\Propriedade;

class APropriedade {

    private $nome;
    private $tabela;

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setTabela($tabela)
    {
        $this->tabela = $tabela;
    }

    public function getTabela()
    {
        return $this->tabela;
    }

}