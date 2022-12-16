<?php
    //ATUALIZA OS NÍVEIS DOS TÓPICOS DE UM DETERMINADO TUTORIAL
    
    //require "../include/database.inc";
    require "../include/database_oo.inc";
    
    //$cn = new PDO($connection_string, $user_name, $password);
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    /*Exemplo: select topico_id, topico_superior from sgc_topico where topico_id = 7 */    
    $sql_superior = "select topico_id, topico_superior " .
        "from sgc_topico " .
        "where topico_id = :topico_id";
    $pstmt_superior = $cn->prepare($sql_superior);
    
    /* Exemplo: update sgc_topico set topico_nivel = 2 where topico_id = 8 */
    $sql_nivel = "update sgc_topico set topico_nivel = :topico_nivel where topico_id = :topico_id";
    $pstmt_nivel = $cn->prepare($sql_nivel);
    
    /* Exemplo: select topico_id, topico_superior from sgc_topico where tutorial_id = 15 */
    $sql_topico = "select topico_id, topico_superior " .
        " from sgc_topico where tutorial_id = :tutorial_id";
    $pstmt_topico = $cn->prepare($sql_topico);
    $tutorial_id = 15;
    $pstmt_topico->bindParam(":tutorial_id", $tutorial_id);
    $pstmt_topico->execute();
    $rs_topico = $pstmt_topico->fetchAll();    
    foreach($rs_topico as $reg_topico){
        $nivel = 0;
        
        if ($reg_topico['topico_superior'] != null){
            $topico_superior = $reg_topico['topico_superior'];
            $continua = true;
            
            do{
                $nivel++;
                $pstmt_superior->bindParam(":topico_id", $topico_superior);
                $pstmt_superior->execute();
                if($pstmt_superior->rowCount() == 0){
                    /* ERRO TÓPICO SUPERIOR NÃO ENCONTRADO */
                }            
                $reg_superior = $pstmt_superior->fetch(PDO::FETCH_ASSOC);
                if($reg_superior['topico_superior'] == NULL){
                //if($reg_superior == false){
                    $continua = false;
                }else{
                    $topico_superior = $reg_superior['topico_superior'];
                }
            }while($continua);                       
        }//end if
        //Atualiza o nível do tópico
        $pstmt_nivel->bindParam(":topico_nivel", $nivel);
        $pstmt_nivel->bindParam(":topico_id", $reg_topico['topico_id']);
        $pstmt_nivel->execute();        
    }//end foreach
    
?>


