<?php    
    session_start();
    
    //Se existe o parâmetro 'aut', é visitante e acabou
    //Nota: Mesmo se estiver logado, se existir o parâmetro 'aut', será tratado como visitante
    
    if(isset($_GET['aut'])):
        $_SESSION['visitante'] = true;
        
        $_SESSION['autor_id_para_visitante'] = $_GET['aut'];    
    else:
        if(empty($_SESSION['autenticado'])):
            header('Location:/index.php');
        endif;
        if(!$_SESSION['autenticado']):
            header('Location:/index.php');
        endif;
        
        $_SESSION['visitante'] = false;
    endif;
    
    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
?>
<!DOCTYPE html>
<html>
    <head>        
        <title>Tópicos <?php echo $_SESSION['visitante'] ? "(modo visitante)" : "(modo autor)"; ?> </title>
        
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-contextmenu.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        link_href("expositor/expositor.css");
        link_href("css/aguarde.css");
        script_src("expositor/expositor.js");
        script_src("js/topictree/topictree.js");
        ?>
        
        <script>        
        $(document).ready(function(){
            var bloquear = true;
            var card_id = null;
            
            $.blockUI.defaults.message = $('#span_aguarde');
            $.blockUI.defaults.baseZ = 1200;
            
            $(document).ajaxStart(function(){
                if(bloquear){ 
                    $.blockUI(); //ATENÇÃO: Para funcionar o valor de async deve ser true
                }//end if 
            });                
            $(document).ajaxStop($.unblockUI);
            
            var avisos = ajax_request_montar_lista_tutoriais();            
            if(avisos.add_card < 5){
            	$('#dica_card').show();
                bloquear = false;
                ajax_resquest_contar("add_card");
                bloquear = true;                
            }//end if
            
        	$('#grade').selectable({
        		filter: "li",
        		cancel: '.sort-handle',
                selected: function(event, ui) { 
                    $(ui.selected).addClass("ui-selected").siblings().removeClass("ui-selected");
                    
                    $(".ui-selected", this).each(function() {
                        if(this.id != ""){
                            card_id = this.id;
                        }//end if
                    });//end each
                    
                    card_id = card_id.replace('card_id_', '');
                    
                    <?php if($_SESSION['visitante']): ?>
                        ajax_request_permissao(card_id, '');
                    <?php endif; ?>
                }//end selected                                                    
        	}).sortable({
        		items: "> li",
        		handle: '.sort-handle',
        		helper: function(e, item) {
        		    if ( ! item.hasClass('ui-selected') ) {
        		        item.parent().children('.ui-selected').removeClass('ui-selected');
        		        item.addClass('ui-selected');
        		    }//end if
                    
        		    var selected = item.parent().children('.ui-selected').clone();
        		    item.data('multidrag', selected).siblings('.ui-selected').remove();
        		    return $('<li/>').append(selected);
        		},//end helper
       		    stop: function(e, ui) {
        		    var selected = ui.item.data('multidrag');
        		    ui.item.after(selected);
        		    ui.item.remove();
                    
        		    ajax_resquest_ordenar();                                              
        		}//end stop
        	});//end sortable
            
            $('#button_editar').click(function(){
                editar_card();
            });
            
            $('#button_apagar').click(function(){
                apagar_card();
            });
            
            $('#button_topicos').click(function(){
                topicos();
            });
            
            $('#button_visualizar').click(function(){
                var url =  'expositor.php?aut=' + <?php echo $_SESSION['autor_id'] ?> 
                window.open(url, '_blank');
            });
            
            //Quando clicar no botão Ok da caixa de diálogo senha
            $('#button_ok').click(function(){
                var senha = $('#input_text_senha').val();
                ajax_request_permissao(card_id, senha);
            });
            
            var mover = false;
            $('#button_mover').click(
              function()
              {
                $(".sort-handle").toggle();
                mover = !mover;
                
                existe = $("#dica_mover").length;                               
                if(existe && mover && avisos.mover_card < 5){
                	avisos.mover_card++;
                    $('#dica_mover').show();
                    bloquear = false;
                    ajax_resquest_contar("mover_card");
                    bloquear = true;                
                }//end if                                
              }
            );
            
            $('#button_copiar_link').click(function(){
                var url = '<?php echo $_SERVER["HTTP_HOST"] . '/' ?>'
                        + 'expositor.php?aut=' + <?php echo $_SESSION['autor_id'] ?> 
                        // + '&t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/tutorial.php')?>';
            	copiarTexto(url);
            });
            
            $('#div_novo_card').click(function(e){
            	ajax_resquest_id_novo_tutorial(mover);
            });
            
            <?php if(!$_SESSION['visitante']): ?>
            $.contextMenu({
                selector: '.card',
                items:
                {
                    topicos:
                    {
                        name: 'Tópicos',
                        icon: 'topicos',
                        callback: function(key, opt)
                        {
                            topicos();
                        }//end callback
                    },
                    editar:
                    {
                        name: 'Editar card',
                        icon: 'editar',
                        callback: function(key, opt)
                        {
                            editar_card();
                        }//end callback
                    }, //end editar
                    permissao:
                    {
                        name: 'Permissão',
                        icon: 'permissao',
                        callback: function(key, opt)
                        {
                            permissao_card();
                        }//end callback
                    }, //end editar
                    excluir:
                    {
                        name: 'Excluir card',
                        icon: 'excluir',
                        callback: function(key, opt)
                        {
                            apagar_card();
                        }//end callback
                    } //end excluir
                } //end items
            }); //end objetct
            <?php endif; ?>
                                
            $('li').mousedown(function(event) {
                //Se clicou 2x com o botão esquerdo do mouse
                if (event.which == 1 && event.detail == 2)
                {
                    //Destaca o card clicado e remove o destaque dos demais
                    $(event.currentTarget).addClass("ui-selected").siblings().removeClass("ui-selected");
                    card_id = event.currentTarget.id.replace('card_id_', '');
                    
                    //Abre o tutorial e seus tópicos
                    topicos();
                }
                //Se clicou com o botão direito do mouse
                else if(event.which == 3)
                {
                    //Destaca o card clicado e remove o destaque dos demais
                    $(event.currentTarget).addClass("ui-selected").siblings().removeClass("ui-selected");
                    card_id = event.currentTarget.id.replace('card_id_', '');
                }//end if
            });            
            
            //Nota: tentei usar CSS e as propriedades background-image e content, 
            //porém elas apresentam problemas entre o chrome e o firefox 
            $('#img_mover').attr('src', '/expositor/resources/mover.png');
            $('#img_editar').attr('src', '/expositor/resources/editar.png');
            $('#img_topicos').attr('src', '/expositor/resources/branch.png');
            $('#img_apagar').attr('src', '/expositor/resources/excluir.png');
            $('#img_visualizar').attr('src', '/expositor/resources/view2.png');
            $('#img_copiar_link').attr('src', '/expositor/resources/link.png');
            
            //Abre o tutorial e seus tópicos
            function topicos()
            {
                if(card_id == null)
                {
                    //Mostra mensagem
                    $("#p_dialog").html("Por favor, selecione um <i>card</i>.");                               
                    $("#div_dialog").dialog({
                        modal: true,
                        height: "auto",
                        width: "auto",
                        buttons:{
                            "Fechar":function(){$(this).dialog("close");}
                        }//end buttons
                    });//end dialog                  
                }
                else
                {
                    <?php if($_SESSION['visitante']): ?>
                        ajax_request_permissao(card_id, '');
                    <?php else: ?>
                        $.blockUI({ message: $('#span_aguarde') });
                        window.location = 'tutorial.php?tutorial_id=' + card_id 
                                        + '&t=<?php echo filemtime($_SESSION["DOCUMENT_ROOT"] . '/tutorial.php')?>';
                    <?php endif; ?>
                }//end if               
            }//end function
            
            function apagar_card()
            {
                if(card_id == null)
                {
                    //Mostra mensagem
                    $("#p_dialog").html("Por favor, selecione um <i>card</i>.");                               
                    $("#div_dialog").dialog({
                        modal: true,
                        height: "auto",
                        width: "auto",
                        buttons:{
                            "Fechar":function(){$(this).dialog("close");}
                        }//end buttons
                    });//end dialog                                          
                }
                else
                {         
                    $("#confirmar_excluir_card").dialog(
                      {  
                         modal: true,
                         height: "auto",
                         width: "auto",
                         buttons:
                         {
                           "Excluir":
                           function()
                           {  
                              ajax_resquest_apagar(card_id);
                              $(this).dialog("close");
                           }//end excluir
                           ,
                           "Cancelar":
                           function()
                           {  
                              $(this).dialog("close");
                           }//end cancelar
                         }//end buttons
                      }//end object
                    );//end dialog
                }//end if                                   
            }//end function
            
            function editar_card()
            {
                if(card_id == null)
                {
                    //Mostra mensagem
                    $("#p_dialog").html("Por favor, selecione um <i>card</i>.");                               
                    $("#div_dialog").dialog({
                        modal: true,
                        height: "auto",
                        width: "auto",
                        buttons:{
                            "Fechar":function(){$(this).dialog("close");}
                        }//end buttons
                    });//end dialog                                          
                }
                else
                {
                    $.blockUI({ message: $('#span_aguarde') });
                	window.location = 'card_editar.php?card_id=' + card_id + '&t=<?php echo filemtime($_SESSION["DOCUMENT_ROOT"] . '/card_editar.php')?>';
                }//end if                	
            }//end function
            
            function permissao_card()
            {
                if(card_id == null)
                {
                    //Mostra mensagem
                    $("#p_dialog").html("Por favor, selecione um <i>card</i>.");                               
                    $("#div_dialog").dialog({
                        modal: true,
                        height: "auto",
                        width: "auto",
                        buttons:{
                            "Fechar":function(){$(this).dialog("close");}
                        }//end buttons
                    });//end dialog                                          
                }
                else
                {
                    $.blockUI({ message: $('#span_aguarde') });
                	window.location = 'card_permissao.php?card_id=' + card_id + '&t=<?php echo filemtime($_SESSION["DOCUMENT_ROOT"] . '/card_permissao.php')?>';
                }//end if                	
            }//end function
            
            //Quando o modal senha é mostrado
            $('#modalSenha').on('shown.bs.modal', function (e) {                
                $('#input_text_senha').val('');
                $('#input_text_senha').trigger('focus');                               
            });            
            
            <?php if($_SESSION['visitante']): ?>
            //Captura qualquer botão que foi clicado (dentro da janela modal senha e no footer)
            $('#modalSenha .modal-footer button').on('click', function (e) {
                $botao = $(e.target);  
            });
            
            //Quando o modal senha fecha
            $('#modalSenha').on('hidden.bs.modal', function (e) {
                if($botao == undefined){
                    return
                };
                var acao = $botao[0].innerText;
                if(acao == "Ok"){
                    var nova = $('#input_text_senha').val();
                    
                    /*
                        var data = {mensagem:"Alteração de senha falhou!<br/>Por favor, repita a senha corretamente.",
                                    pagina:""};
                        mostrar_mensagem(data);
                    */
                }//end if    
                $botao = undefined;                     
            });
            <?php endif; ?>
            
        });//end ready
        </script>
        
    </head>
    
    <body>
        
        <input id="hidden_url" type="hidden" >
        
        <div class="container-fluid page-container" >
            
            <?php if (!$_SESSION['visitante']): ?>  
            
            <div id="confirmar_excluir_card" title="Excluir card" style="display: none">
                <p>
                    Deseja mesmo excluir o card selecionado?<br/>
                </p>
            </div>            
            
            <div id="dica_card" class="alert alert-success alert-dismissible" style="display: none">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                Cada <i>card</i> representa um tutorial.
                Para começar adicione um <i>card</i> clicando no botão '+'. <br/>
                Selecione um <i>card</i> e clique no botão 'Editar card' para personalizar sua apresentação.
            </div>
            
            <div id="dica_mover" class="alert alert-success alert-dismissible" style="display:none">
                Coloque o mouse sobre o ícone '<img src='/expositor/resources/mover_25px_25px.png'/>' e arraste o <i>card</i> para a posição desejada.
                <button type="button" class="close" data-dismiss="alert">&times;</button>                
            </div>
            
            <div id="div_botoes" class="row d-flex justify-content-center"  >
                
                <button id="button_mover" class="botao m-1 p-1">
                    <img id="img_mover" class="m-1" /><span class="mr-2">Mover card</span>
                </button>
                &ensp;
                <button id="button_editar" class="botao m-1 p-1">
                    <img id="img_editar" class="m-1" /><span class="mr-2">Editar card</span>
                </button>
                &ensp;
                <button id="button_apagar" class="botao m-1 p-1">
                    <img id="img_apagar" class="m-1"/><span class="mr-2">Excluir card</span>
                </button>
                &ensp;
                <button id="button_topicos" class="botao m-1 p-1">
                    <img id="img_topicos" class="m-1"/><span class="mr-2">Tópicos</span>
                </button>
                &ensp;
                <button id="button_visualizar" class="botao m-1 p-1">
                    <img id="img_visualizar" class="m-1"/><span class="mr-2">Visualizar</span>
                </button>
                &ensp;
                <button id="button_copiar_link" class="botao m-1 p-1">
                    <img id="img_copiar_link" class="m-1"/><span class="mr-2">Copiar link</span>
                </button>
                
            </div>
            <?php endif; ?>
            
            <div id="linha_cards" class="row"  >                
                <div class="col-md-1" >                
                </div>
                
                <div class="col-md-10">  
                    <ul id="grade" >
                    </ul>
                    
                    <?php if (!$_SESSION['visitante']): ?>
                    <div id="div_novo_card" class="ui-state-default"> </div>
                    <?php endif; ?>                                                                                                    
                </div>
                
                <div class="col-md-1" >
                </div>
                
                <div id="div_dialog" title="Aviso" style="display:none">
                    <p id="p_dialog">
                    </p>
                </div>
                
                <span id="span_aguarde" style="display:none" class="aguarde position-relative">
                    <img src="../resources/ajax-loader-red.gif" /> aguarde...
                </span>                
            </div>            
		</div>
		
		<?php if($_SESSION['visitante']): ?>
        <div class="modal fade" id="modalSenha" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-dark" id="staticBackdropLabel">Por favor, informe a senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-dark">
                
                <input type="text" 
                       class="form-control m-2" 
                       id="username" 
                       name="username" 
                       readonly>
                
                <input type="password" 
                       placeholder="senha" 
                       class="form-control m-2"
                       id="input_text_senha"
                       name="senha"
                       maxlength="50"
                       />                    
              </div>
              <div class="modal-footer">
                <!-- button type="button" class="btn btn-secondary" data-dismiss="modal" id="button_cancelar2" >Cancelar</button -->
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="button_ok">Ok</button>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
    </body>
</html>

<?php
?>
