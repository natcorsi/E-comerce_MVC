<?php

class GerenciadorImportacao {
    private $importadores = [];

    public function registrarImportador($nome, $importador) {
        $this->importadores[$nome] = $importador;
    }

    public function importarProdutos($api) {
        if (!isset($this->importadores[$api])) {
            throw new Exception("API não registrada: $api");
        }
        return $this->importadores[$api]->importarProdutos();
    }

    public function importarVariantes($api, $produtoId) {
        if (!isset($this->importadores[$api])) {
            throw new Exception("API não registrada: $api");
        }
        return $this->importadores[$api]->importarVariantes($produtoId);
    }

   public function importarProdutoDetalhado($api, $produtoId) {
      if (!isset($this->importadores[$api])) {
         throw new Exception("API não registrada: $api");
      }
      return $this->importadores[$api]->importarProdutoDetalhado($produtoId);
   }
}
?>