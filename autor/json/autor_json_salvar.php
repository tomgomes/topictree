<?php
header('Content-type: application/json; charset=utf-8');

try {        
    $pagina = "";
    
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    
    $mensagem = "";
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    //Se tiver &nbsp; converte para o caracter espaço ' '    
    $query_string = html_entity_decode($_POST['query_string']);
    
    //Cada parâmetro da querystring, ou seja, &param= será um elemento do array associativo
    $dados_pagina = "";    
    parse_str($query_string, $dados_pagina);
    
    if (!isset($dados_pagina['nome'])
        || !isset($dados_pagina['sobrenome'])
        || !isset($dados_pagina['datanascimento'])
        || !isset($dados_pagina['email'])
        || !isset($dados_pagina['usuario'])
        || !isset($dados_pagina['senha'])
        || !isset($dados_pagina['senha_repetida'])):
        
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    $nome = $dados_pagina['nome'];
    $sobrenome = $dados_pagina['sobrenome'];
    $data_nascimento = $dados_pagina['datanascimento'];
    $email = $dados_pagina['email'];    
    $usuario = $dados_pagina['usuario'];
    $senha = $dados_pagina['senha'];
    $senha_repetida = $dados_pagina['senha_repetida'];
    
    if(empty($nome)):
        $mensagem = "Por favor, informe o seu nome.";
        throw new Exception($mensagem);        
    endif;
    
    if(mb_strlen($nome, "UTF-8") > 25):
        $mensagem = "Erro! Nome possui mais que 25 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    if(empty($sobrenome)):
        $mensagem = "Por favor, informe o seu sobrenome.";
        throw new Exception($mensagem);
    endif;
    
    if(mb_strlen($sobrenome, "UTF-8") > 50):
        $mensagem = "Erro! Sobrenome possui mais que 50 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    if(empty($data_nascimento)):
        $mensagem = "Por favor, informe a sua data de nascimento.";
        throw new Exception($mensagem);
    endif;
    
    $array_data = explode('/', $data_nascimento);    
    if(!checkdate($array_data[1], $array_data[0], $array_data[2])): //mes, dia, ano
        $mensagem = "Por favor, informe uma data válida.";
        throw new Exception($mensagem);
    endif;
    $data_mysql = $array_data[2] . "/" . $array_data[1] . "/" . $array_data[0];
    
    if(empty($email)):
        $mensagem = "Por favor, informe o seu e-mail.";
        throw new Exception($mensagem);
    endif;
    
    if(mb_strlen($email, "UTF-8") > 50):
        $mensagem = "Erro! E-mail possui mais que 50 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)):
        $mensagem = "Por favor, informe um e-mail válido.";
        throw new Exception($mensagem);
    endif;
    
    if(empty($usuario)):
        $mensagem = "Por favor, informe um nome de usuário.";
        throw new Exception($mensagem);
    endif;
    
    if(mb_strlen($usuario, "UTF-8") > 15):
        $mensagem = "Erro! Usuário possui mais que 15 caracteres.";
        throw new Exception($mensagem);
    endif;
    
    //ASSINAR SENHA
    
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
    $tabela = new TabelaAutor($cn);
    
    $existe = $tabela->ja_existe($usuario);
    //$existe = false; //teste
    if($existe):
        $mensagem = "Esse nome de usuário já existe.<br/>Por favor, digite um outro nome de usuário.";
        throw new Exception($mensagem);        
    endif;
    
    //Obtém página inicial (link será usado pelo JavaScript)
    $pagina = $_SERVER["HTTP_HOST"] . '/login.php';
    $pagina = addhttp($pagina);
    
    $consultas = new AutorConsultas($cn);
    $existe = $consultas->ja_existe_email($email);
    //$existe = false; //teste
    if($existe):
        $mensagem = "E-mail já cadastrado. Você já possui uma conta.<br/>Por favor, apenas efetue o seu <i>login</i>.";
        throw new Exception($mensagem);
    endif;
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    
    //Obtém e-mail modelo
    $caminho = $_SERVER["DOCUMENT_ROOT"] . "/email_confirmar/email_modelo.php";    
    $body = file_get_contents($caminho);
    
    //Inseri o nome
    $body = str_replace("{autor_nome}", $nome, $body);
    
    //Inseri o link    
    //$guid = com_create_guid(); //Essa função não existe no servidor compartilhado =\
    $guid = $consultas->gerar_guid();
    $link = $_SERVER["HTTP_HOST"] . '/email_confirmar/email_confirmar.php?guid=' . $guid;
    $link = addhttp($link);
    $body = str_replace("{link}", $link, $body);
    
    
    $mail->Subject = "Confirmação de cadastro - topictree.eti.br";
    $mail->Body = $body;
    
    $mail->IsSMTP();
    
    //$mail->SetFrom("cadastro@topictree.eti.br");
    $mail->SetFrom("cadastro@topictree.app.br");
    
    //$mail->Host = "mail.topictree.eti.br";
    $mail->Host = "smtp.umbler.com";
    
    //$mail->Username = "cadastro@topictree.eti.br";
    $mail->Username = "cadastro@topictree.app.br";
    
    //$mail->Password = "????????????????";   
    $mail->Password = "??????????????????????";   
    
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;
    //$mail->SMTPSecure = 'ssl'; O umbler não usa SSL no SMTP
    
    //$mail->charSet = "UTF-8";
    $mail->CharSet = 'UTF-8';
    
    //$mail->Port = 465;
    $mail->Port = 587;
    
    $mail->IsHTML(true);
    $mail->AddAddress($email);
    if(!$mail->Send()):
        $mensagem = "Falha no envio do e-mail.<br/>" . $mail->ErrorInfo;
        throw new Exception($mensagem);                
    endif;
    
    $registro = new RegistroAutor();
    $registro->setAutorNome($nome);
    $registro->setAutorSobrenome($sobrenome);
    $registro->setAutorDataNascimento($data_mysql);
    $registro->setAutorEmail($email);
    $registro->setAutorEmailGuid($guid);
    $registro->setAutorEmailVerificado('N');
    $registro->setAutorUsuario($usuario);
    $registro->setAutorAssinaturaSenha($senha);
    $registro->setAutorAvisoAddCard(0);
    $registro->setAutorAvisoMoverCard(0);
    $tabela->salvar_registro($registro);
    
    $mensagem = "Cadastro realizado com sucesso!<br/>Um <i>link</i> de confirmação foi enviado para o seu e-mail.";
    
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



