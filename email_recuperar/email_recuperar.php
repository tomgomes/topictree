<?php
//require $_SERVER["DOCUMENT_ROOT"] . "/include/php/redirecionar_https.inc";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Nova Senha</title>
        <?php
        require $_SERVER["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SERVER["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SERVER["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        ?>        
        <link rel="stylesheet" 
              type="text/css" 
              href="/email_recuperar/email_recuperar.css?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/email_recuperar/email_recuperar.css')?>">
        
        <script type="text/javascript" 
                src="/email_recuperar/email_recuperar.js?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/email_recuperar/email_recuperar.js')?>">
        </script>
        
        <script type="text/javascript" 
                src="/js/topictree/topictree.js?t=<?php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/js/topictree/topictree.js')?>">
        </script>
        
        <script type="text/javascript">   
            $(document).ready(function () {         
                $(document).ajaxStart(function(){ $.blockUI({ message: $('#span_aguarde') }) });
                $(document).ajaxStop($.unblockUI);
                
                document.getElementById("frm_login").addEventListener("submit", function(event){
                    event.preventDefault();
                    ajax_request_enviar_dados();
                });
                
                //Mostra a mensagem
                var mensagem = "Por favor, anote o código que foi enviado para o seu e-mail."
                $("#p_dialog").html(mensagem);
                $("#div_dialog").dialog({
                    modal: true,
                    height: "auto",
                    width: "auto",                    
                    buttons:{
                        "Fechar":function(){
                            $(this).dialog("close");
                            //window.location = data.pagina;
                        }
                    }//end buttons
                });//end dialog
                                              
            });//end ready  
        </script>                
    </head>
    <body class="tela">        
        <div class="container">    
            
        	<div align="center">
            <div style="padding: 1% 0">
            <div style="padding: 1% 0">    
            
            <div class="row">
                
    			<div class="col-xl-4" >
    			</div>
    			
    			<div class="col-xl-4" >
                    
                    <form method="post"                                                                                                         
                          class="fundo_formulario border rounded p-4"
                          id="frm_login"   
                          >
                        
                        <img src="/login/tree_230px_140px.png" />
                        
                        <p class="bem_vindo">
                            <i>
                                Informe o código recebido por e-mail.<br/>
                                E defina sua nova senha.
                            </i>
                        </p>
                        
                        <input type="text" 
                               placeholder="Código" 
                               class="form-control caixa_texto" 
                               id="input_text_codigo"
                               name="codigo"
                               maxlength="6"
                               />
                        
                        <input type="password" 
                               placeholder="Senha" 
                               class="form-control caixa_texto" 
                               id="input_text_senha"
                               name="senha"
                               maxlength="50"
                               />
                        
                        <input type="password" 
                               placeholder="Repita senha" 
                               class="form-control caixa_texto" 
                               id="input_text_senha_repetida"
                               name="senha_repetida"
                               maxlength="50"
                               />                                    
                                                
                        <!-- div class="form-group form-check"> -->
                            <!-- input type="checkbox" class="form-check-input" id="input_checkbox_lembrar" / -->                                    
                            <!-- label for="input_checkbox_lembrar" class="branco">Lembrar</label -->
                        <!-- /div>  -->
                        
                        <button type="submit" class="btn btn-primary" id="submit">Entrar</button>
                        
                    </form>                                
    			</div>
    			
    			<div class="col-xl-4" >
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
            </div>
            
            </div>            
        </div>        
    </body>
</html>
