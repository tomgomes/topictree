<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    //SEGURANÇA: Somente autores autenticados podem executar esta operação
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    $pagina = "";
    $mensagem = "";
    $depurar = false;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    //teste
    //$_SESSION['tutorial_id'] = 15;

/*     
    if(!isset($_SESSION['tutorial_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
 */    

/*
    if(!isset($_POST['query_string'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    
     if(!isset($dados_pagina['topico_superior'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
     if(!isset($dados_pagina['posicao'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['hierarquia'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['topico_selecionado'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['topico_conteudo'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
 */
    
    if(!isset($_POST['topico_selecionado'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['topico_conteudo'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    $tabela_topico = new TabelaTopico($cn);
    $reg_topico = $tabela_topico->encontrar_registro($_POST['topico_selecionado']);
    if($reg_topico == null):
        $mensagem = "O tópico selecionado não foi encontrado no banco de dados!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $reg_topico->getTutorialID();
    
    $tabela_tutorial = new TabelaTutorial($cn);
    $reg_tutorial = $tabela_tutorial->encontrar_registro($tutorial_id);
    if($reg_tutorial == null):
        $mensagem = "Tutorial não foi encontrado no banco de dados!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: Esse tutorial pertence mesmo ao autor?
    $autor_id_tut = $reg_tutorial->getAutorID();
    if($_SESSION['autor_id'] != $autor_id_tut):
        $mensagem = "Erro! Você não possui autoria sob este tutorial!";
        $pagina = "/expositor.php";
        throw new Exception($mensagem);
    endif;
    
    $reg_topico->setTopicoConteudo($_POST['topico_conteudo']);
    $tabela_topico->salvar($reg_topico);
    
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




