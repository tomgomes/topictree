<?php
    //require $_SERVER["DOCUMENT_ROOT"] . "/include/php/redirecionar_https.inc";
    
    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Topic Tree</title>
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        link_href("menu/menu.css");
        script_src("js/topictree/topictree.js");
        ?>        
        
        <script type="text/javascript">   
            $(document).ready(function () {         
                
                $(document).ajaxStart(function(){ $.blockUI({ message: $('#span_aguarde') }) });
                $(document).ajaxStop($.unblockUI);
                
                //Nota: tentei usar CSS e as propriedades background-image e content, 
                //porém elas apresentam problemas entre o chrome e o firefox 
                $('#img_minha_conta').attr('src', '/menu/resources/minha_conta_02_32px.png');
                $('#img_suporte').attr('src', '/menu/resources/suporte_02_32px.png');
                $('#img_manual').attr('src', '/menu/resources/manual_02_32px.png');
                $('#img_cobranca').attr('src', '/menu/resources/cobranca_32px.png');
                $('#img_tutorial').attr('src', '/menu/resources/tutoriais_02_32px.png');
                
                $('#button_tutorial').click(function(){
                    $.blockUI({ message: $('#span_aguarde') });
                    //window.location = '/expositor.php';
                    <?php window_location("expositor.php") ?>
                });
                
                $('#button_manual').click(function(){
                    window.open('/tutorial.php?aut=4&tut=68&topico=261', '_blank');
                });
                
                $('#button_minha_conta').click(function(){
                    window.open('/conta.php', '_blank');
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
    			    
    			    <div id="div_botoes" class="border rounded p-4">
    			        
                        <img id="img_logo" src="/login/tree_230px_140px.png" class="m-1 p-1" />
        			    <br/>
        			    
        			    <button type="button" class="botao w-50 m-1 p-1" id="button_tutorial">
        			        <img id="img_tutorial" class="m-1" /><span class="ml-2">Tutoriais</span>
        			    </button>
        			    <br/>
        			    
        			    <button type="button" class="botao w-50 m-1 p-1" id="button_cobranca">
        			        <img id="img_cobranca" class="m-1" /><span class="ml-2">Cobrança</span>
        			    </button>
        			    <br/>
        			    
        			    <button type="button" class="botao w-50 m-1 p-1" id="button_manual">
        			        <img id="img_manual" class="m-1" /><span class="ml-2">Manual</span>
        			    </button>
        			    <br/>
        			    
        			    <button type="button" class="botao w-50 m-1 p-1" id="button_minha_conta">
        			        <img id="img_minha_conta" class="m-1" /><span class="ml-2">Minha conta</span>
        			    </button>
        			    <br/>
        			    
        			    <button type="button" class="botao w-50 m-1 p-1" id="button_suporte">
        			        <img id="img_suporte" class="m-1" /><span class="ml-2">Suporte</span>
        			    </button>
    			    
    			    </div>
    			    
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
