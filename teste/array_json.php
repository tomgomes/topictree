<?php
    
    //$fora = array("dado1", "dado2", "dado3");
    //$dentro = array("c1", "c2", "c3");
    //$teste2 = array_merge($fora, $dentro);
    

    //$nomeArrayPai = "raiz";
    $raiz = array();    
    
    arvoreMaluca($raiz, 0, 0, "Referência");
    arvoreMaluca($raiz, 0, 1, "Ferramentas");
    arvoreMaluca($raiz, 1, 2, "PHP");
    arvoreMaluca($raiz, 2, 3, "Referência");
    arvoreMaluca($raiz, 2, 4, "Baixar");
    
    
    //$resposta[] = $raiz;
    
    
    header('Content-type: application/json; charset=utf-8');
    
    //echo json_encode($resposta);
    
    function arvoreMaluca(&$raiz, $idpai, $id, $nome){
        $no = array(array($idpai, $id, $nome));
        
        if($idpai == 0){
            $raiz = array_merge($raiz, $no);
            return;
        }
        
        foreach($raiz as $elemento){
            //Se encontrou o id pai 
            if($elemento[1] == $idpai){
                $elemento[2] = array_merge($elemento[2], $no);
            }//end if            
        }//end foreach
    }//end function
    
    function criarArray($nome, array $elementos){
        $novoArray = $nome;        
        global $$novoArray;
        $$novoArray = $elementos;
        return $$novoArray;
    }
?>








