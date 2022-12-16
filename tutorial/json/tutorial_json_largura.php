<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    $pagina = "";
    $mensagem = "";
    $conteudo = "";
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
        
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['tutorial_id'];
    
    if(!isset($dados_pagina['largura'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $largura = $dados_pagina['largura'];
    
    if(!isset($dados_pagina['elemento'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $elemento = $dados_pagina['elemento'];
    
    //SEGURANÇA: O tópico é realmente do usuário?
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $tabela = new TabelaTutorial($cn);
    $registro = $tabela->encontrar_registro($tutorial_id);
    if($registro == null){
        $mensagem = "Falha ao localizar o tutorial!";
        throw new Exception($mensagem);
    }//end if
    
    if($elemento == 'arvore'):
        $registro->setTutorialLarguraArvore($largura);
    elseif($elemento == 'editor'):
        $registro->setTutorialLarguraEditor($largura);
    else:
        $mensagem = "Os valores para 'elemento' devem ser 'arvore' ou 'editor'!";
        throw new Exception($mensagem);    
    endif;
    
    $tabela->salvar($registro);
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['conteudo'] = $conteudo;
ob_end_clean();
echo json_encode($resposta);
?>
