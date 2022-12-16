<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    $mensagem = "";
    $pagina = "";
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
    
    if(!isset($_SESSION['autor_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;    
    $dados_pagina = "";
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['acao'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $acao = $dados_pagina['acao'];
    
    if( ($acao != 'ocultar') && ($acao != 'mostrar')):
        $mensagem = "A ação deve ser 'ocultar' ou 'mostrar'.";
        throw new Exception($mensagem);    
    endif;
    
    if(!isset($dados_pagina['topico_id'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    $topico_id = $dados_pagina['topico_id'];
    
    $banco = new BancoDeDados();    
    $cn = $banco->conectar();             
    if($acao == 'ocultar')
    {
        $consulta = new TopicoConsultas($cn);
        $consulta->topico_ocultar($topico_id, $acao);
    }
    
    if($acao == 'mostrar')
    {
        $tabela = new TabelaTopico($cn);
        $reg_topico = $tabela->encontrar_registro($topico_id);
        
        if(is_null($reg_topico))
        {
            $mensagem = "Erro! O tópico com o ID $topico_id não foi encontrado!";
            throw new Exception($mensagem);
        }//end if

        if($reg_topico->getTopicoOculto() == 'S')    
        {
            $valor = 'N';
            $reg_topico->setTopicoOculto($valor);
            $tabela->salvar($reg_topico);
        }//end if
        
        while(!is_null($reg_topico->getTopicoSuperior()))
        {
            $reg_topico = $tabela->encontrar_registro($reg_topico->getTopicoSuperior());
            
            if(is_null($reg_topico))
            {
                $mensagem = "Erro! O tópico com o ID $topico_id não foi encontrado!";
                throw new Exception($mensagem);
            }//end if
            
            if($reg_topico->getTopicoOculto() == 'S')    
            {
                $valor = 'N';
                $reg_topico->setTopicoOculto($valor);
                $tabela->salvar($reg_topico);
            }//end if
        }//end while
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
