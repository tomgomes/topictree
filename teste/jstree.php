<!DOCTYPE html>
<html>
    <head>        
        <title>Topic Tree - SGC - Sistema de Gerenciamento de Conteúdo</title>
        <?php
        require "../include/meta.inc";
        require "../include/bootstrap.inc";
        require "../include/jquery_ui.inc";
        require "../include/plugins.inc";
        ?>        
        <link rel="stylesheet" type="text/css" href="../plugin/jquery-jstree/themes/default/style.min.css">
        <link rel="stylesheet" type="text/css" href="../css/aguarde.css?ultimaAtualizacao=<?php echo filemtime('../css/aguarde.css')?>">
        
        <script src="../plugin/jquery-jstree/jstree.min.js"></script>
        <script>
            
            $(document).ready(function () {        
            	ajax_request_montar_arvore_topicos();
                
            	//create an instance when the DOM is ready
                $('#jstree').jstree(
                    { core: { check_callback: function (op, node, par, pos, more) {
                    	                          if(op === "rename_node") {                                                        
                                                      //return ajax_resquest_renomear(node.id, pos);
                                                      return true;
                    	                          }else if(op === "create_node"){
                                                      return true;                                                      
                                                  }else{
                                                      return true;
                                                  }//end if
                                              }//end function
                         
                    }//end core          
                    , plugins: ["contextmenu"]
                    , contextmenu: {         
                          items: function($node) {
                                var tree = $('#jstree').jstree(true);
                                return { 
                                          CriarFilho: { separator_before: false
                                            , separator_after: false
                                            , label: "+ Filho"
                                            , action: function (obj) { 
                                                          $node = tree.create_node($node);
                                                          tree.edit($node);
                                                      }//end function
                                          }//end create
                                        , CriarApos: { separator_before: false
                                                       , separator_after: false
                                                       , label: "+ Após"
                                                       , action: function (obj) { 
                                                                     $node = tree.create_node($node);
                                                                     tree.edit($node);
                                                                 }//end function
                                                     }//end create
                                        , CriarAntes: { separator_before: false
                                             , separator_after: false
                                             , label: "+ Antes"
                                             , action: function (obj) {
                                                           $node = tree.create_node($node);                                                           
                                                           tree.edit($node);
                                                       }//end function
                                           }//end create
                                       , Renomear: { separator_before: true
                                                   , separator_after: false
                                                   , label: "Renomear"
                                                   , action: function (obj) { 
                                                                 tree.edit($node);
                                                             }//end function
                                                 }//end rename
                                       , Apagar: { separator_before: false
                                                   , separator_after: false
                                                   , label: "Apagar"
                                                   , action: function (obj) { 
                                                                 tree.delete_node($node);
                                                             }//end function
                                                 }//end remove
                                       };//end return
                           }//end function
                      }//end contextmenu
                });//end jstree
                
            	$(".panel-left").resizable({
                    resizeHeight: false
                });


                //Quando clicar no 'botão mover para cima'
                $('#buttonMoverCima').on('click', function(){
                    //limpa qualquer aviso dado anteriormente
                    $('#h2Aviso').html('');
                    
                    arrayIDs = $.jstree.reference('#jstree').get_selected(false);
                    //arrayIDs = $('#divArvore').get_selected(false);
                    
                    if (arrayIDs.length === 0){
                        $('#h2Aviso').html('Por favor, escolha um nó!');
                    }else if(arrayIDs.length > 1){
                        $('#h2Aviso').html('Por favor, escolha somente um nó!');
                    }else{
                        strId = arrayIDs[0];
                        strSeletor = '#' + strId;
                        nodeSelecionado = $(strSeletor);
                                                
                        mover_cima(nodeSelecionado);
                        
                        $('#h2Aviso').html(strSeletor);
                        
                    }//end if
                });//end click
                
                //Quando clicar no 'botão criar após'
                $('#buttonCriarApos').on('click', function(){
                	var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];                    
                    $("#jstree").jstree(true).create_node(nodeSelecionado, "Novo tópico", "after");                    
                });//end click

                
            //LIMITAÇÃO: Não funciona com nós raiz.
            function mover_cima(nodeMover){
                nodeAnterior = nodeMover.prev();
                if (nodeAnterior.length) {
                    posicao = posicao_node(nodeMover);
                    if (posicao !== 0){
                        posicao--;
                        
                        nodePaiAnterior =  nodeAnterior.parent();
                        
                        //essa função filha da puta só adiciona como filho, quando passado um número :@
                        //então para poder adicionar no mesmo nó, eu pego o pai do nó anterior                                                                                                                
                        $("#jstree").jstree(true).move_node(nodeMover, nodePaiAnterior, posicao);
                    }//end if
                }//end if                
            }//end function
            
            //Retorna a posição que o nó está
            //IMPORTANTE: Primeiro elemento índice = 0.
            function posicao_node(node){                                
                posicao = -1;
                while (node.length) {
                    posicao++;
                    node = node.prev();
                }//end while
                return posicao;
            }//end function       

            function ajax_request_montar_arvore_topicos(){
                $.ajax({
                    url: "../tutorial/tutorial_json.php"
                    , async: false
                    , type: "post"
                    , cache: false
                    , data: {"query_string": ""}
                    , complete: function(data){        
                          //Resposta do servidor
                          var resposta = eval("(" + data.responseText + ")");
                          
                          //Se há mensagem
                          if(resposta['mensagem'] != ""){
                              //Preenche mensagem
                              $("#p_dialog").html(resposta.mensagem);              
                              
                              //Mostra mensagem
                              $("#div_dialog").dialog({
                                  modal: true,
                                  buttons:{
                                      "Fechar":function(){$(this).dialog("close");}
                                  }//end buttons
                              });//end dialog
                              
                              //cai fora
                              return;
                          }//end if
                          
                          //Vai para a página indicada
                          if(resposta['pagina']= ""){
                              window.location = resposta.pagina;
                          }//end if
                          
                          for (topico_nivel = 1; topico_nivel <= resposta['nivel_maximo']; topico_nivel++) {                    
                              for(var indice in resposta['arvore_topicos']){                          
                                  topico = resposta['arvore_topicos'][indice];                    
                                  if(topico.topico_nivel == topico_nivel){
                                      var li = $("<li/>", {id:"li_id_" + topico.topico_id});                          
                                      $(li).append(topico.topico_nome);
                                      
                                      var ul = $("<ul/>", {id:"ul_id_" + topico.topico_id});
                                      $(li).append(ul);
                                      
                                      $(li).contextmenu(function() {
                                          alert( "Funciona" );
                                      });
                                      
                                      if(topico.topico_superior == null){
                                          $("#ul_raiz").append(li);                       
                                      }else{
                                          $("#ul_id_" + topico.topico_superior).append(li);
                                      }//end if                       
                                  }//end if                                                       
                              }//end for                  
                          }//end for
                          
                      }//end complete
                });//end ajax     
            }//end function
        });//end ready
                        
        </script>        
    </head>
    <body>    
        <div class="page-container">            
            <div class="panel-container">  
                <button id="buttonMoverCima">Mover para cima</button>
                <button id="buttonCriarApos">Criar após</button>
                
                <div id="jstree" class="panel-left">                                
                    <ul id="ul_raiz">                    
                    </ul>                    
                </div>   
                <div class="panel-right">
                    right panel
                </div>
            </div>
            
            <div id="div_dialog" title="Aviso" style="display:none">
                <p id="p_dialog">
                </p>
            </div>
            
            <span id="span_aguarde" style="display:none" class="aguarde position-relative">
                <img src="../resources/ajax-loader-red.gif" /> aguarde...
            </span>                
                        
        </div>
            
    </body>
</html>

<?php
?>
