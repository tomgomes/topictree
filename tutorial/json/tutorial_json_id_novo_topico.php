<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    $pagina = "";
    $mensagem = "";
    $topico_id = "";
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    
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
    
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['tutorial_id'];
        
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    //Exemplo, insert into sgc_topico(tutorial_id, topico_superior, topico_ordem, topico_nivel, topico_nome) values (15, NULL, 0, 0, 'Novo tópico');                
    $sql = "insert into sgc_topico(tutorial_id, topico_superior, topico_ordem, topico_nivel, topico_nome, topico_conteudo) " 
         . "values (:tutorial_id, :topico_superior, -1, -1, 'Novo tópico', '')";
    $pstmt = $cn->prepare($sql);
    $pstmt->bindParam(':tutorial_id', $tutorial_id);
    $pstmt->bindParam(':topico_superior', $dados_pagina['topico_superior']);
    $pstmt->execute();
    $topico_id = $cn->lastInsertId();
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['topico_id'] = $topico_id;
ob_end_clean();
echo json_encode($resposta);
?>
