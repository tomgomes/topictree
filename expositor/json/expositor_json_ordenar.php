<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    $mensagem = "";
    $pagina = "";
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    
    $dados_pagina = "";
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['dados'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;    
    $dados = json_decode($dados_pagina['dados']);
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $autor_id = $_SESSION['autor_id'];
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    $sql = "update sgc_tutorial " .
           "set tutorial_card_ordem = :tutorial_card_ordem " .
           "where autor_id = :autor_id and tutorial_id = :tutorial_id ";
    $pstmt = $cn->prepare($sql);
    
    $ordem = 0;    
    foreach ($dados as $tutorial_id){
        $ordem++;
        $pstmt->bindParam(':tutorial_card_ordem', $ordem);
        $pstmt->bindParam(':autor_id', $autor_id);
        $pstmt->bindParam(':tutorial_id', $tutorial_id);   
        $pstmt->execute();
    }//end foreach
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
ob_end_clean();
echo json_encode($resposta);
?>
