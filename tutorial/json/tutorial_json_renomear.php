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
        
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;    
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['novo_nome'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
/*     
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['tutorial_id'];
 */
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(mb_strlen($dados_pagina['novo_nome'], 'utf-8') > 60):
        $mensagem = "O nome do tópico é muito comprido. "
                  . "<br/>O tamanho máximo permitido é de 60 caracteres."
                  . "<br/>Atualize a página do seu navegador."
                  . "<br/>Não foi possível salvar o novo nome do tópico!";
        throw new Exception($mensagem);    
    endif;
    
    //SEGURANÇA: O tópico é realmente do usuário?
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();             
    $consulta = new TopicoConsultas($cn);        
    $consulta->renomear_topico($dados_pagina['novo_nome'], $dados_pagina['topico_id']);
    
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
