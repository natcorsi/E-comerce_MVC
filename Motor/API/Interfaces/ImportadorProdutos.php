<?php
interface ImportadorProdutos {
   public function importarProdutos();
   public function importarVariantes($produtoId);
}
?>