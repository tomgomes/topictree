<?php

class TabelaAutor{
    private $pstmt_encontrar;
    private $pstmt_ja_existe;
    private $pstmt_atualizar;
    private $pstmt_inserir;
    private $pstmt_excluir;
    private $cn;
    
    /* Método construtor */
    public function __construct($conexao){
        //Exemplo,         
        $this->cn = $conexao;
        $sql = "insert into sgc_autor ( " . 
          "autor_nome, " .
          "autor_sobrenome, " .
          "autor_data_nascimento, " .
          "autor_email, " .
          "autor_email_guid, " .
          "autor_usuario, " .
          "autor_assinatura_senha) " .
          "values ( " .
          ":autor_nome, " .
          ":autor_sobrenome, " .
          ":autor_data_nascimento, " .
          ":autor_email, " .
          ":autor_email_guid, " .
          ":autor_usuario, " .
          ":autor_assinatura_senha)";                        
        $this->pstmt_inserir = $this->cn->prepare($sql);
        
        //Exemplo, 
        $sql_encontrar = "select * from sgc_autor where autor_usuario = :autor_usuario";
        $this->pstmt_encontrar = $this->cn->prepare($sql_encontrar);
        
        //Exemplo, select 1 from sgc_autor where autor_usuario = 'leto'
        $sql_ja_existe = "select 1 from sgc_autor where autor_usuario = :autor_usuario";
        $this->pstmt_ja_existe = $this->cn->prepare($sql_ja_existe);
        
        //Exemplo, 
        $sql_atualizar = "update sgc_autor set " .
          "autor_nome = :autor_nome, " .
          "autor_sobrenome = :autor_sobrenome, " .
          "autor_data_nascimento = :autor_data_nascimento, " .
          "autor_email = :autor_email, " .
          "autor_email_verificado = :autor_email_verificado, " .
          "autor_email_codigo = :autor_email_codigo, " .
          "autor_assinatura_senha = :autor_assinatura_senha, " .
          "autor_aviso_add_card = :autor_aviso_add_card, " .
          "autor_aviso_mover_card = :autor_aviso_mover_card " .
          "where autor_usuario = :autor_usuario";        
        $this->pstmt_atualizar = $this->cn->prepare($sql_atualizar);
        
        //Exemplo, delete from sgc_autor where autor_usuario = 'Sandra'
        $sql_excluir = "delete from sgc_autor where autor_usuario = :autor_usuario";
        $this->pstmt_excluir = $this->cn->prepare($sql_excluir);
        
    }//end method
    
