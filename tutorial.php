<?php
    session_start();
    
    //Se existe o parâmetro 'aut', é visitante e acabou
    //Nota: Mesmo se estiver logado, se existir o parâmetro 'aut', será tratado como visitante
    
    if(isset($_GET['aut'])):
        $_SESSION['visitante'] = true;
        $_SESSION['autor_id_para_visitante'] = $_GET['aut'];
        
        $_SESSION['tutorial_id'] = $_GET['tut'];
        $_GET['tutorial_id'] = $_GET['tut'];        
    else:
        //Se valor na sessão não existe
        if(empty($_SESSION['autenticado'])):
            header('Location:/index.php'); //Vai para o index
            return;
        endif;
        
        //Se não está autenticado (valor é false)
        if(!$_SESSION['autenticado']):
            header('Location:/index.php'); //Vai para o index
            return;
        endif;
        
        //Marque que não é visitante e continua
        $_SESSION['visitante'] = false;
    endif;
    
?>

<!DOCTYPE html>
<html>
    
    <head>        
        <title>Tópicos <?php echo $_SESSION['visitante'] ? "(modo visitante)" : "(modo autor)"; ?> </title>
        <?php
        require $_SERVER["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        //require $_SERVER["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";        
        require $_SERVER["DOCUMENT_ROOT"] . "/include/js/ckeditor.inc";
        require $_SERVER["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        link_href("tutorial/tutorial.css");
        link_href("js/jquery-jstree/themes/default/style.min.css");
        link_href("css/aguarde.css");
        link_href("css/black_overlay.css");
        link_href("js/jquery-bootstrap/jquery-ui.css");
        
        script_src("js/jquery-bootstrap/jquery.min.js");
        script_src("js/jquery-bootstrap/jquery-ui.min.js");
        script_src("js/jquery-block-ui/jquery.blockUI.js");
        script_src("js/jquery-jstree/jstree.min.js");
        script_src("tutorial/tutorial.js");
        script_src("js/jquery-redimensionar/jquery-redimensionar.js");
        script_src("js/topictree/topictree.js");
        ?>
        
        <script>        
        $(document).ready(function () {
            
            //Bloqueio de tela
            $.blockUI.defaults.message = $('#span_aguarde');
            $.blockUI.defaults.baseZ = 1200;
            
            var bloquear = true;                
            $(document).ajaxStart(function(){
                if(bloquear){                	
                    $.blockUI(); //ATENÇÃO: 'async' do ajax deve ser true
                }//end if
            });                
            $(document).ajaxStop($.unblockUI);
            
            ajax_request_montar_arvore_topicos();
            
            //create an instance when the DOM is ready
            $('#jstree').jstree({
                core: { 
                    multiple: false,
                    check_callback: function (op, node, par, pos, more) {
                        if(op === "rename_node") {
                            ajax_resquest_renomear(node.id, pos);                                                                                                                      
                            return true;
                        } else if(op === "create_node") {
                            var topico_superior = par.id.replace('li_id_', '');
                            var topico_id = node.id.replace('li_id_', '');
                            var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];                                  
                            ajax_resquest_criar(topico_superior, topico_id, pos, node.data, nodeSelecionado);
                            
                            return true;                                                      
                        }else if(op == "move_node"){
                            return true;
                        }//end if
                    }//end check_callback
                },//end core          
                plugins: ["contextmenu", "types", "dnd"],
                dnd: {
                    copy: false,
                    drag_selection: false,
                    large_drop_target: true,
                    //, use_html5: true     // Didn't work in my testing
                    //, check_while_dragging: false   // For debugging only
                    is_draggable: function() {return true;},
                },//end dnd     
                types: 
                {
                    "oculto" : 
                    {
                        "icon" : "/tutorial/resources/topico_oculto.png"
                    }//end oculto
                },//end types
                contextmenu: {         
                    items: function($node) {
                        
                        //var tree = $('#jstree').jstree(true);
                        //var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
                        
                        <?php if ($_SESSION['visitante']): ?>
                        
                        var menu_completo = {};
                        
                        <?php else: ?>
                        var menu_completo = { 
                            
                            CriarFilho: { 
                                label: "+ Filho",
                                separator_before: false,
                                separator_after: false,
                                action: function (obj) { 
                                    var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];														                                    	                      
                                    var topico_id = ajax_resquest_id_novo_topico(nodeSelecionado);                                            	                          
                                    if(topico_id == null){ return; }
                                                      
                                    var topico_novo =  {"id": "li_id_" + topico_id, "text": "Novo tópico", "data": "filho"};                                            	          
                                    $("#jstree").jstree(true).create_node(nodeSelecionado, topico_novo, "last");
                                                      
                                }//end action
                            },//end criarfilho
                            CriarApos: { 
                                label: "+ Após",
                                separator_before: false,
                                separator_after: false,
                                action: function (obj) { 
                                    var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
                                    var topico_id = ajax_resquest_id_novo_topico(nodeSelecionado);
                                    if(topico_id == null){ return; }
                                    
                                    var topico_novo =  { 
                                        "id": "li_id_" + topico_id,
                                        "text": "Novo tópico",
                                        "data": "apos"
                                    };//end topico_novo
                                                      
                                    $("#jstree").jstree(true).create_node(nodeSelecionado, topico_novo, "after");                    
                                }//end action
                            },//end criarapos
                            CriarAntes: { 
                                label: "+ Antes",
                                separator_before: false, 
                                separator_after: false,
                                action: function (obj) {
                                    var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
                                    var topico_id = ajax_resquest_id_novo_topico(nodeSelecionado);
                                    if(topico_id == null){ return; }
                                    
                                    var topico_novo =  { 
                                        "id": "li_id_" + topico_id,
                                        "text": "Novo tópico",
                                        "data": "antes"
                                    };//end topico_novo
                                                      
                                    $("#jstree").jstree(true).create_node(nodeSelecionado, topico_novo, "before");                    
                                }//end action
                            },//end criarantes
                            Renomear: { 
                                label: "Renomear",
                                separator_before: true,
                                separator_after: false,
                                action: function (obj) { 
                                    //tree.edit($node);
                                    $("#jstree").jstree(true).edit($node);
                                }//end action
                            },//end renomear
                            Apagar: { 
                                label: "Apagar",
                                separator_before: false,
                                separator_after: false,
                                action: function (obj) {
                                    var nodeSelecionado = $.jstree.reference('#jstree').get_selected(false)[0];
                                    
                                    $("#confirmar_excluir_topico").dialog(
                                      {  
                                         modal: true,
                                         height: "auto",
                                         width: "auto",
                                         buttons:
                                         {
                                           "Excluir":
                                           function()
                                           {  
                                              ajax_resquest_apagar(nodeSelecionado); 
                                              $("#jstree").jstree(true).delete_node($node);
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
                                }//end action
                            },//end apagar
                            Ocultar_mostrar: 
                            { 
                                label: "",
                                separator_before: false,
                                separator_after: false,
                                action: function (obj) 
                                {
                                    ref_arvore = $('#jstree');
                                    id_topico_selecionado = ref_arvore.jstree(true).get_selected(false)[0];
                                    tipo = ref_arvore.jstree(true).get_type($node);
                                    if(tipo == "oculto")
                                    {
                                        ajax_resquest_ocultar_mostrar(id_topico_selecionado, "mostrar");
                                    }
                                    else
                                    {
                                        //default
                                        ajax_resquest_ocultar_mostrar(id_topico_selecionado, "ocultar");
                                    }//end if
                                }//end action
                            }//end ocultar_mostrar
                        };//end menu_completo
                        <?php endif; ?>
                        
                        tipo = $("#jstree").jstree(true).get_type($node);
                        if(tipo == "oculto")
                        {
                            menu_completo.Ocultar_mostrar.label = "Mostrar";
                        }
                        else
                        {
                            menu_completo.Ocultar_mostrar.label = "Ocultar";
                        }//end if
                        
                        return menu_completo;                                                                                        
                    }//end items
                }//end contextmenu
            });//end jstree
            
            <?php if ($_SESSION['visitante']): ?>
            
            //Selecionar nó conforme o parâmetro top (tópico)                        
            var topico_id = $('#input_hidden_topic_id_para_visitante').val();
            if(topico_id != ""){            
                $('#jstree').jstree(true).select_node('#li_id_' + topico_id);
                ajax_resquest_conteudo('li_id_' + topico_id, true);
            }//end if
            
            $("#jstree")
            .bind("select_node.jstree", function (node, selected, event) {
                ajax_resquest_conteudo(selected.node.id, true);
            });
            
            $("#img_expandir").click(function(){
                $("#jstree").jstree("open_all");
            }).attr('title', 'Expandir todos');
            
            $("#img_recolher").click(function(){
                $("#jstree").jstree("close_all");
            }).attr('title', 'Recolher todos');

            $('#form_editortexto').css('padding', '20px');

            $('#editortexto').hide();
            
            //RickStrahl- jquery-resizable
            $("#painel_esquerdo").redimensionar({
                handleSelector: ".splitter",
                resizeHeight: false
            });

            var altura_window = $(window).height();     
            var altura_cabecalho = $('#conteudo_cabecalho').outerHeight();
            var altura = altura_window - (altura_cabecalho + 20);   
            $('#form_editortexto').css('height', altura * 0.90);
            $('#form_editortexto').css('overflow-y', 'scroll');  
            $('#jstree').css('max-height', altura);
            $('#jstree').css('min-height', altura);
            
            <?php else: ?>
            //Muda a url, adicionando o parâmetro aut=[código], assim o usuário pode copiar a url e compartilhar o tutorial
            var autor_id = $('#input_hidden_autor_id').val();
            var tutorial_id = $('#input_tutorial_id').val();                           
            url_topico = "tutorial.php?aut=" + autor_id + "&tut=" + tutorial_id
            window.history.pushState("topico", "", url_topico);             
            
            ajax_request_abrir_topicos();
            
            var evento_abrir = function (event, selected) {
                bloquear = false;
                ajax_resquest_abertura_topico(selected.node.id, 'S');
                bloquear = true;
            }            
            $("#jstree").bind("open_node.jstree", evento_abrir);
            
            var evento_fechar = function (event, selected) {
                bloquear = false;
                ajax_resquest_abertura_topico(selected.node.id, 'N');
                bloquear = true;
            }
            $("#jstree").bind("close_node.jstree", evento_fechar);
            
            $("#img_expandir").click(function(){            	
                ajax_resquest_abertura_tutorial('S', evento_abrir, evento_fechar);                                
            });
            $("#img_expandir").attr('title', 'Expandir todos');
            
            $("#img_recolher").click(function(){            	            	
                ajax_resquest_abertura_tutorial('N', evento_abrir, evento_fechar);                                
            });
            $("#img_recolher").attr('title', 'Recolher todos')
            
            //RickStrahl- jquery-resizable
            $("#painel_esquerdo").redimensionar({
                handleSelector: ".splitter",
                resizeHeight: false,
                onDragEnd:function(evt) {
                    bloquear = false; 
                    ajax_resquest_largura('painel_esquerdo');
                    bloquear = true;                         
                }//end ondragend  
            });
            
            var umavez = false;                
            const EDITOR = 0;
            const ARVORE = 1;
            var origem_alteracao;                
            $("#jstree")
            .bind("select_node.jstree", function (node, selected, event) {
                CKEDITOR.instances.editortexto.getCommand('save').disable();
                ajax_resquest_conteudo(selected.node.id, false);
                umavez = true;
                origem_alteracao = ARVORE;
            });
            
            CKEDITOR.replace('editortexto', {                              
            });
            
            //instanceReady
            CKEDITOR.instances.editortexto.on( 
                'loaded', function(evt) { 
                    //CKEDITOR.instances.editortexto.resize('500', '300');
                    CKEDITOR.addCss('.cke_editable p { margin: 0 !important; }'); 
                }//end loaded                    
            );//end on
            
            CKEDITOR.instances.editortexto.on(
                'change', function( evt ) {
                    if(umavez){
                        umavez = false;                        
                    }else{
                        if(origem_alteracao == EDITOR){
                            CKEDITOR.instances.editortexto.getCommand('save').enable();
                        }//end if
                    }//end if
                    origem_alteracao = EDITOR;
                }//end change
            );//end on
            
            CKEDITOR.on( 'dialogDefinition', function( ev ) {
                // Take the dialog name and its definition from the event data.
                var dialogName = ev.data.name;
                var dialogDefinition = ev.data.definition;
            
                if ( dialogName == 'link' ) {
                  var targetTab = dialogDefinition.getContents( 'target' );
                  // Set the default value for the URL field.
                  var targetField = targetTab.get( 'linkTargetType' );
                  var targetName = targetTab.get( 'linkTargetName' );
                  targetField[ 'default' ] = '_blank';
                  targetName[ 'default' ] = '_blank';
                }
            });            
            
            CKEDITOR.on("instanceReady", function(event) {
                event.editor.on("beforeCommandExec", function(event) {
                // Show the paste dialog for the paste buttons and right-click paste
                if (event.data.name == "paste") {
                    event.editor._.forcePasteDialog = true;
                }
                // Don't show the paste dialog for Ctrl+Shift+V
                if (event.data.name == "pastetext" && event.data.commandData.from == "keystrokeHandler") {
                    event.cancel();
                }
                });
            });
            
            formulario = $("#form_editortexto");
            
            //e - east (leste, direita)
            $(formulario).resizable({ 
                handles: 'e', 
                helper: "ui-resizable-helper"
            });
            
            var resize_change = false;                
            $(formulario).resize(function() {
                resize_change = true;
            });                
            $(window).mouseup(function() {
                if(resize_change){
                    bloquear = false;
                    ajax_resquest_largura('form_editortexto');
                    bloquear = true;
                }//end if
                resize_change = false;
            });
            
            $("#jstree")
            .bind("move_node.jstree", function(e, data) {     
                ajax_resquest_mover(data);                                                                      
            });
            
            <?php endif; ?>
            
            $("#jstree").jstree("set_theme", "default-dark", "/js/jquery-jstree/themes/default-dark/style.css");                
            $("#jstree").jstree("set_theme", "default-dark");            
        });//end ready
                        
        </script>        
    </head>
    
    <body>
        
        <div class="container">
            
            <div id="confirmar_excluir_topico" title="Excluir tópico" style="display: none">
                <p>
                    Deseja mesmo excluir o tópico selecionado?<br/>
                </p>
            </div>            
            
            <div class="container-row">
                <div id="painel_esquerdo" class="sidebar">
                    <div class="logo">                                            
                        <img id="img_recolher" src="/tutorial/resources/collapse-ball.png" />    
                        <span class="separador_icones"></span>                    
                        <img id="img_expandir" src="/tutorial/resources/expand-ball.png" />
                    </div>
                    
                    <div id="jstree" class="inner">
                        <ul id="ul_raiz">
                        </ul>                    
                    </div>
                </div>
                
                <div id="separador" class="splitter">
                </div>
                
                <div class="content">
                    <div id="conteudo_cabecalho" class="header">
                        <?php if ($_SESSION['visitante']): ?>
                        
                        <a href='expositor.php?aut=<?php echo $_SESSION['autor_id_para_visitante'] ?>'>
                        <img id="img_voltar" src= "/tutorial/resources/voltar.png" />                                            
                        </a>
                        
                        <input type="hidden" 
                               id="input_hidden_autor_id_para_visitante" 
                               value=<?php echo $_SESSION['autor_id_para_visitante'] ?>>
                        
                        <input type="hidden" 
                               id="input_hidden_topic_id_para_visitante" 
                               value=<?php if(isset($_GET['topico'])): echo $_GET['topico']; endif ?>>
                        
                        <?php else: ?>
                        
                        <input type="hidden" 
                               id="input_hidden_autor_id" 
                               value=<?php echo $_SESSION['autor_id'] ?>>
                        
                        <a href="/expositor.php" >
                        <img id="img_voltar" src= "/tutorial/resources/voltar.png" />                                            
                        </a>
                                                
                        <?php endif;?>
                    </div>
                    
                    <div class="content-body">                        
                        <form id="form_editortexto" class="panel" action="javascript:ajax_request_salvar()" onsubmit="return false;">
                            <textarea id="editortexto" class=""></textarea>                        
                        </form>
                    </div>                    
                </div>
                
                <div id="div_dialog" title="Aviso" style="display:none">
                    <p id="p_dialog">
                    </p>
                </div>
                
                <span id="span_aguarde" style="display:none" class="aguarde position-relative">
                    <img src="resources/ajax-loader-red.gif" /> aguarde...
                </span>
                
                <input type="hidden" id="input_tutorial_id" name="tutorial_id" value=<?php echo $_GET['tutorial_id']?>>
                
            </div>
        </div>                
    </body>
</html>

<?php
?>























