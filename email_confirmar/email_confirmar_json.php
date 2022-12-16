
<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    //Obtém página inicial (link será usado pelo JavaScript)        
    $pagina = $_SERVER["HTTP_HOST"] . '/login.php';
    $pagina = addhttp($pagina);
    
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    //require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    
    $mensagem = "";
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    //Se contiver entities, então converte, por exemplo, &nbsp; é convertido para o caracter espaço ' '    
    $query_string = html_entity_decode($_POST['query_string']);
    
    //Cada parâmetro da querystring, ou seja, &param= será um elemento do array associativo
    $dados_pagina = "";    
    parse_str($query_string, $dados_pagina);
    
    if (!isset($dados_pagina['guid'])):        
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $guid = $dados_pagina['guid'];
    
    if(empty($guid)):
        $mensagem = "Informe o GUID recebido para confirmar o e-mail.";
        throw new Exception($mensagem);        
    endif;
    
    if(mb_strlen($guid, "UTF-8") > 38):
        $mensagem = "Erro! GUID possui mais que 36 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $consulta = new AutorConsultas($cn);
    
    $registro = $consulta->encontrar_registro_por_guid($guid);
    if($registro == null):
        $mensagem = "Erro! O cadastro não foi localizado!";
        throw new Exception($mensagem);
    else:
        if($registro->getAutorEmailVerificado()=='S'):
            $mensagem = "Seu e-mail já foi anteriormente confirmado!<br/>Efetue o seu <i>login</i>.";
        else:
            $tabela = new TabelaAutor($cn);        
            $registro->setAutorEmailVerificado('S');
            $tabela->salvar_registro($registro);
            $mensagem = "E-mail confirmado!<br/>Efetue o seu <i>login</i>.";            
        endif;    
    endif;
        
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
ob_end_clean();
echo json_encode($resposta);
?>
