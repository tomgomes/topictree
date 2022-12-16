<?php
//require $_SERVER["DOCUMENT_ROOT"] . "/include/php/db/topico/topico_registro.inc";

class TopicoConsultas{
    private $pstmt_ultimo_topico;
    private $pstmt_renomear_topico;
    private $pstmt_organizar_ao_excluir;
    private $pstmt_excluir_topico;
    private $pstmt_organizar_ao_adicionar;
    private $pstmt_organizar_ao_adicionar_nulo;
    private $pstmt_atualizar_topico;
    private $pstmt_topico_principal;
    private $pstmt_topico_abertura;
    private $pstmt_tutorial_abertura;
    private $cn;
    
    /* Método construtor */
    public function __construct($conexao){
        $this->cn = $conexao;
        
        //Exemplo, select max(topico_ordem) topico_ultimo from sgc_topico where topico_superior = 16
        $sql_ultimo_topico = "select max(topico_ordem) topico_ultimo from sgc_topico where topico_superior = :topico_superior";         
        $this->pstmt_ultimo_topico = $this->cn->prepare($sql_ultimo_topico);
        
        //Exemplo, update sgc_topico set topico_nome = "renomear" where topico_id = 123
        $sql_renomear_topico = "update sgc_topico set topico_nome = :topico_nome where topico_id = :topico_id";
        $this->pstmt_renomear_topico = $this->cn->prepare($sql_renomear_topico);
        
        //Exemplo, update sgc_topico set topico_ordem = (topico_ordem - 1) where topico_superior = 192 and topico_ordem > 1
        $sql_organizar_ao_excluir = "update sgc_topico set topico_ordem = (topico_ordem - 1) where topico_superior = :topico_superior and topico_ordem > :topico_ordem";
        $this->pstmt_organizar_ao_excluir = $this->cn->prepare($sql_organizar_ao_excluir);
        
        //Exemplo, delete from sgc_topico where topico_id = 185
        $sql_excluir = "delete from sgc_topico where topico_id = :topico_id";
        $this->pstmt_excluir_topico = $this->cn->prepare($sql_excluir);
        
        //Exemplo, update sgc_topico set topico_ordem = (topico_ordem + 1) where topico_superior = 192 and topico_ordem > 1
        //$sql_organizar_apos = "update sgc_topico set topico_ordem = (topico_ordem + 1) where topico_superior = :topico_superior and topico_ordem >= :topico_ordem";
        //$this->pstmt_organizar_apos = $this->cn->prepare($sql_organizar_apos);;
        
        //Exemplo, update sgc_topico set topico_ordem = (topico_ordem + 1) where topico_superior = 192 and topico_ordem > 1
        $sql_organizar_ao_adicionar = "update sgc_topico set topico_ordem = (topico_ordem + 1) where topico_superior = :topico_superior and topico_ordem >= :topico_ordem";
        $this->pstmt_organizar_ao_adicionar = $this->cn->prepare($sql_organizar_ao_adicionar);
        
        //Exemplo, update sgc_topico set topico_ordem = (topico_ordem + 1) where tutorial_id = 56 and topico_superior is null and topico_ordem >= 0
        $sql_organizar_ao_adicionar_nulo = "update sgc_topico set topico_ordem = (topico_ordem + 1) where tutorial_id = :tutorial_id and topico_superior is null and topico_ordem >= :topico_ordem";
        $this->pstmt_organizar_ao_adicionar_nulo = $this->cn->prepare($sql_organizar_ao_adicionar_nulo);
        
        //Exemplo, update sgc_topico set topico_superior=0, topico_id=NULL,	topico_ordem=0, topico_nivel=0, topico_nome='' where topico_id=NULL
        $sql_atualizar_topico = "update sgc_topico set tutorial_id=:tutorial_id, topico_superior=:topico_superior, topico_ordem=:topico_ordem, topico_nivel=:topico_nivel, topico_nome=:topico_nome where topico_id=:topico_id";
        $this->pstmt_atualizar_topico = $this->cn->prepare($sql_atualizar_topico);
        
        //Exemplo, select topico_id from sgc_topico where topico_superior is null and tutorial_id = 41
        $sql_topico_principal = "select topico_id from sgc_topico where topico_superior is null and tutorial_id = :tutorial_id";
        $this->pstmt_topico_principal = $this->cn->prepare($sql_topico_principal);
        
        //Exemplo, update sgc_topico set topico_aberto = 'S' where topico_id = 33
        $sql_topico_abertura = "update sgc_topico set topico_aberto = :estado where topico_id = :topico_id";
        $this->pstmt_topico_abertura = $this->cn->prepare($sql_topico_abertura);
        
        //Exemplo, update sgc_topico set topico_oculto = 'S' where topico_id = 25
        $sql_topico_oculto = "update sgc_topico set topico_oculto = :estado where topico_id = :topico_id";
        $this->pstmt_topico_oculto = $this->cn->prepare($sql_topico_oculto);
        
        $sql_tutorial_abertura = "update sgc_topico set topico_aberto = :estado where tutorial_id = :tutorial_id";
        $this->pstmt_tutorial_abertura = $this->cn->prepare($sql_tutorial_abertura);
        
        //Exemplo, select  tutorial_id, topico_id, topico_nome, topico_superior from sgc_topico, (select @pv := '47') initialisation where find_in_set(topico_superior, @pv) and length(@pv := concat(@pv, ',', topico_id))
        $sql_topico_ocultar_mostrar = "select topico_id from sgc_topico, (select @pv := :topico_superior) initialisation where find_in_set(topico_superior, @pv) and length(@pv := concat(@pv, ',', topico_id))";
        $this->pstmt_topico_ocultar_mostrar = $this->cn->prepare($sql_topico_ocultar_mostrar);
        
    }//end method
    
