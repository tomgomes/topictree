<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    $pagina = "";
    $mensagem = "";
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    //$topico_ordem = -1;
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_id = $dados_pagina['topico_id'];
    
    if(!isset($dados_pagina['topico_superior_destino'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_superior_destino = $dados_pagina['topico_superior_destino'];
    
    if(!isset($dados_pagina['topico_ordem_destino'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_ordem_destino = $dados_pagina['topico_ordem_destino'];
    
    if(!isset($dados_pagina['topico_ordem'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_ordem = $dados_pagina['topico_ordem'];
        
    /*
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;    
    $tutorial_id = $dados_pagina['tutorial_id'];
    
    if(!isset($dados_pagina['topico_selecionado'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
 */
    //SEGURANÇA: Os tópicos são realmente do usuário?
    
    if($topico_superior_destino == '#'):
        $topico_superior_destino = null;
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    //$tabela = new TabelaTopico($cn);
    $consulta = new TopicoConsultas($cn);                   
    $consulta->mover($topico_id, $topico_ordem, $topico_superior_destino, $topico_ordem_destino);
    
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
