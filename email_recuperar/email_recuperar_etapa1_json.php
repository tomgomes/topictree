<!-- Envia um código para o e-mail da pessoa que esqueceu a senha -->
<!-- NOTA: A chamada vem de login.js-ajax_esqueci_senha() -->
<?php
header('Content-type: application/json; charset=utf-8');
try {
    
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/email/email.inc";
    
    $pagina="";
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
    
    if (!isset($dados_pagina['username'])):
        
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $usuario = $dados_pagina['username'];
    
    if(empty($usuario)):
        $mensagem = "Por favor, informe o seu nome de usuário.";
        throw new Exception($mensagem);        
    endif;
    
    //Gera e salva código no banco de dados
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $consulta = new AutorConsultas($cn);    
    $registro = $consulta->encontrar_registro_por_usuario($usuario);
    if($registro == null):
        $mensagem = "Erro! Este nome de usuario não existe!";
        throw new Exception($mensagem);
    endif;    
    $tabela = new TabelaAutor($cn);
    $codigo = rand(100000, 999999);
    $registro->setAutorEmailCodigo($codigo);
    $tabela->salvar_registro($registro);
    $nome = $registro->getAutorNome();
    $email = $registro->getAutorEmail();
    $_SESSION['autor_usuario'] = $registro->getAutorUsuario();
    
    //Envia o código por e-mail para o usuário    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    //Obtém e-mail modelo
    $caminho = $_SERVER["DOCUMENT_ROOT"] . "/email_recuperar/email_recuperar_modelo.php";
    $body = file_get_contents($caminho);
    
    //Inseri o nome
    $body = str_replace("{autor_nome}", $nome, $body);
    
    //Inseri o código
    $body = str_replace("{autor_email_codigo}", $codigo, $body);
    
    $mail->Body = $body;    
    $mail->IsSMTP();
    
    $mail->Subject = "Res: Esqueci a senha - topictree.app.br";
    
    //$mail->SetFrom("suporte@topictree.eti.br");
    $mail->SetFrom("cadastro@topictree.app.br");
    
    //$mail->Host = "mail.topictree.eti.br";
    $mail->Host = "smtp.umbler.com";
    
    //$mail->Username = "suporte@topictree.eti.br";
    $mail->Username = "cadastro@topictree.app.br";
    
    //$mail->Password = "????????????????";
    $mail->Password = "??????????????????????";   
    
    $mail->SMTPAuth = true;
    
    //$mail->SMTPSecure = 'ssl'; O umbler não usa SSL no SMTP
    
    //$mail->Port = 465;
    $mail->Port = 587;
    
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->IsHTML(true);
    $mail->AddAddress($email);
    if(!$mail->Send()):
        $mensagem = "Falha no envio do e-mail.<br/>" . $mail->ErrorInfo;
        throw new Exception($mensagem);
    endif;
    
    //Obtém página inicial (link será usado pelo JavaScript)
    $pagina = $_SERVER["HTTP_HOST"] . '/email_recuperar/email_recuperar.php';
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
