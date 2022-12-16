<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    $mensagem = "";
    $pagina = "";
    $depurar = false;
    
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
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
        
    $dados_pagina = "";
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "query_string" : "");
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['card_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "card_id" : "");
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['card_id'];
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando! " . ($depurar ? "autor_id" : "");
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $tabela = new TabelaTutorial($cn);
    
    $reg_tutorial = $tabela->encontrar_registro($tutorial_id);
    if($reg_tutorial == null):
        $mensagem = "Falha ao localizar o tutorial!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: Esse tutorial pertence mesmo ao autor?
    $autor_id_tut = $reg_tutorial->getAutorID();
    if($_SESSION['autor_id'] != $autor_id_tut):
        $mensagem = "Erro! Você não possui autoria sob este tutorial!";
        $pagina = "/expositor.php";
        throw new Exception($mensagem);
    endif;
    
    $tabela->excluir($tutorial_id);
        
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
//$resposta['tutorial_id'] = $tutorial_id;
ob_end_clean();
echo json_encode($resposta);
?>



