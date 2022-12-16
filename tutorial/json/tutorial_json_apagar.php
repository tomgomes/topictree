<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    $pagina = "";    
    $mensagem = "";
    $depurar = false;
    
    //SEGURANÇA: Essa ação é somente para autor, 
    //           além disso autenticado, ou seja, digitou a senha corretamente
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
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    $dados_pagina = "";
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "query_string" : "");
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando! " . ($depurar ? "autor_id" : "");
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "topico_id" : "");
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    $tabela_topico = new TabelaTopico($cn);
    $reg_topico = $tabela_topico->encontrar_registro($dados_pagina['topico_id']);
    
    if($reg_topico == null):
        $mensagem = "Falha ao localizar o tópico!";
        throw new Exception($mensagem);        
    endif;
    
    //SEGURANÇA: Encontrar o tutorial para saber quem é o autor
    $tabela_tutorial = new TabelaTutorial($cn);
    $reg_tutorial = $tabela_tutorial->encontrar_registro($reg_topico->getTutorialID());
    if($reg_tutorial == null):
        $mensagem = "Falha ao localizar o tutorial!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: O tópico é realmente do usuário?    
    $autor_id_tut = $reg_tutorial->getAutorID();
    if($_SESSION['autor_id'] != $autor_id_tut):
        $mensagem = "Erro! Para excluir um tópico, você deve ser o autor deste.";
        throw new Exception($mensagem);
    endif;
    
    $consulta = new TopicoConsultas($cn);
    $consulta->excluir_topico($reg_topico->getTopicoSuperior(), $dados_pagina['topico_id'], $reg_topico->getTopicoOrdem());
    
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
