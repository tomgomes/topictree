<?php

class AutorConsultas{
    private $cn;
    private $pstmt_ja_existe_email;
    private $pstmt_login;
    private $pstmt_encontrar_guid;
    private $stmt_gerar_guid;
    private $pstmt_encontrar_email;
    private $pstmt_encontrar_usuario;
    
    /* Método construtor */
    public function __construct($conexao){
        $this->cn = $conexao;
        
        //Exemplo, select 1 from sgc_autor where autor_usuario = 'leto'
        $sql_ja_existe_email = "select 1 from sgc_autor where autor_email = :autor_email";
        $this->pstmt_ja_existe_email = $this->cn->prepare($sql_ja_existe_email);        
        //Exemplo,
        $sql_login = "select autor_id, autor_usuario " .
            " from sgc_autor " .
            " where autor_usuario = :autor_usuario " .
            " and autor_assinatura_senha = :autor_assinatura_senha";
        $this->pstmt_login = $this->cn->prepare($sql_login);       
        
        //Exemplo,
        $sql_encontrar_guid = "select * from sgc_autor where autor_email_guid = :autor_email_guid";
        $this->pstmt_encontrar_guid = $this->cn->prepare($sql_encontrar_guid);                
        $sql_gerar_guid = "select UUID() guid";        
        $this->stmt_gerar_guid = $this->cn->prepare($sql_gerar_guid);
        
        $sql_encontrar_email = "select * from sgc_autor where autor_email = :autor_email";
        $this->pstmt_encontrar_email = $this->cn->prepare($sql_encontrar_email);
        
        $sql_encontrar_usuario = "select * from sgc_autor where autor_usuario = :autor_usuario";
        $this->pstmt_encontrar_usuario = $this->cn->prepare($sql_encontrar_usuario);
        
    }//end method
    
    public function ja_existe_email($autor_email){
        try {
            $this->pstmt_ja_existe_email->bindParam(':autor_email', $autor_email);
            $this->pstmt_ja_existe_email->execute();
            $rs = $this->pstmt_ja_existe_email->fetchAll();
            foreach($rs as $registro){
                unset($registro);
                return true;
            }
            return false;
        } catch (Exception  $ex) {
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function login($autor_usuario, $senha){
        try {            
            $this->pstmt_login->bindParam(':autor_usuario', $autor_usuario);
            $this->pstmt_login->bindParam(':autor_assinatura_senha', $senha);
            $this->pstmt_login->execute();
            $rs = $this->pstmt_login->fetchAll();            
            //$rs = $this->pstmt_login->fetch(PDO::FETCH_ASSOC);
            foreach($rs as $registro){
                return $registro;
            }
            return null;                                                
        } catch (Exception  $ex) {
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function encontrar_registro_por_guid($guid){
        try {
            $this->pstmt_encontrar_guid->bindParam(':autor_email_guid', $guid);
            $this->pstmt_encontrar_guid->execute();
            $rs = $this->pstmt_encontrar_guid->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroAutor();
                $registro->setAutorId($valores['autor_id']);
                $registro->setAutorNome($valores['autor_nome']);
                $registro->setAutorSobrenome($valores['autor_sobrenome']);
                $registro->setAutorDataNascimento($valores['autor_data_nascimento']);
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
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function encontrar_registro_por_email($email){
        try {
            $this->pstmt_encontrar_email->bindParam(':autor_email', $email);
            $this->pstmt_encontrar_email->execute();
            $rs = $this->pstmt_encontrar_email->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroAutor();
                $registro->setAutorId($valores['autor_id']);
                $registro->setAutorNome($valores['autor_nome']);
                $registro->setAutorSobrenome($valores['autor_sobrenome']);
                $registro->setAutorDataNascimento($valores['autor_data_nascimento']);
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
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function encontrar_registro_por_usuario($usuario){
        try {
            $this->pstmt_encontrar_usuario->bindParam(':autor_usuario', $usuario);
            $this->pstmt_encontrar_usuario->execute();
            $rs = $this->pstmt_encontrar_usuario->fetchAll();
            foreach($rs as $valores){
                $registro = new RegistroAutor();
                $registro->setAutorId($valores['autor_id']);
                $registro->setAutorNome($valores['autor_nome']);
                $registro->setAutorSobrenome($valores['autor_sobrenome']);
                $registro->setAutorDataNascimento($valores['autor_data_nascimento']);
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
            throw $ex; //rethrow it
        }//end try
    }//end method
    
    public function gerar_guid(){
        try {
            $this->stmt_gerar_guid->execute();
            $rs = $this->stmt_gerar_guid->fetchAll();
            foreach($rs as $valores){
                return $valores['guid'];
            }
            return null;
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
