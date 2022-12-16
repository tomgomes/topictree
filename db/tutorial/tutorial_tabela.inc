<?php

class TabelaTutorial{
    private $pstmt_encontrar;
    private $pstmt_ja_existe;
    private $pstmt_atualizar;
    private $pstmt_inserir;
    private $pstmt_excluir;
    private $cn;
    
    /* Método construtor */
    public function __construct($conexao){
        $this->cn = $conexao;
        
        //Exemplo, insert into sgc_tutorial (tutorial_id, autor_id, tutorial_card_nome, tutorial_card_ordem, tutorial_card_tipo, tutorial_card_predefinida, tutorial_card_cor, tutorial_card_personalizada, tutorial_card_texto, tutorial_card_permissao) values (null, 1, null, 0, 'pre_definida', null, 'amarelo', null, null, 'T')
        $sql = "insert into sgc_tutorial ( "
             . "    tutorial_id, "
             . "    autor_id, "
             . "    tutorial_card_nome, "
             . "    tutorial_card_ordem, "
             . "    tutorial_card_tipo, "
             . "    tutorial_card_predefinida, "
             . "    tutorial_card_cor, "
             . "    tutorial_card_personalizada, "
             . "    tutorial_card_texto, "
             . "    tutorial_largura_arvore, "
             . "    tutorial_largura_editor, "             
             . "    tutorial_card_permissao, "
             . "    tutorial_card_senha "
             . ")values("
             . "    :tutorial_id, "
             . "    :autor_id, "
             . "    :tutorial_card_nome, "
             . "    :tutorial_card_ordem, "
             . "    :tutorial_card_tipo, "
             . "    :tutorial_card_predefinida, "
             . "    :tutorial_card_cor, "
             . "    :tutorial_card_personalizada, "
             . "    :tutorial_card_texto, "
             . "    :tutorial_largura_arvore, "
             . "    :tutorial_largura_editor, "             
             . "    :tutorial_card_permissao, "
             . "    :tutorial_card_senha "
             . ")";
        
        $this->pstmt_inserir = $this->cn->prepare($sql);
        
        //Exemplo, select tutorial_id, autor_id, tutorial_card_nome, tutorial_card_ordem, tutorial_card_tipo, tutorial_card_predefinida, tutorial_card_cor, tutorial_card_personalizada, tutorial_card_textofrom sgc_tutorial where tutorial_id = 41
        $sql_encontrar = "select tutorial_id, " 
                       . "autor_id, " 
                       . "tutorial_card_nome, " 
                       . "tutorial_card_ordem, " 
                       . "tutorial_card_tipo, " 
                       . "tutorial_card_predefinida, " 
                       . "tutorial_card_cor, " 
                       . "tutorial_card_personalizada, " 
                       . "tutorial_card_texto, "
                       . "tutorial_largura_arvore, "
                       . "tutorial_largura_editor, "
                       . "tutorial_card_permissao, "
                       . "tutorial_card_senha "
                       . "from sgc_tutorial "
                       . "where tutorial_id = :tutorial_id";        
        $this->pstmt_encontrar = $this->cn->prepare($sql_encontrar);
        
        //Exemplo, select 1 from sgc_tutorial where tutorial_id = 41
        $sql_ja_existe = "select 1 from sgc_tutorial where tutorial_id = :tutorial_id";
        $this->pstmt_ja_existe = $this->cn->prepare($sql_ja_existe);
        
        //Exemplo, update sgc_tutorial set autor_id = 1, tutorial_card_nome = 'Teste', tutorial_card_ordem = 5, tutorial_card_tipo = 'pre_definida', tutorial_card_predefinida = 'resources/card_amarelo.png', tutorial_card_cor='amarelo', tutorial_card_personalizada = null, tutorial_card_texto = 'Teste' where tutorial_id = 52
        $sql_atualizar = "update sgc_tutorial set "
                       . "autor_id = :autor_id, "
		               . "tutorial_card_nome = :tutorial_card_nome, "
		               . "tutorial_card_ordem = :tutorial_card_ordem, "
		               . "tutorial_card_tipo = :tutorial_card_tipo, "
		               . "tutorial_card_predefinida = :tutorial_card_predefinida, "
		               . "tutorial_card_cor= :tutorial_card_cor, "
		               . "tutorial_card_personalizada = :tutorial_card_personalizada, "
		               . "tutorial_card_texto = :tutorial_card_texto, "
		               . "tutorial_largura_arvore = :tutorial_largura_arvore, "
		               . "tutorial_largura_editor = :tutorial_largura_editor, "
		               . "tutorial_card_permissao = :tutorial_card_permissao, "
		               . "tutorial_card_senha = :tutorial_card_senha "
		               . "where tutorial_id = :tutorial_id";
        
        $this->pstmt_atualizar = $this->cn->prepare($sql_atualizar);
        
        //Exemplo, delete from sgc_tutorial where tutorial_id = 53
        $sql_excluir = "delete from sgc_tutorial where tutorial_id = :tutorial_id";
        $this->pstmt_excluir = $this->cn->prepare($sql_excluir);
        
    }//end method
    
