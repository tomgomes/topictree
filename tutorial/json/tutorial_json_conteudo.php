<?php
header('Content-type: application/json; charset=utf-8');
try {
    // require "../include/restrito.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    session_start();
    
    $dados_pagina = "";
    $mensagem = "";
    $pagina = "";
    $conteudo = "";
    
/*         
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
 */    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['topico_selecionado'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_id = $dados_pagina['topico_selecionado'];
    //SEGURANÇA: O tópico é realmente do usuário?
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $tabela = new TabelaTopico($cn);
    $registro = $tabela->encontrar_registro($topico_id);
    if($registro == null){
        $mensagem = "Falha ao localizar o tópico!";
        throw new Exception($mensagem);
    }//end if
    
    $conteudo = $registro->getTopicoConteudo();
    $tutorial_id = $registro->getTutorialID();
    
    //link com o top (tópico)
    //Exemplo de link, http://localhost:8181/tutorial.php?aut=1&tut=41&t=1572270172
    $tempo = filemtime($_SERVER["DOCUMENT_ROOT"] . '/tutorial.php');
    $link = $_SERVER["HTTP_HOST"] . "/tutorial.php?aut=0&tut=$tutorial_id&top=$topico_id&t=$tempo";
    $link = addhttp($link);
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['conteudo'] = $conteudo;
$resposta['link'] = $link;
ob_end_clean();
echo json_encode($resposta);
?>