    public function salvar_registro($registro){
        try {
            $existe = $this->ja_existe($registro->getAutorUsuario());
            if($existe){                
                $autor_nome = $registro->getAutorNome();
                $autor_sobrenome = $registro->getAutorSobrenome();
                $autor_data_nascimento = $registro->getAutorDataNascimento();
                $autor_email = $registro->getAutorEmail();
                
                $autor_email_verificado = $registro->getAutorEmailVerificado();
                $autor_email_codigo = $registro->getAutorEmailCodigo();
                $autor_usuario = $registro->getAutorUsuario();
                $autor_assinatura_senha = $registro->getAutorAssinaturaSenha();
                $autor_aviso_add_card = $registro->getAutorAvisoAddCard();
                $autor_aviso_mover_card = $registro->getAutorAvisoMoverCard();
                
                $this->pstmt_atualizar->bindParam(':autor_nome', $autor_nome);
                $this->pstmt_atualizar->bindParam(':autor_sobrenome', $autor_sobrenome);
                $this->pstmt_atualizar->bindParam(':autor_data_nascimento', $autor_data_nascimento);
                $this->pstmt_atualizar->bindParam(':autor_email', $autor_email);
                
                $this->pstmt_atualizar->bindParam(':autor_email_verificado', $autor_email_verificado);
                $this->pstmt_atualizar->bindParam(':autor_email_codigo', $autor_email_codigo);                                
                $this->pstmt_atualizar->bindParam(':autor_assinatura_senha', $autor_assinatura_senha);
                $this->pstmt_atualizar->bindParam(':autor_aviso_add_card', $autor_aviso_add_card);
                $this->pstmt_atualizar->bindParam(':autor_aviso_mover_card', $autor_aviso_mover_card);
                $this->pstmt_atualizar->bindParam(':autor_usuario', $autor_usuario);                
                $this->pstmt_atualizar->execute();                                                
            }else{
                $autor_nome = $registro->getAutorNome();
                $autor_sobrenome = $registro->getAutorSobrenome();
                $autor_data_nascimento = $registro->getAutorDataNascimento();
                $autor_email = $registro->getAutorEmail();
                $autor_email_guid = $registro->getAutorEmailGuid();
                $autor_usuario = $registro->getAutorUsuario();
                $autor_assinatura_senha = $registro->getAutorAssinaturaSenha();
                
                $this->pstmt_inserir->bindParam(':autor_nome', $autor_nome);
                $this->pstmt_inserir->bindParam(':autor_sobrenome', $autor_sobrenome);
                $this->pstmt_inserir->bindParam(':autor_data_nascimento', $autor_data_nascimento);
                $this->pstmt_inserir->bindParam(':autor_email', $autor_email);
                $this->pstmt_inserir->bindParam(':autor_email_guid', $autor_email_guid);
                $this->pstmt_inserir->bindParam(':autor_usuario', $autor_usuario);
                $this->pstmt_inserir->bindParam(':autor_assinatura_senha', $autor_assinatura_senha);
                $this->pstmt_inserir->execute();                
            }//end if
        } catch (Exception  $ex) {            
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function ja_existe($autor_usuario){
        try {
            $this->pstmt_ja_existe->bindParam(':autor_usuario', $autor_usuario);
            $this->pstmt_ja_existe->execute();
            $rs = $this->pstmt_ja_existe->fetchAll();
            foreach($rs as $registro){
                unset($registro);
                return true;
            }
            return false;
        } catch (Exception  $ex) {            
            throw $ex; //rethrow it
        }//end try        
    }//end method
    
    public function encontrar($autor_usuario){
        try {
            $this->pstmt_encontrar->bindParam(':autor_usuario', $autor_usuario);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
                return $valores;
            }//end foreach
            return null;
        } catch (Exception  $ex) {            
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function encontrar_registro($autor_usuario){
        try {
            $this->pstmt_encontrar->bindParam(':autor_usuario', $autor_usuario);
            $this->pstmt_encontrar->execute();
            $rs = $this->pstmt_encontrar->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroAutor();                
                $registro->setAutorId($valores['autor_id']);
                $registro->setAutorNome($valores['autor_nome']);
                $registro->setAutorSobrenome($valores['autor_sobrenome']);
                
                //converte para dd/mm/aaaa
                $data_nascimento = date("d/m/Y", strtotime($valores['autor_data_nascimento']));
                $registro->setAutorDataNascimento($data_nascimento);
                //$registro->setAutorDataNascimento($valores['autor_data_nascimento']);
                
                $registro->setAutorEmail($valores['autor_email']);
                $registro->setAutorEmailGuid($valores['autor_email_guid']);
                $registro->setAutorEmailVerificado($valores['autor_email_verificado']);
                $registro->setAutorEmailCodigo($valores['autor_email_codigo']);
                $registro->setAutorUsuario($valores['autor_usuario']);
                $registro->setAutorAssinaturaSenha($valores['autor_assinatura_senha']);
                $registro->setAutorAvisoAddCard($valores['autor_aviso_add_card']);
                $registro->setAutorAvisoMoverCard($valores['autor_aviso_mover_card']);                
                return $registro;
            }
            return null;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
    public function excluir($autor_usuario){
        try {
            $this->pstmt_excluir->bindParam(':autor_usuario', $autor_usuario);
            $this->pstmt_excluir->execute();
        } catch (Exception  $ex) {            
            throw $ex; //rethrow it
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