    public function ordem_ultimo_topico($topico_superior){
        try {
            $this->pstmt_ultimo_topico->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_ultimo_topico->execute();
            $rs = $this->pstmt_ultimo_topico->fetchAll();
            foreach($rs as $registro){
                return $registro['topico_ultimo'];                
            }
            return 0;            
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function topico_principal($tutorial_id){
        try {
            $this->pstmt_topico_principal->bindParam(':tutorial_id', $tutorial_id);
            $this->pstmt_topico_principal->execute();
            $rs = $this->pstmt_topico_principal->fetchAll();
            foreach($rs as $registro){
                return $registro['topico_id'];
            }
            return null;
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function renomear_topico($topico_novo_nome, $topico_id){
        try {
            $this->pstmt_renomear_topico->bindParam(':topico_nome', $topico_novo_nome);
            $this->pstmt_renomear_topico->bindParam(':topico_id', $topico_id);
            $this->pstmt_renomear_topico->execute();
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function topico_abertura($topico_id, $estado){
        try {
            $this->pstmt_topico_abertura->bindParam(':estado', $estado);
            $this->pstmt_topico_abertura->bindParam(':topico_id', $topico_id);
            $this->pstmt_topico_abertura->execute();
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function topico_ocultar($topico_id, $acao)
    {
        try 
        {
            if($acao == 'ocultar')
            {
                $this->cn->beginTransaction();
                
                //Ocultar topico selecionado
                $estado = 'S';
                $this->pstmt_topico_oculto->bindParam(':estado', $estado);
                $this->pstmt_topico_oculto->bindParam(':topico_id', $topico_id);
                $this->pstmt_topico_oculto->execute();
                
                //Obter todos tópicos filhos
                $this->pstmt_topico_ocultar_mostrar->bindParam(':topico_superior', $topico_id);
                $this->pstmt_topico_ocultar_mostrar->execute();
                $rs = $this->pstmt_topico_ocultar_mostrar->fetchAll();
                
                //Ocultar todos tópicos filhos
                foreach($rs as $registro)
                {
                    $filho_id = $registro['topico_id'];
                    $this->pstmt_topico_oculto->bindParam(':estado', $estado);
                    $this->pstmt_topico_oculto->bindParam(':topico_id', $filho_id);
                    $this->pstmt_topico_oculto->execute();
                }//end foreach
                $this->cn->commit();
            }//end if
        }
        catch (Exception  $ex)
        {
            $this->cn->rollBack();
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function tutorial_abertura($tutorial_id, $estado){
        try {
            $this->pstmt_tutorial_abertura->bindParam(':estado', $estado);
            $this->pstmt_tutorial_abertura->bindParam(':tutorial_id', $tutorial_id);
            $this->pstmt_tutorial_abertura->execute();
        } catch (Exception  $ex) {
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function excluir_topico($topico_superior, $topico_id, $topico_ordem){
        try {
            $this->cn->beginTransaction();
            
            $this->pstmt_excluir_topico->bindParam(':topico_id', $topico_id);
            $this->pstmt_excluir_topico->execute();
            
            $this->pstmt_organizar_ao_excluir->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_organizar_ao_excluir->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_organizar_ao_excluir->execute();
            
            $this->cn->commit();
        } catch (Exception  $ex) {
            $this->cn->rollBack();
            // rethrow it
            throw $ex;
        }//end try
    }//end method
    
    public function mover($topico_id, $topico_ordem, $topico_superior_destino, $topico_ordem_destino){
        try {
            $banco = new BancoDeDados();
            $cn = $banco->conectar();            
            $tabela = new TabelaTopico($cn);
            
            //REGISTRO SUPERIOR DESTINO
            if($topico_superior_destino == null){
                $reg_topico_superior = array();
                $reg_topico_superior['topico_nivel'] = 0;
                $nivel = 0;                
            }else{
                $reg_topico_superior = $tabela->encontrar($topico_superior_destino);
                if($reg_topico_superior == null){
                    $mensagem = "Tópico não encontrado. Não foi possível movê-lo!";
                    throw new Exception($mensagem);
                }//end if
                $nivel = $reg_topico_superior['topico_nivel'] + 1;                
            }//end if
            
            //REGISTRO MOVER - ACERTA SEU NOVO LOCAL
            $reg_topico_mover = $tabela->encontrar($topico_id);
            if($reg_topico_mover == null){
                $mensagem = "Tópico não encontrado. Não foi possível movê-lo!";
                throw new Exception($mensagem);
            }//end if
            $topico_superior_origem = $reg_topico_mover['topico_superior'];
            
            $reg_topico_mover['topico_superior'] = $topico_superior_destino;
            $reg_topico_mover['topico_nivel'] = $nivel;
            $reg_topico_mover['topico_ordem'] = $topico_ordem_destino;
            
            $cn->beginTransaction();
            
            //ORDENA VELHO LOCAL            
            //recriando a consulta devido o uso de transação            
            $sql_organizar_ao_excluir = "update sgc_topico set topico_ordem = (topico_ordem - 1) where topico_superior = :topico_superior and topico_ordem > :topico_ordem";
            $pstmt_organizar_ao_excluir = $cn->prepare($sql_organizar_ao_excluir);            
            $pstmt_organizar_ao_excluir->bindParam(':topico_superior', $topico_superior_origem);
            $pstmt_organizar_ao_excluir->bindParam(':topico_ordem', $topico_ordem);
            $pstmt_organizar_ao_excluir->execute();
            
            //ORDENA O NOVO LOCAL
            $sql_organizar_ao_adicionar = "update sgc_topico set topico_ordem = (topico_ordem + 1) where topico_superior = :topico_superior and topico_ordem >= :topico_ordem";
            $pstmt_organizar_ao_adicionar = $cn->prepare($sql_organizar_ao_adicionar);            
            $pstmt_organizar_ao_adicionar->bindParam(':topico_superior', $topico_superior_destino);
            $pstmt_organizar_ao_adicionar->bindParam(':topico_ordem', $topico_ordem_destino);
            $pstmt_organizar_ao_adicionar->execute();
            
            //ATUALIZA O TÓPICO MOVIDO
            $sql_atualizar_topico = "update sgc_topico set tutorial_id=:tutorial_id, topico_superior=:topico_superior, topico_ordem=:topico_ordem, topico_nivel=:topico_nivel, topico_nome=:topico_nome where topico_id=:topico_id";
            $pstmt_atualizar_topico = $cn->prepare($sql_atualizar_topico);
            $pstmt_atualizar_topico->bindParam(':tutorial_id', $reg_topico_mover['tutorial_id']);                        
            if($reg_topico_mover['topico_superior'] == null):
                $nulo = null;
                $pstmt_atualizar_topico->bindParam(':topico_superior', $nulo, PDO::PARAM_NULL);
            else:
                $pstmt_atualizar_topico->bindParam(':topico_superior', $reg_topico_mover['topico_superior']);
            endif;            
            $pstmt_atualizar_topico->bindParam(':topico_ordem', $reg_topico_mover['topico_ordem']);
            $pstmt_atualizar_topico->bindParam(':topico_nivel', $reg_topico_mover['topico_nivel']);
            $pstmt_atualizar_topico->bindParam(':topico_nome', $reg_topico_mover['topico_nome']);
            $pstmt_atualizar_topico->bindParam(':topico_id', $reg_topico_mover['topico_id']);
            $pstmt_atualizar_topico->execute();
            
            $cn->commit();
        } catch (Exception  $ex) {
            $cn->rollBack();            
            throw $ex; //rethrow it
        }finally{
            unset($pstmt_organizar_ao_excluir);
            unset($pstmt_organizar_ao_adicionar);
            unset($pstmt_atualizar_topico);
            unset($cn);
        }//end try
        $this->refazer_nivel_filhos($topico_id);
    }//end method
    
    private function refazer_nivel_filhos($topico_id){
        try {
            $banco = new BancoDeDados();
            $cn = $banco->conectar();            
            $tabela = new TabelaTopico($cn);
            
            //MARCAR TODOS TÓPICOS FILHOS
            $refazer_nivel = true;
            $sql_topico_filho_marcar = "update sgc_topico set topico_refazer_nivel = :topico_refazer_nivel where topico_id = :topico_id";
            $pstmt_topico_filho_marcar = $cn->prepare($sql_topico_filho_marcar);
            
            $sql_topicos_filhos = "select topico_id from sgc_topico, (select @pv := :topico_superior) initialisation where find_in_set(topico_superior, @pv) and length(@pv := concat(@pv, ',', topico_id))";
            $pstmt_topicos_filhos = $cn->prepare($sql_topicos_filhos);
            $pstmt_topicos_filhos->bindParam(':topico_superior', $topico_id);
            $pstmt_topicos_filhos->execute();
            $rs = $pstmt_topicos_filhos->fetchAll();
            foreach($rs as $registro){
                $pstmt_topico_filho_marcar->bindParam(':topico_refazer_nivel', $refazer_nivel);
                $pstmt_topico_filho_marcar->bindParam(':topico_id', $registro['topico_id']);
                $pstmt_topico_filho_marcar->execute();
            }//end for
            
            //PERCORRER TODOS NA ORDEM DO NÍVEL ANTIGO
            $refazer_nivel = false;
            $sql_percorrer = "select tutorial_id, topico_superior, topico_id, topico_ordem, topico_nivel, topico_nome, topico_refazer_nivel from sgc_topico where topico_refazer_nivel = true order by topico_nivel";
            $query_percorrer = $cn->prepare($sql_percorrer);
            $query_percorrer->execute();
            $rs = $query_percorrer->fetchAll();
            
            foreach($rs as $registro){
                
                $reg_topico_superior = $tabela->encontrar_registro($registro['topico_superior']);
                if($reg_topico_superior == null){
                    continue;
                }
                
                $nivel = $reg_topico_superior->getTopicoNivel() + 1;
                $reg_topico_filho = $tabela->encontrar_registro($registro['topico_id']);
                if($reg_topico_filho != null){
                    $reg_topico_filho->setTopicoNivel($nivel);
                    $tabela->salvar($reg_topico_filho);
                    
                    //DESMARCAR
                    $pstmt_topico_filho_marcar->bindParam(':topico_refazer_nivel', $refazer_nivel, PDO::PARAM_BOOL);
                    $pstmt_topico_filho_marcar->bindParam(':topico_id', $registro['topico_id']);
                    $pstmt_topico_filho_marcar->execute();
                }//end if
            }//end for
            
        } catch (Exception  $ex) {
            $cn->rollBack();            
            throw $ex; //rethrow it
        }finally{
            unset($pstmt_topicos_filhos);
            unset($pstmt_topico_filho_marcar);
            unset($cn);
        }//end try
    }//end method
    
    public function adicionar_organizar($registro){
        try {
            $this->cn->beginTransaction();
            
            $tutorial_id = $registro->getTutorialID();
            $topico_superior = $registro->getTopicoSuperior();
            $topico_id = $registro->getTopicoID();
            $topico_ordem = $registro->getTopicoOrdem();
            $topico_nivel = $registro->getTopicoNivel();
            $topico_nome = $registro->getTopicoNome();
            
            if($topico_superior == null):
                $this->pstmt_organizar_ao_adicionar_nulo->bindParam(':tutorial_id', $tutorial_id);
                $this->pstmt_organizar_ao_adicionar_nulo->bindParam(':topico_ordem', $topico_ordem);
                $this->pstmt_organizar_ao_adicionar_nulo->execute();            
            else:
                $this->pstmt_organizar_ao_adicionar->bindParam(':topico_superior', $topico_superior);
                $this->pstmt_organizar_ao_adicionar->bindParam(':topico_ordem', $topico_ordem);
                $this->pstmt_organizar_ao_adicionar->execute();
            endif;
            
            $this->pstmt_atualizar_topico->bindParam(':tutorial_id', $tutorial_id);
            $this->pstmt_atualizar_topico->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_atualizar_topico->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_atualizar_topico->bindParam(':topico_nivel', $topico_nivel);
            $this->pstmt_atualizar_topico->bindParam(':topico_nome', $topico_nome);
            $this->pstmt_atualizar_topico->bindParam(':topico_id', $topico_id);
            $this->pstmt_atualizar_topico->execute();
            
            $this->cn->commit();
        } catch (Exception  $ex) {
            $this->cn->rollBack();
            // rethrow it
            throw $ex;
        }//end try        
    }//end method
    
/*     
    public function adicionar_apos($registro){
        try {
            $this->cn->beginTransaction();
            
            $tutorial_id = $registro->getTutorialID();
            $topico_superior = $registro->getTopicoSuperior();
            $topico_id = $registro->getTopicoID();
            $topico_ordem = $registro->getTopicoOrdem();
            $topico_nivel = $registro->getTopicoNivel();
            $topico_nome = $registro->getTopicoNome();
            
            $this->pstmt_organizar_apos->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_organizar_apos->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_organizar_apos->execute();
            
            $this->pstmt_atualizar_topico->bindParam(':tutorial_id', $tutorial_id);
            $this->pstmt_atualizar_topico->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_atualizar_topico->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_atualizar_topico->bindParam(':topico_nivel', $topico_nivel);
            $this->pstmt_atualizar_topico->bindParam(':topico_nome', $topico_nome);
            $this->pstmt_atualizar_topico->bindParam(':topico_id', $topico_id);
            $this->pstmt_atualizar_topico->execute();
            
            $this->cn->commit();
        } catch (Exception  $ex) {
            $this->cn->rollBack();
            // rethrow it
            throw $ex;
        }//end try
        
    }//end method
    
    public function adicionar_antes($registro){
        try {
            $this->cn->beginTransaction();
            
            $tutorial_id = $registro->getTutorialID();
            $topico_superior = $registro->getTopicoSuperior();
            $topico_id = $registro->getTopicoID();
            $topico_ordem = $registro->getTopicoOrdem();
            $topico_nivel = $registro->getTopicoNivel();
            $topico_nome = $registro->getTopicoNome();
            
            $this->pstmt_organizar_apos->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_organizar_apos->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_organizar_apos->execute();
            
            $this->pstmt_atualizar_topico->bindParam(':tutorial_id', $tutorial_id);
            $this->pstmt_atualizar_topico->bindParam(':topico_superior', $topico_superior);
            $this->pstmt_atualizar_topico->bindParam(':topico_ordem', $topico_ordem);
            $this->pstmt_atualizar_topico->bindParam(':topico_nivel', $topico_nivel);
            $this->pstmt_atualizar_topico->bindParam(':topico_nome', $topico_nome);
            $this->pstmt_atualizar_topico->bindParam(':topico_id', $topico_id);
            $this->pstmt_atualizar_topico->execute();
            
            $this->cn->commit();
        } catch (Exception  $ex) {
            $this->cn->rollBack();
            // rethrow it
            throw $ex;
        }//end try
        
    }//end method
 */    
    
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
