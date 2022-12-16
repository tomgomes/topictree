<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    $pagina = "";
    
    session_start();
    $path = session_save_path() . '/sess_' . session_id();
    chmod($path, 0777);
    chown($path, 'root');
    chgrp($path, 'root');

    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    $pagina = "";
    $dados_formulario = "";
    $mensagem = "";
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_formulario);
    
    if(!isset($dados_formulario['username'])
    or !isset($dados_formulario['password'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $usuario = $dados_formulario['username'];
    $senha = $dados_formulario['password'];
    
    if(empty($dados_formulario['username'])
    or empty($dados_formulario['password'])):
        $mensagem = "Por favor, informe o usuário e a senha.";
        throw new Exception($mensagem);
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    $consultas = new AutorConsultas($cn);    
    $registro = $consultas->login($usuario, $senha);
    
    if($registro == null):
        $_SESSION['autenticado'] = false;
        //$pagina = 'index.php';
        $mensagem = "Usuário ou senha não reconhecidos.";
    else:
        $tabela = new TabelaAutor($cn);
        $registro = $tabela->encontrar($usuario);
        if($registro['autor_email_verificado'] == 'N'):
            $_SESSION['autenticado'] = false;
            //$pagina = 'index.php';
            $mensagem = "Por favor, acesse o seu e-mail e confirme o seu cadastro.";        
        else:
            $_SESSION['autor_id'] = $registro['autor_id'];
            $_SESSION['autor_usuario'] = $registro['autor_usuario'];
            $_SESSION['autenticado'] = true;
            //$pagina = 'expositor.php?t=' .  filemtime($_SERVER["DOCUMENT_ROOT"] . '/expositor.php');
            $pagina = 'menu.php?t=' .  filemtime($_SERVER["DOCUMENT_ROOT"] . '/menu.php');
        endif;
    endif;
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['pagina'] = $pagina;
$resposta['mensagem'] = $mensagem;
ob_end_clean();
echo json_encode($resposta);
?>
