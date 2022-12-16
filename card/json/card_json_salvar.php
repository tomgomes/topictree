<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    $mensagem = "";
    $pagina = "";
    
    $depurar = true;
    
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
    
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $autor_id = $_SESSION['autor_id'];
    
    $dados_pagina = "";    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "query_string" : "") ;
        throw new Exception($mensagem);
    endif;
    
    //Se tiver &nbsp; converte para o caracter espaço ' '    
    $query_string = html_entity_decode($_POST['query_string']);
    //Cada parâmetro da querystring, ou seja, &param= será um elemento nomeado do array 
    parse_str($query_string, $dados_pagina);
    
    if(!isset($dados_pagina['card_nome'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "card_nome" : "") ;
        throw new Exception($mensagem);
    endif;
    $tutorial_card_nome = $dados_pagina['card_nome'];
    
    if( empty($tutorial_card_nome) or ($tutorial_card_nome == "Título") ):
        $mensagem = "Por favor, informe um nome para o card, ou seja, título ou assunto.";
        throw new Exception($mensagem);        
    endif;
    
    if(mb_strlen($tutorial_card_nome, "UTF-8") > 60):
        $mensagem = "Erro! Título possui mais que 60 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['card_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "card_id" : "") ;
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['card_id'];
    
    if(!isset($dados_pagina['card_texto'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "card_texto" : "") ;
        throw new Exception($mensagem);
    endif;
    $tutorial_card_texto = $dados_pagina['card_texto'];
    
    if(!isset($dados_pagina['cor'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "cor" : "") ;
        throw new Exception($mensagem);
    endif;
    $tutorial_card_cor = $dados_pagina['cor'];
    
    if(!isset($dados_pagina['personalizada'])):
        $mensagem = "Requisição inválida! Parâmetros faltando! " . ($depurar ? "personalizada" : "") ;
        throw new Exception($mensagem);
    endif;
    $tutorial_card_personalizada = $dados_pagina['personalizada'];
    
    /*
    if(!isset($dados_pagina['card_ordem'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_card_ordem = $dados_pagina['card_ordem'];
    */
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    $tabela = new TabelaTutorial($cn);
    
    //Campos ordem e permissao continuam como anteriormente
    //$reg_dados_anteriores = new RegistroTutorial();
    $registro = $tabela->encontrar_registro($tutorial_id);
    if($registro == null):
        $mensagem = "O registro não foi encontrado!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: Esse card/tutorial pertence mesmo ao autor?
    $autor_id_tut = $registro->getAutorID();
    if($_SESSION['autor_id'] != $autor_id_tut):
        $mensagem = "Erro! Você não possui autoria sob este tutorial!";
        $pagina = "/expositor.php";
        throw new Exception($mensagem);
    endif;
    
    //$registro->setTutorialID($tutorial_id);
    $registro->setTutorialCardCor($tutorial_card_cor);
    $registro->setTutorialCardNome($tutorial_card_nome);
    $registro->setTutorialCardPersonalizada($tutorial_card_personalizada);
    $registro->setTutorialCardPredefinida($tutorial_card_predefinida);
    $registro->setTutorialCardTexto($tutorial_card_texto);
    $registro->setTutorialCardTipo($tutorial_card_tipo);
    $tabela->salvar($registro);
    
    // inicio - Cria ou atualiza o tópico principal
    // usos
    // card/json/card_json_salvar.php
    // expositor/json/expositor_json_id_novo_tutorial.php
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    $tabelaTopico = new TabelaTopico($cn);
    $consultas = new TopicoConsultas($cn);
    
    $topic_id = $consultas->topico_principal($tutorial_id);
    
    $registro_principal = new RegistroTopico();
    $registro_principal->setTopicoConteudo(null);
    $registro_principal->setTopicoID($topic_id);
    $registro_principal->setTopicoNivel(0);
    $registro_principal->setTopicoNome($tutorial_card_nome);
    $registro_principal->setTopicoOrdem(0);
    $registro_principal->setTopicoSuperior(null);
    $registro_principal->setTutorialID($tutorial_id);
    $tabelaTopico->salvar($registro_principal);
    //fim
    
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



