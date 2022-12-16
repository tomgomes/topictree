<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    $pagina = "";
    $mensagem = "";
    if(!isset($_SESSION['autenticado'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    if(!$_SESSION['autenticado']):
        $mensagem = "Operação não autorizada!";
        throw new Exception($mensagem);
    endif;
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    $topico_ordem = -1;
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['topico_superior'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['posicao'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['hierarquia'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['topico_selecionado'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Dados na requisição estão faltando!";
        throw new Exception($mensagem);
    endif;    
    $tutorial_id = $dados_pagina['tutorial_id'];
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();        
    $tabela = new TabelaTopico($cn); 
    
    //SEGURANÇA: Esse tópico superior é realmente do usuário?
    //SEGURANÇA: Esse tópico selecionado é realmente do usuário?
    
    if($dados_pagina['hierarquia'] == "filho"){
        //DEFINIR O NÍVEL            
        $reg_topico_superior = new RegistroTopico();
        //Procura quem é o tópico superior
        $reg_topico_superior = $tabela->encontrar_registro($dados_pagina['topico_superior']);
        //Se encontrou
        if($reg_topico_superior != null){
            $nivel = $reg_topico_superior->getTopicoNivel();
            //Um nível a mais de profundidade
            $nivel++;
        }else{
            $mensagem = "Não foi possível determinar o nível para o novo tópico!";
            throw new Exception($mensagem);
        }//end if
        
        //DEFINIR ORDEM            
        $consulta = new TopicoConsultas($cn);
        $topico_ordem = $consulta->ordem_ultimo_topico($dados_pagina['topico_superior']);  
        $topico_ordem++;
        
        $reg_filho = new RegistroTopico();
        $reg_filho->setTutorialID($tutorial_id);
        $reg_filho->setTopicoSuperior($dados_pagina['topico_superior']);
        $reg_filho->setTopicoID($dados_pagina['topico_id']);
        $reg_filho->setTopicoOrdem($topico_ordem);
        $reg_filho->setTopicoNivel($nivel);
        $reg_filho->setTopicoNome('Novo tópico');
        $tabela->salvar($reg_filho);            
    }//end if
    
    if($dados_pagina['hierarquia'] == "antes" || $dados_pagina['hierarquia'] == "apos"){
        
        $reg_topico_selecionado = new RegistroTopico();
        $reg_topico_selecionado = $tabela->encontrar_registro($dados_pagina['topico_selecionado']);
        if($reg_topico_selecionado != null){            
            //Defini ordem
            $topico_ordem = $reg_topico_selecionado->getTopicoOrdem();
            if($dados_pagina['hierarquia'] == "apos"){
                $topico_ordem++;
            }//end if
        }else{
            $mensagem = "Não foi possível determinar o nível para o novo tópico!";
            throw new Exception($mensagem);
        }//end if
        
        $reg_apos = new RegistroTopico();
        $reg_apos->setTutorialID($reg_topico_selecionado->getTutorialID());
        $reg_apos->setTopicoSuperior($reg_topico_selecionado->getTopicoSuperior());
        $reg_apos->setTopicoID($dados_pagina['topico_id']);
        $reg_apos->setTopicoOrdem($topico_ordem);
        $reg_apos->setTopicoNivel($reg_topico_selecionado->getTopicoNivel());
        $reg_apos->setTopicoNome('Novo tópico');
        
        $consulta = new TopicoConsultas($cn);
        $consulta->adicionar_organizar($reg_apos);
    }//end if
            
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
