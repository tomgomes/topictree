<?php
header('Content-type: application/json; charset=utf-8');
try {

    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_consultas.inc";
    
    $mensagem = "";
    $pagina = "";
    $rs_tutorial = array();
    
    /*
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    */
    
    if(!isset($_SESSION['visitante'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $consultas = new TutorialConsultas($cn);
    
    if($_SESSION['visitante']):
        $autor_id = $_SESSION['autor_id_para_visitante'];
        $rs_tutorial = $consultas->todos_tutoriais_para_visitante($autor_id);
    else:
        $autor_id = $_SESSION['autor_id'];
        $rs_tutorial = $consultas->todos_tutoriais($autor_id);
    endif;
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['tutoriais'] = $rs_tutorial;
$resposta['pagina'] = $pagina;
ob_end_clean();
echo json_encode($resposta);
?>









