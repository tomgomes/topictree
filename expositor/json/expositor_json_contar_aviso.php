<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    if(!isset($_SESSION['autenticado'])):
        return;
    endif;
    if(!$_SESSION['autenticado']):
        return;
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    $mensagem = "";
    $pagina = "";
    
    $dados_pagina = "";
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['aviso'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $aviso = $dados_pagina['aviso'];
    
    if(($aviso != "add_card") && ($aviso != "mover_card")):
        $mensagem = "O valor de 'aviso' deve ser 'add_card' ou 'mover_card'!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $autor_id = $_SESSION['autor_id'];
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    if($aviso == "add_card"):
        $pstmt = $cn->prepare('update sgc_autor set autor_aviso_add_card = autor_aviso_add_card + 1 where autor_id = :autor_id');
        $pstmt->bindParam(':autor_id', $autor_id);
        $pstmt->execute();        
    elseif($aviso == "mover_card"):
        $pstmt = $cn->prepare('update sgc_autor set autor_aviso_mover_card = autor_aviso_mover_card + 1 where autor_id = :autor_id');
        $pstmt->bindParam(':autor_id', $autor_id);
        $pstmt->execute();        
    endif;            
    
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



