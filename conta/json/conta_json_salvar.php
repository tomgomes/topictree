<?php
header('Content-type: application/json; charset=utf-8');
try {        
    $pagina = "";
    
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
    require $_SERVER["DOCUMENT_ROOT"] . "/db/autor/autor_consultas.inc";
    
    //require $_SERVER["DOCUMENT_ROOT"] . "/vendor/autoload.php";
    
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
        ):
        
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    $nome = $dados_pagina['nome'];
    $sobrenome = $dados_pagina['sobrenome'];
    $data_nascimento = $dados_pagina['datanascimento'];
    $email = $dados_pagina['email'];    
    $senha = $dados_pagina['senha'];
    
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
        
    //se a senha estiver vazia, é para manter a mesma
    if(empty($senha)):
    else:
        if(mb_strlen($senha, "UTF-8") < 8):
            $mensagem = "Erro! A senha deve ter pelo menos 8 caracteres.";
            throw new Exception($mensagem);
        endif;    
    endif;
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    $tabela = new TabelaAutor($cn);
    
    $registro = $tabela->encontrar_registro($_SESSION['autor_usuario']);
    if($registro == null){
        $mensagem = "Falha ao encontrar usuário!";
        throw new Exception($mensagem);        
    }//end if
    
    //Se o email mudar, confirmar novamente?
    
    //$registro = new RegistroAutor();
    $registro->setAutorNome($nome);
    $registro->setAutorSobrenome($sobrenome);
    $registro->setAutorDataNascimento($data_mysql);
    $registro->setAutorEmail($email);    
    //$registro->setAutorEmailGuid($guid);
    $registro->setAutorEmailVerificado('S');    
    $registro->setAutorUsuario($_SESSION['autor_usuario']);
    
    if(empty($senha)):
        //manter a mesma assinatura
    else:
        //ASSINAR SENHA AQUI
        
        $registro->setAutorAssinaturaSenha($senha);
    endif;
    
    //$registro->setAutorAvisoAddCard(0);
    //$registro->setAutorAvisoMoverCard(0);
    $tabela->salvar_registro($registro);
    
    $mensagem = "Conta atualizada com sucesso!";
    
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



