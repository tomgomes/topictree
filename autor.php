<?php
    //require $_SERVER["DOCUMENT_ROOT"] . "/include/php/redirecionar_https.inc";
    
    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Nova Conta</title>
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        link_href("autor/autor.css");
        script_src("autor/autor.js");
        script_src("js/topictree/topictree.js");
        script_src("js/jquery-mask/jquery.mask.min.js");
        script_src("js/jquery-capitalizar/capitalizar.js");
        ?>        
        
        <script type="text/javascript">   
            $(document).ready(function () {         
                $(document).ajaxStart(function(){ $.blockUI({ message: $('#span_aguarde') }) });
                $(document).ajaxStop($.unblockUI);
                
                $('#button_cadastrar').click(function(){
                	ajax_request_cadastrar();
                });
                
                jQuery('#input_text_nome, #input_text_sobrenome').keyup(function(){ 
                       this.value = this.value.replace(/[^A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]/g,'');
                });
                
                jQuery('#input_text_usuario').keyup(function(){ 
                    this.value = this.value.replace(/[^A-Za-z0-9áàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ_ ]/g,'');
                });
                
                $('#input_text_datanascimento').mask('00/00/0000');
                
                $("#input_text_nome").blur(function(){
                	capitalizar(this);
                });
                
                $("#input_text_sobrenome").blur(function(){
                    capitalizar(this);
                });
                
                $('#input_text_usuario').val("");
                $('#input_text_senha').val("");
                
            });//end ready  
        </script>                
    </head>
    <body class="tela">        
        <div class="container">            
        	<div align="center">
            <div style="padding: 5% 0">
            <div style="padding: 5% 0">   
            
            <div class="row">
                
    			<div class="col-xl-2" >
    			</div>
                
                <div class="col-xl-2 fundo_formulario" style="border-top-left-radius: 20px;">
                    <img src="/autor/tree_115px_70px.png" class="m-3" />
                    <p class="bem_vindo">
                        <i>
                        Por favor, preencha todos os campos.
                        </i>
                    </p>                    
                </div>
                
                <div class="col-xl-3 fundo_formulario">
                    <br/>
                    <input type="text" 
                           placeholder="Nome" 
                           class="form-control caixa_texto" 
                           id="input_text_nome"
                           name="nome"
                           maxlength="25"
                           />
                    
                    <input type="text" 
                           placeholder="Sobrenome" 
                           class="form-control caixa_texto" 
                           id="input_text_sobrenome"
                           name="sobrenome"
                           maxlength="50"
                           />
                                        
                    <input type="text" 
                           placeholder="dd/mm/aaaa nascimento" 
                           class="form-control caixa_texto" 
                           id="input_text_datanascimento"
                           name="datanascimento"
                           />
                    
                    <input type="email" 
                           placeholder="e-mail" 
                           class="form-control caixa_texto" 
                           id="input_text_email"
                           name="email"
                           maxlength="50"
                           />                    
                </div>
                
                <div class="col-xl-3 fundo_formulario" style="border-top-right-radius: 20px">
                    <br/>
                    <input type="text" 
                           placeholder="Nome de usuário" 
                           class="form-control caixa_texto" 
                           id="input_text_usuario"
                           name="usuario"
                           maxlength="15"
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
                </div>
                
                <div class="col-xl-2" >
                </div>
                
                <!-- class="form-group" -->
                
                <div id="div_dialog" title="Aviso" style="display:none">
                    <p id="p_dialog">
                    </p>
                </div>
                
				<span id="span_aguarde" style="display:none" class="aguarde position-relative">
				    <img src="../resources/ajax-loader-red.gif" /> aguarde...
				</span>                                
            </div>
            
            <div class="row">
                <div class="col-xl-2" >
                </div>
                
                <div class="col-xl-8 fundo_formulario" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px">
                    <button class="btn btn-primary m-3" id="button_cadastrar">Cadastrar</button>
                </div>
                
                <div class="col-xl-2" >
                </div>                            
            </div>
            
            </div>
            </div>
            </div>
            
        </div>
        
    </body>
</html>
