
<?php
header('Content-type: application/json; charset=utf-8');
try {
        
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    //require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    
    $pagina = "";
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
    
    if(!isset($_SESSION['autor_usuario'])):
        $pagina = $_SERVER["HTTP_HOST"] . '/login.php';
        $pagina = addhttp($pagina);
    
        $mensagem = "Erro! Sessão expirou ou dados da sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $usuario = $_SESSION['autor_usuario'];
    
    if (!isset($dados_pagina['codigo'])
        || !isset($dados_pagina['senha'])
        || !isset($dados_pagina['senha_repetida'])):
        
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $codigo = $dados_pagina['codigo'];
    $senha = $dados_pagina['senha'];
    $senha_repetida = $dados_pagina['senha_repetida'];
    
    if(empty($codigo)):
        $mensagem = "Informe o código recebido pelo e-mail.";
        throw new Exception($mensagem);        
    endif;
    
    if(empty($senha)):
        $mensagem = "Por favor, informe uma senha.";
        throw new Exception($mensagem);
    endif;
    
    if(mb_strlen($senha, "UTF-8") < 8):
        $mensagem = "Erro! A senha deve ter pelo menos 8 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    if(empty($senha_repetida)):
        $mensagem = "Por favor, repita a senha.";
        throw new Exception($mensagem);
    endif;
    
    if($senha != $senha_repetida):
        $mensagem = "As senhas não são iguais, informe-as novamente.";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $consulta = new AutorConsultas($cn);
    
    $registro = $consulta->encontrar_registro_por_usuario($usuario);
    if($registro == null):
        $mensagem = "Erro! Usuário não localizado!";
        throw new Exception($mensagem);
    endif;
    $codigo_db = $registro->getAutorEmailCodigo();
    
    if($codigo != $codigo_db):
        $mensagem = "Código incorreto!";
        throw new Exception($mensagem);
    endif;
    
    //Altera e salva registro
    $registro->setAutorAssinaturaSenha($senha);
    $registro->setAutorEmailCodigo(null);
    $tabela = new TabelaAutor($cn);
    $tabela->salvar_registro($registro);
    
    $mensagem = "Sua senha foi alterada com sucesso!";
    
    //Obtém página inicial (link será usado pelo JavaScript)
    $pagina = $_SERVER["HTTP_HOST"] . '/login.php';
    $pagina = addhttp($pagina);
    
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
