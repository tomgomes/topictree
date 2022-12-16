<?php
    //require $_SERVER["DOCUMENT_ROOT"] . "/include/php/redirecionar_https.inc";

    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Topic Tree - SGC - Sistema de Gerenciamento de Conteúdo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        //require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        //require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        
        link_href("css/aguarde.css");
        link_href("css/black_overlay.css");
        link_href("js/jquery-bootstrap/jquery-ui.css"); 
        link_href("login/login.css");
        link_href("js/jquery-bootstrap/bootstrap.min.css");
        
        script_src("js/jquery-bootstrap/jquery.min.js");
        script_src("js/jquery-bootstrap/jquery-ui.min.js");
        script_src("js/jquery-bootstrap/popper.min.js");
        //script_src("js/jquery-bootstrap/bootstrap.min.js"); <-- buga o botão close
        script_src("js/jquery-block-ui/jquery.blockUI.js");
        script_src("js/topictree/topictree.js");
        script_src("login/login.js");
        
        ?>        
        
        <script type="text/javascript">   
            $(document).ready(function () {         
                $(document).ajaxStart(function(){ $.blockUI({ message: $('#span_aguarde') }) });
                $(document).ajaxStop($.unblockUI);
                
                document.getElementById("frm_login").addEventListener("submit", function(event){
                    event.preventDefault();
                    ajax_request_logar();
                });

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
    			    
                    <!-- Formulário aqui -->
                    
                    <form method="post"                                                                                                         
                          class="fundo_formulario border rounded p-4"
                          id="frm_login"   
                          >
                        
                        <img src="/login/tree_230px_140px.png" />
                        
                        <p class="bem_vindo">
                            <i>
                                Bem vindo de volta! Tudo bem?<br/>
                                Espero que sim, vamos lá.
                            </i>
                        </p>
                        
                        <div class="form-group">
                            <input type="text" 
                                   placeholder="Nome de usuário" 
                                   class="form-control caixa_texto" 
                                   id="username"
                                   name="username"
                                   autocomplete="on"
                                   />
                        </div>
                        
                        <div class="form-group">
                            <input type="password" 
                                   placeholder="Senha" 
                                   class="form-control caixa_texto" 
                                   id="password"
                                   name="password"
                                   autocomplete="current-password"                                    
                                   />
                        </div>
                        
                        <!-- div class="form-group form-check"> -->
                            <!-- input type="checkbox" class="form-check-input" id="input_checkbox_lembrar" / -->                                    
                            <!-- label for="input_checkbox_lembrar" class="branco">Lembrar</label -->
                        <!-- /div>  -->
                        
                        <button type="submit" class="btn btn-primary" id="submit">Entrar</button>
                        <hr/>
                        <a href="autor.php">
                            Criar uma conta
                        </a>
                        <hr/>
                        <a href="javascript:ajax_esqueci_senha()">
                            Esqueci a senha
                        </a>                            
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
