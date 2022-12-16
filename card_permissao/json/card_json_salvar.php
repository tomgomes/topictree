<?php
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
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $autor_id = $_SESSION['autor_id'];
    
    $dados_pagina = "";    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    //Se tiver &nbsp; converte para o caracter espaço ' '    
    $query_string = html_entity_decode($_POST['query_string']);
    //Cada parâmetro da querystring, ou seja, &param= será um elemento nomeado do array 
    parse_str($query_string, $dados_pagina);
    
    if(!isset($dados_pagina['permissao'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_permissao = $dados_pagina['permissao'];
    
    //Se a permissao for: Todos com senha
    if ($tutorial_permissao == "TCS"):
        if(!isset($dados_pagina['senha'])):
            $mensagem = "Requisição inválida! Parâmetros faltando!";
            throw new Exception($mensagem);
        endif;
        $tutorial_card_senha = $dados_pagina['senha'];
    endif;
    
    if(!isset($dados_pagina['card_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['card_id'];
    
    //SEGURANÇA: O tutorial/card é mesmo da pessoa?
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $tabela = new TabelaTutorial($cn);
    
    $registro = $tabela->encontrar_registro($tutorial_id);
    if($registro == null):
        $mensagem = "Registro não encontrado na tabela!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: Esse card/tutorial pertence mesmo ao autor?
    $autor_id_tut = $registro->getAutorID();
    if($_SESSION['autor_id'] != $autor_id_tut):
        $mensagem = "Erro! Você não possui autoria sob este tutorial!";
        $pagina = "/expositor.php";
        throw new Exception($mensagem);
    endif;
    
    //Altero somente a permissao e salvo
    $registro->setTutorialCardPermissao($tutorial_permissao);
    if ($tutorial_permissao == "TCS"):
        $registro->setTutorialCardSenha($tutorial_card_senha);
    else:
        $registro->setTutorialCardSenha(null);
    endif;
    $tabela->salvar($registro);
    
    $mensagem = "Os dados foram salvos!";
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



