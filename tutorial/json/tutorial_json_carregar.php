<?php
header('Content-type: application/json; charset=utf-8');
try {
    session_start();
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/database.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_registro.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/topico/topico_consultas.inc";
    
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_tabela.inc";
    require $_SERVER["DOCUMENT_ROOT"] . "/db/tutorial/tutorial_registro.inc";
    
    $mensagem = "";
    $pagina = "";
    $nivel_maximo = 0;
    $reg_tutorial = null;
    $rs_arvore = array();
    
    $dados_pagina = "";
    if(!isset($_POST['query_string'])):
        $mensagem = "Requisição inválida! Parâmetros faltando!";
        throw new Exception($mensagem);
    endif;
    
    if(!isset($_SESSION['visitante'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    
    parse_str($_POST['query_string'], $dados_pagina);
    
    if(!isset($dados_pagina['tutorial_id'])):
        $mensagem = "Dados na sessão estão faltando!";
        throw new Exception($mensagem);
    endif;
    $tutorial_id = $dados_pagina['tutorial_id'];
    
    //SEGURANÇA: O tutorial é realmente do autor?
    
    //SEGURANÇA: É um tutorial SE, ou seja, somente para autor (Somente Eu)?
    
    $banco = new BancoDeDados();
    $cn = $banco->conectar();
    
    /*
    $consultas = new TopicoConsultas($cn);
    $topico_principal = $consultas->topico_principal($tutorial_id);
    if($topico_principal == null):    
        $pagina = "/card_editar.php?card_id=" . $tutorial_id;        
        $mensagem = "O card não possui um nome.<br/>" .
                    "Dê um nome e clique no botão Salvar.";
        throw new Exception($mensagem);
    endif;
    */
    
    $tabela = new TabelaTutorial($cn);
    $reg_tutorial = $tabela->encontrar_registro($tutorial_id);
    if($reg_tutorial == null):
        $mensagem = "Falha ao localizar o tutorial!";
        throw new Exception($mensagem);
    endif;
    
    //SEGURANÇA: Esse tutorial pertence mesmo ao autor?
    $autor_id_tut = $reg_tutorial->getAutorID();
    $permissao = $reg_tutorial->getTutorialCardPermissao();
    $nome = $reg_tutorial->getTutorialCardNome();
    
    if ($_SESSION['visitante']):
        
        if($_SESSION['autor_id_para_visitante'] != $autor_id_tut):
            $mensagem = "Erro! O autor informado não possui autoria sob o tutorial requisitado!";
            throw new Exception($mensagem);
        endif;
        
        //SEGURANÇA: O visitante está tentando acessar um tutorial privado?
        if($permissao == "SE"):
            $mensagem = "Erro! Este tutorial ($nome) é privado! <br/>" 
                      . "Se você é autor deste, <br/>" 
                      . "efetue o login e o escolha no expositor.";
            $pagina = "/login.php";
            throw new Exception($mensagem);
        endif;
        
        //SEGURANÇA: O visitante está tentando acessar um tutorial com senha?
        if($permissao == "TCS"):
            if(!isset($_SESSION['tutorial_autenticado'])):
                $mensagem = "Erro! Este tutorial ($nome) exige uma senha! <br/>" . 
                            "Escolha este tutorial no expositor e a informe.";
                $pagina = "/expositor.php?aut=" . $autor_id_tut;
                throw new Exception($mensagem);
            endif;
        endif;
        
    else:
        
        //SEGURANÇA: O autor está tentando acesso a um tutorial que não é dele?
        if($_SESSION['autor_id'] != $autor_id_tut):
            $mensagem = "Erro! Você não possui autoria sob este tutorial!";
            $pagina = "/expositor.php";
            throw new Exception($mensagem);
        endif;
        
    endif;
    
    if ($_SESSION['visitante']):
        //Exemplo, select topico_superior, topico_id, topico_nivel, topico_nome, topico_aberto, topico_oculto from sgc_topico where tutorial_id = 15 order by topico_ordem
        $sql_arvore = "select topico_superior, topico_id, topico_nivel, topico_nome, topico_aberto, topico_oculto " .
            "from sgc_topico " .
            "where tutorial_id = :tutorial_id and topico_oculto = 'N' " .
            "order by topico_ordem";
    else:
        //Exemplo, select topico_superior, topico_id, topico_nivel, topico_nome, topico_aberto, topico_oculto from sgc_topico where tutorial_id = 15 order by topico_ordem
        $sql_arvore = "select topico_superior, topico_id, topico_nivel, topico_nome, topico_aberto, topico_oculto " .
            "from sgc_topico " .
            "where tutorial_id = :tutorial_id " .
            "order by topico_ordem";
    endif;
    
    $pstmt_arvore = $cn->prepare($sql_arvore);
    $pstmt_arvore->bindParam(':tutorial_id', $tutorial_id);
    $pstmt_arvore->execute();
    $rs_arvore = $pstmt_arvore->fetchAll(PDO::FETCH_ASSOC);
    
    //Exemplo, select max(topico_nivel) nivel_maximo from sgc_topico where tutorial_id = 15 order by topico_ordem
    $sql_nivel_maximo = "select max(topico_nivel) nivel_maximo " .
        "from sgc_topico " .
        "where tutorial_id = :tutorial_id " .
        "order by topico_ordem ";
    $pstmt_nivel_maximo = $cn->prepare($sql_nivel_maximo);
    $pstmt_nivel_maximo->bindParam(':tutorial_id', $tutorial_id);
    $pstmt_nivel_maximo->execute();
    $nivel_maximo = $pstmt_nivel_maximo->fetchColumn();
    
} catch (Exception $ex) {
    $mensagem = $ex->getMessage();
}//end try

$resposta = array();
$resposta['buffer'] = ob_get_contents();
$resposta['mensagem'] = $mensagem;
$resposta['arvore_topicos'] = $rs_arvore;
$resposta['nivel_maximo'] = $nivel_maximo;
$resposta['pagina'] = $pagina;
if($reg_tutorial!= null):
    $resposta['arvore'] = $reg_tutorial->getTutorialLarguraArvore();
    $resposta['editor'] = $reg_tutorial->getTutorialLarguraEditor();
endif;
ob_end_clean();
echo json_encode($resposta);
?>


