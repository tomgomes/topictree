<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    $mensagem = "";
    $pagina = "";
    $tutorial_id = "";
    
    //SEGURANÇA: Somente autores autenticados podem executar esta operação
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    //$dados_pagina = "";
    
    /*
       if(!isset($_POST['query_string'])):
            $mensagem = "Requisição inválida! Parâmetros faltando!";
            throw new Exception($mensagem);
        endif;
    
        parse_str($_POST['query_string'], $dados_pagina);
        
        if(!isset($dados_pagina['topico_superior'])):
            $mensagem = "Requisição inválida! Parâmetros faltando!";
            throw new Exception($mensagem);
        endif;
        
        if(!isset($_SESSION['tutorial_id'])):
            $mensagem = "Dados na sessão estão faltando!";
            throw new Exception($mensagem);
        endif;
    */
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    //Exemplo, select max(tutorial_card_ordem) ordem from sgc_tutorial where autor_id = 1
    $sql = "select max(tutorial_card_ordem) ordem from sgc_tutorial where autor_id = :autor_id";
    $pstmt_ordem = $cn->prepare($sql);
    $pstmt_ordem->bindParam(':autor_id', $_SESSION['autor_id']);
    $pstmt_ordem->execute();
    $rs_ordem = $pstmt_ordem->fetchAll(PDO::FETCH_ASSOC);
    $ordem = null;
    foreach($rs_ordem as $registro){
        $ordem = $registro['ordem'];
        if($ordem == null):
            $ordem = 1;
        else:
            $ordem++;
        endif;
    }//end foreach
    
    $tutorial_card_nome = "Título";
    $tutorial_card_tipo = "pre_definida";
    $tutorial_card_predefinida = "expositor/resources/card_cinza.png";
    $tutorial_card_cor = "cinza";
    $tutorial_card_texto = "<p><br></p><p><br></p><p style='text-align: center;'><span style='font-size:24px;'><span style='color:#ffffff;'>Título</span></span></p><p><br></p><p><br></p><p><br></p><p><span style='font-size:16px;'><span style='color:#000000;'>Descrição...</span></span></p>";

    //Exemplo, insert into sgc_tutorial (autor_id, tutorial_card_ordem)	values (1, 1)
    $sql = "insert into sgc_tutorial (" 
         . "autor_id, "
         . "tutorial_card_nome, "
         . "tutorial_card_ordem, "
         . "tutorial_card_tipo, "
         . "tutorial_card_predefinida, "
         . "tutorial_card_cor, "
         . "tutorial_card_texto, "
         . "tutorial_card_permissao "
         . ") values ( "
         . ":autor_id, "             
         . ":tutorial_card_nome, "
         . ":tutorial_card_ordem, "
         . ":tutorial_card_tipo, "
         . ":tutorial_card_predefinida, "
         . ":tutorial_card_cor, "
         . ":tutorial_card_texto, "
         . "'T' "
         . ")";
    $pstmt = $cn->prepare($sql);
    $pstmt->bindParam(':autor_id', $_SESSION['autor_id']);    
    $pstmt->bindParam(':tutorial_card_nome', $tutorial_card_nome);        
    $pstmt->bindParam(':tutorial_card_ordem', $ordem);    
    $pstmt->bindParam(':tutorial_card_tipo', $tutorial_card_tipo);
    $pstmt->bindParam(':tutorial_card_predefinida', $tutorial_card_predefinida);
    $pstmt->bindParam(':tutorial_card_cor', $tutorial_card_cor);
    $pstmt->bindParam(':tutorial_card_texto', $tutorial_card_texto);            
    $pstmt->execute();
    $tutorial_id = $cn->lastInsertId();
    
    // inicio - Cria ou atualiza o tópico principal
    // usos
    // card/json/card_json_salvar.php
    // expositor/json/expositor_json_id_novo_tutorial.php
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    $tabelaTopico = new TabelaTopico($cn);
    $consultas = new TopicoConsultas($cn);
    
    $topic_id = $consultas->topico_principal($tutorial_id);
    
    $registro_principal = new RegistroTopico();
    $registro_principal->setTopicoConteudo(null);
    $registro_principal->setTopicoID($topic_id);
    $registro_principal->setTopicoNivel(0);
    $registro_principal->setTopicoNome($tutorial_card_nome);
    $registro_principal->setTopicoOrdem(0);
    $registro_principal->setTopicoSuperior(null);
    $registro_principal->setTutorialID($tutorial_id);
    $tabelaTopico->salvar($registro_principal);
    //fim
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['tutorial_id'] = $tutorial_id;
ob_end_clean();
echo json_encode($resposta);
?>



