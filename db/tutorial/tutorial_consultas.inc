<?php

class TutorialConsultas
{
    private $cn;
    private $pstmt_todos_tutoriais;
    
    /* Método construtor */
    public function __construct($conexao)
    {
        $this->cn = $conexao;
        
        $sql_todos_tutoriais = "select tutorial_id, " . 
            "tutorial_card_ordem, " .
            "tutorial_card_tipo, " .
            "tutorial_card_predefinida, " .
            "tutorial_card_personalizada, " .
            "tutorial_card_texto, " .
            "tutorial_card_cor, " .
            "tutorial_card_permissao, " .
            "autor_aviso_add_card, " .
            "autor_aviso_mover_card " .
            "from sgc_tutorial " .
            "inner join sgc_autor on sgc_tutorial.autor_id = sgc_autor.autor_id " .
            "where sgc_autor.autor_id = :autor_id " .
            "order by tutorial_card_ordem";        
        $this->pstmt_todos_tutoriais = $this->cn->prepare($sql_todos_tutoriais);                
        
        $sql_todos_tutoriais_para_visitante = "select tutorial_id, " . 
            "tutorial_card_ordem, " .
            "tutorial_card_tipo, " .
            "tutorial_card_predefinida, " .
            "tutorial_card_personalizada, " .
            "tutorial_card_texto, " .
            "tutorial_card_cor, " .
            "tutorial_card_permissao, " .
            "autor_aviso_add_card, " .
            "autor_aviso_mover_card " .
            "from sgc_tutorial " .
            "inner join sgc_autor on sgc_tutorial.autor_id = sgc_autor.autor_id " .
            "where sgc_autor.autor_id = :autor_id and tutorial_card_permissao != 'SE' " .
            "order by tutorial_card_ordem";        
        $this->pstmt_todos_tutoriais_para_visitante = $this->cn->prepare($sql_todos_tutoriais_para_visitante);
        
    }//end method
    
    public function todos_tutoriais($autor_id){
        try {
            $this->pstmt_todos_tutoriais->bindParam(':autor_id', $autor_id);
            $this->pstmt_todos_tutoriais->execute();
            $rs_tutorial = $this->pstmt_todos_tutoriais->fetchAll(PDO::FETCH_ASSOC);
            return $rs_tutorial;
        } 
        catch (Exception  $ex) 
        {
            throw $ex;
        }//end try
    }//end method
    
    public function todos_tutoriais_para_visitante($autor_id)
    {
        try 
        {
            $this->pstmt_todos_tutoriais_para_visitante->bindParam(':autor_id', $autor_id);
            $this->pstmt_todos_tutoriais_para_visitante->execute();
            $rs_tutorial = $this->pstmt_todos_tutoriais_para_visitante->fetchAll(PDO::FETCH_ASSOC);
            return $rs_tutorial;
        } 
        catch (Exception  $ex) 
        {
            throw $ex;
        }//end try
    }//end method
    
    /* Evita que a classe seja clonada */
    private function __clone()
    {
    }//end method
    
    /* Método que remove da memória todas as variáveis setadas */
    public function __destruct()
    {        
        foreach ($this as $key => $value)
        {
            unset($this->$key);
            unset($value);
        }//end foreach
    }//end method    
}//end class

?>
