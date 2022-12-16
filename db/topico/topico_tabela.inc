<?php

class TabelaTopico{
    private $pstmt_encontrar;
    private $pstmt_ja_existe;
    private $pstmt_atualizar;
    private $pstmt_inserir;
    private $pstmt_excluir;
    private $cn;
    
    /* Método construtor */
    public function __construct($conexao){
        //Exemplo, insert into sgc_topico(tutorial_id, topico_superior, topico_ordem, topico_nivel, topico_nome) values (15, NULL, 0, 0, 'Novo tópico', '')
        $this->cn = $conexao;
        $sql = "insert into sgc_topico(tutorial_id, topico_superior, topico_ordem, topico_nivel, topico_nome, topico_conteudo, topico_oculto) " 
             . "values (:tutorial_id, :topico_superior, :topico_ordem, :topico_nivel, :topico_nome, '', 'N')";
        $this->pstmt_inserir = $this->cn->prepare($sql);
        
        //Exemplo, select * from sgc_topico where topico_id = 12
        $sql_encontrar = "select * from sgc_topico where topico_id = :topico_id";
        $this->pstmt_encontrar = $this->cn->prepare($sql_encontrar);
        
        //Exemplo, select 1 from sgc_topico where topico_id = 12
        $sql_ja_existe = "select 1 from sgc_topico where topico_id = :topico_id";
        $this->pstmt_ja_existe = $this->cn->prepare($sql_ja_existe);
        
        //Exemplo, update sgc_topico set topico_superior=0, topico_id=NULL,	topico_ordem=0, topico_nivel=0, topico_nome='' where topico_id=NULL
        $sql_atualizar = "update sgc_topico set "
                       . "tutorial_id=:tutorial_id, " 
                       . "topico_superior=:topico_superior, " 
                       . "topico_ordem=:topico_ordem, " 
                       . "topico_nivel=:topico_nivel, " 
                       . "topico_nome=:topico_nome, " 
                       . "topico_conteudo=:topico_conteudo, " 
                       . "topico_oculto=:topico_oculto "
                       . " where topico_id=:topico_id"; 
        $this->pstmt_atualizar = $this->cn->prepare($sql_atualizar);
        
        //Exemplo, delete from sgc_topico where topico_id = 185
        $sql_excluir = "delete from sgc_topico where topico_id = :topico_id";
        $this->pstmt_excluir = $this->cn->prepare($sql_excluir);
        
    }//end method
    
    public function salvar($registro){
        try {
            $existe = $this->ja_existe($registro->getTopicoID());
            if($existe){
                $tutorial_id = $registro->getTutorialID();
                $topico_superior = $registro->getTopicoSuperior();
                $topico_id = $registro->getTopicoID();
                $topico_ordem = $registro->getTopicoOrdem();
                $topico_nivel = $registro->getTopicoNivel();
                $topico_nome = $registro->getTopicoNome();
                
                $topico_conteudo = $registro->getTopicoConteudo();
                if(is_null($topico_conteudo))
                {
                    $topico_conteudo = '';
                }
                
                $topico_oculto = $registro->getTopicoOculto();
                if(is_null($topico_oculto))
                {
                    $topico_oculto = 'N';
                }
                
                $this->pstmt_atualizar->bindParam(':tutorial_id', $tutorial_id);
                $this->pstmt_atualizar->bindParam(':topico_superior', $topico_superior);
                $this->pstmt_atualizar->bindParam(':topico_ordem', $topico_ordem);
                $this->pstmt_atualizar->bindParam(':topico_nivel', $topico_nivel);
                $this->pstmt_atualizar->bindParam(':topico_nome', $topico_nome);
                $this->pstmt_atualizar->bindParam(':topico_id', $topico_id);
                $this->pstmt_atualizar->bindParam(':topico_conteudo', $topico_conteudo);
                $this->pstmt_atualizar->bindParam(':topico_oculto', $topico_oculto);
                $this->pstmt_atualizar->execute();                                                
            }else{
                $tutorial_id = $registro->getTutorialID();
                $topico_superior = $registro->getTopicoSuperior();
                $topico_ordem = $registro->getTopicoOrdem();
                $topico_nivel = $registro->getTopicoNivel();
                $topico_nome = $registro->getTopicoNome();
                
                $this->pstmt_inserir->bindParam(':tutorial_id', $tutorial_id);
                $this->pstmt_inserir->bindParam(':topico_superior', $topico_superior);
                $this->pstmt_inserir->bindParam(':topico_ordem', $topico_ordem);
                $this->pstmt_inserir->bindParam(':topico_nivel', $topico_nivel);
                $this->pstmt_inserir->bindParam(':topico_nome', $topico_nome);
                $this->pstmt_inserir->execute();                
            }//end if
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function ja_existe($id){
        try {
            $this->pstmt_ja_existe->bindParam(':topico_id', $id);
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
            $this->pstmt_encontrar->bindParam(':topico_id', $id);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
/*                 
                $registro = new RegistroTopico();
                $registro->setTutorialID($valores['tutorial_id']);
                $registro->setTopicoSuperior($valores['topico_superior']);
                $registro->setTopicoID($valores['topico_id']);
                $registro->setTopicoOrdem($valores['topico_ordem']);
                $registro->setTopicoNivel($valores['topico_nivel']);
                $registro->setTopicoNome($valores['topico_nome']);
                $registro->setTopicoConteudo($valores['topico_conteudo']);
                return $registro;
 */
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
            $this->pstmt_encontrar->bindParam(':topico_id', $id);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroTopico();
                $registro->setTutorialID($valores['tutorial_id']);
                $registro->setTopicoSuperior($valores['topico_superior']);
                $registro->setTopicoID($valores['topico_id']);
                $registro->setTopicoOrdem($valores['topico_ordem']);
                $registro->setTopicoNivel($valores['topico_nivel']);
                $registro->setTopicoNome($valores['topico_nome']);
                $registro->setTopicoConteudo($valores['topico_conteudo']);
                $registro->setTopicoOculto($valores['topico_oculto']);
                return $registro;
            }
            return null;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
    public function excluir($id){
        try {
            $this->pstmt_excluir->bindParam(':topico_id', $id);
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
