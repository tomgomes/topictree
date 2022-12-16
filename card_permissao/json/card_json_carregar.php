<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    $tutorial_id = "";
    $mensagem = "";
    $pagina = "";
    
    /*
       if(!isset($_POST['query_string'])):
            $mensagem = "Requisição inválida! Parâmetros faltando!";
            throw new Exception($mensagem);
        endif;
        $dados_pagina = "";    
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
    
    if(!isset($_POST['card_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $_POST['card_id'];
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();        
    $tabela = new TabelaTutorial($cn);
    $card = $tabela->encontrar($tutorial_id);
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['card'] = $card;
ob_end_clean();
echo json_encode($resposta);
?>



