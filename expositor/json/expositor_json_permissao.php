<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    $depurar = true;
    $tutorial_id = "";
    $mensagem = "";
    $pagina = "";
    $dados_pagina = "";    
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!" . ($depurar ? 'query_string' : '');
        throw new Exception($mensagem);
    endif;
    
    //Se tiver &nbsp; converte para o caracter espaço ' '    
    $query_string = html_entity_decode($_POST['query_string']);
    //Cada parâmetro da querystring, ou seja, &param= será um elemento nomeado do array 
    parse_str($query_string, $dados_pagina);
    
    if(!isset($dados_pagina['senha'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!" . ($depurar ? 'senha' : '');
        throw new Exception($mensagem);
    endif;
    $senha = $dados_pagina['senha'];
    
    if(!isset($dados_pagina['card_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!" . ($depurar ? 'card_id' : '');
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['card_id'];
    
    /*
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!" . ($depurar ? 'autor_id' : '') . $_SESSION['VISITANTE'] ;
        throw new Exception($mensagem);
    endif;
    */
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();        
    $tabela = new TabelaTutorial($cn);
    $card = $tabela->encontrar($tutorial_id);
    
    if($card==null):
        $mensagem = "Erro! Registro não encontrado!";
        throw new Exception($mensagem);
    endif;
    
    //Regras para visitante. Somente visitante chama esta página.
    
    //Senha em branco significa que não sabe se o tutorial exige senha ou não
    if (empty($senha)):
        if($card['tutorial_card_permissao'] == "TCS"):
            //Responde que é para pedir senha
            $_SESSION['tutorial_autenticado'] = null;
            $mensagem = "pedir_senha";
            $tutorial_card_nome = $card['tutorial_card_nome'];
        elseif($card['tutorial_card_permissao'] == "T"):
            //Não precisa de senha, pois é para T - Todos 
            $pagina = 'tutorial.php?aut=' . $_SESSION['autor_id_para_visitante']
                    . '&tut=' . $tutorial_id 
                    . '&t=' . filemtime($_SERVER["DOCUMENT_ROOT"] . '/tutorial.php');        
            $_SESSION['tutorial_autenticado'] = $tutorial_id;
            
            //Nota: Em teoria não teremos o caso (SE - Somente Eu) pois estes tutoriais não serão mostrados para os visitantes
        endif;
    else:
        //Se há uma senha, então foi informado que é para pedir a senha, 
        //o usuário digitou e a caixa de diálogo senha que fez esta chamada
        //e aqui estamos
        if($senha == $card['tutorial_card_senha']):
            $pagina = 'tutorial.php?aut=' . $_SESSION['autor_id_para_visitante'] 
                    . '&tut=' . $tutorial_id 
                    . '&t=' . filemtime($_SERVER["DOCUMENT_ROOT"] . '/tutorial.php');        
            $_SESSION['tutorial_autenticado'] = $tutorial_id;
        else:
            $_SESSION['tutorial_autenticado'] = null;
            
            $mensagem = "Erro! Senha incorreta!";
            throw new Exception($mensagem);
        endif;
    endif;

} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['pagina'] = $pagina;
$resposta['tutorial_card_nome'] = $tutorial_card_nome;
ob_end_clean();
echo json_encode($resposta);
?>



