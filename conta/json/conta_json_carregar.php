<?php
// require "../include/restrito.inc";
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
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
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_registro.inc";
    
    $mensagem = "";
    $pagina = "";
    
    if(!isset($_SESSION['autor_usuario'])):
        $pagina = "/index.php";
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $autor_usuario = $_SESSION['autor_usuario'];
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();        
    $tabela = new TabelaAutor($cn);
    $reg_autor = $tabela->encontrar_registro($autor_usuario);
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
} finally {
    $cn = null;
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['pagina'] = $pagina;
$resposta['mensagem'] = $mensagem;

if(isset($reg_autor)):
    $resposta['autor_usuario'] = $reg_autor->getAutorUsuario();
    $resposta['autor_nome'] = $reg_autor->getAutorNome();
    $resposta['autor_sobrenome'] = $reg_autor->getAutorSobrenome();    
    $resposta['autor_datanascimento'] = $reg_autor->getAutorDataNascimento();    
    $resposta['autor_email'] = $reg_autor->getAutorEmail();
    //senha (?)
endif;

ob_end_clean();
echo json_encode($resposta);
?>