    public function salvar($registro){
        try {
            $existe = $this->ja_existe($registro->getTutorialID());
            if($existe){
                $autor_id = $registro->getAutorID();
                $tutorial_card_nome = $registro->getTutorialCardNome();
                $tutorial_card_ordem = $registro->getTutorialCardOrdem();
                
                //$tutorial_card_tipo = $registro->getTutorialCardTipo();
                $tutorial_card_tipo = "";
                
                $tutorial_card_predefinida = $registro->getTutorialCardPredefinida();
                $tutorial_card_cor = $registro->getTutorialCardCor();
                $tutorial_card_personalizada = $registro->getTutorialCardPersonalizada();
                $tutorial_card_texto = $registro->getTutorialCardTexto();
                $tutorial_id = $registro->getTutorialID();
                $tutorial_largura_arvore = $registro->getTutorialLarguraArvore();
                $tutorial_largura_editor = $registro->getTutorialLarguraEditor();
                $tutorial_card_permissao =  $registro->getTutorialCardPermissao();
                $tutorial_card_senha = $registro->getTutorialCardSenha();
                
                $this->pstmt_atualizar->bindParam(':autor_id', $autor_id);
                $this->pstmt_atualizar->bindParam(':tutorial_card_nome', $tutorial_card_nome);
                $this->pstmt_atualizar->bindParam(':tutorial_card_ordem', $tutorial_card_ordem);
                $this->pstmt_atualizar->bindParam(':tutorial_card_tipo', $tutorial_card_tipo);
                $this->pstmt_atualizar->bindParam(':tutorial_card_predefinida', $tutorial_card_predefinida);
                $this->pstmt_atualizar->bindParam(':tutorial_card_cor', $tutorial_card_cor);
                $this->pstmt_atualizar->bindParam(':tutorial_card_personalizada', $tutorial_card_personalizada);
                $this->pstmt_atualizar->bindParam(':tutorial_card_texto', $tutorial_card_texto);
                $this->pstmt_atualizar->bindParam(':tutorial_largura_arvore', $tutorial_largura_arvore);
                $this->pstmt_atualizar->bindParam(':tutorial_largura_editor', $tutorial_largura_editor);
                $this->pstmt_atualizar->bindParam(':tutorial_card_permissao', $tutorial_card_permissao);
                $this->pstmt_atualizar->bindParam(':tutorial_card_senha', $tutorial_card_senha);
                $this->pstmt_atualizar->bindParam(':tutorial_id', $tutorial_id);
                $this->pstmt_atualizar->execute();                                                
            }else{
                //$tutorial_id = $registro->getTutorialID();
                $autor_id = $registro->getAutorID();
                $tutorial_card_nome = $registro->getTutorialCardNome();
                $tutorial_card_ordem = $registro->getTutorialCardOrdem();
                $tutorial_card_tipo = $registro->getTutorialCardTipo();
                $tutorial_card_predefinida = $registro->getTutorialCardPredefinida();
                $tutorial_card_cor = $registro->getTutorialCardCor();
                $tutorial_card_personalizada = $registro->getTutorialCardPersonalizada();
                $tutorial_card_texto = $registro->getTutorialCardTexto();
                $tutorial_card_permissao =  $registro->getTutorialCardPermissao();
                $tutorial_card_senha = $registro->getTutorialCardSenha();
                
                $this->pstmt_inserir->bindParam(':tutorial_id', null);
                $this->pstmt_inserir->bindParam(':autor_id', $autor_id);                
                $this->pstmt_inserir->bindParam(':tutorial_card_nome', $tutorial_card_nome);
                $this->pstmt_inserir->bindParam(':tutorial_card_ordem', $tutorial_card_ordem);
                $this->pstmt_inserir->bindParam(':tutorial_card_tipo', $tutorial_card_tipo);
                $this->pstmt_inserir->bindParam(':tutorial_card_predefinida', $tutorial_card_predefinida);
                $this->pstmt_inserir->bindParam(':tutorial_card_cor', $tutorial_card_cor);
                $this->pstmt_inserir->bindParam(':tutorial_card_personalizada', $tutorial_card_personalizada);
                $this->pstmt_inserir->bindParam(':tutorial_card_texto', $tutorial_card_texto); 
                $this->pstmt_inserir->bindParam(':tutorial_largura_arvore', $tutorial_largura_arvore);
                $this->pstmt_inserir->bindParam(':tutorial_largura_editor', $tutorial_largura_editor); 
                $this->pstmt_inserir->bindParam(':tutorial_card_permissao', $tutorial_card_permissao);
                $this->pstmt_inserir->bindParam(':tutorial_card_senha', $tutorial_card_senha);
                $this->pstmt_inserir->execute();                
            }//end if
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function ja_existe($id){
        try {
            $this->pstmt_ja_existe->bindParam(':tutorial_id', $id);
            $this->pstmt_ja_existe->execute();
            $rs = $this->pstmt_ja_existe->fetchAll();
            foreach($rs as $registro){
                unset($registro);
                return true;
            }
            return false;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
    public function encontrar($id){
        try {
            $this->pstmt_encontrar->bindParam(':tutorial_id', $id);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
                return $valores;
            }//end foreach
            return null;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function encontrar_registro($id){
        try {
            $this->pstmt_encontrar->bindParam(':tutorial_id', $id);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroTutorial();
                $registro->setTutorialID($valores['tutorial_id']);
                $registro->setAutorID($valores['autor_id']);
                $registro->setTutorialCardNome($valores['tutorial_card_nome']);
                $registro->setTutorialCardOrdem($valores['tutorial_card_ordem']);
                $registro->setTutorialCardTipo($valores['tutorial_card_tipo']);
                $registro->setTutorialCardPredefinida($valores['tutorial_card_predefinida']);
                $registro->setTutorialCardCor($valores['tutorial_card_cor']);
                $registro->setTutorialCardPersonalizada($valores['tutorial_card_personalizada']);
                $registro->setTutorialCardTexto($valores['tutorial_card_texto']);
                $registro->setTutorialLarguraArvore($valores['tutorial_largura_arvore']);
                $registro->setTutorialLarguraEditor($valores['tutorial_largura_editor']);
                $registro->setTutorialCardPermissao($valores['tutorial_card_permissao']);
                $registro->setTutorialCardSenha($valores['tutorial_card_senha']);
                return $registro;
            }//end foreach
            return null;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
    public function excluir($id){
        try {
            $this->pstmt_excluir->bindParam(':tutorial_id', $id);
            $this->pstmt_excluir->execute();
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
    /* Evita que a classe seja clonada */
    private function __clone(){
    }//end method
    
    /* Método que remove da memória todas as variáveis setadas */
    public function __destruct() {        
        foreach ($this as $key => $value) {
            unset($this->$key);
            unset($value);
        }//end foreach
    }//end method
    
}//end class

?>
