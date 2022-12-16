<?php
    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
    
    require $_SESSION["DOCUMENT_ROOT"] . "/include/php/restrito.inc";
?>
<!DOCTYPE html>
<html>
    <head>        
        <title>Topic Tree - SGC - Sistema de Gerenciamento de Conteúdo</title>
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/ckeditor.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/bootstrap-filestyle.inc";
        
        link_href("card_permissao/card_permissao.css");
        script_src("card_permissao/card_permissao.js");
        link_href("css/aguarde.css");
        script_src("js/topictree/topictree.js");
        ?>
        
        <script type="text/javascript">     
            
            $(document).ready(function(){
                
                //Nota: tentei usar CSS e as propriedades background-image e content, 
                //porém elas apresentam problemas entre o chrome e o firefox 
                $('#img_voltar2').attr('src', '/card_permissao/resources/botao-voltar.png');
                $('#img_salvar2').attr('src', '/card_permissao/resources/botao-salvar-4.png');
                
                $.blockUI.defaults.message = $('#span_aguarde');
                $.blockUI.defaults.baseZ = 1200;
                
                //ATENÇÃO: o dado async deve ser true
                $(document).ajaxStart(function(){ 
                    $.blockUI();
                });                
                $(document).ajaxStop($.unblockUI);
                
                //Ao clicar no botão salvar
                $("#img_salvar2").on("click", function(e){
                    e.preventDefault();
                    ajax_resquest_salvar();
                });
                
                $("#select_permissao").on("change", function(e){
                    me_senha();
                });
                
                ajax_resquest_carregar_card();
            });
            
        </script>        
        
    </head>
    
    <body>
        <form id="form_card" >
        
        <div class="container-fluid page-container" >
            
            <div id="dica_card" class="alert alert-success alert-dismissible" >
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                Escolha a permissão e clique no botão salvar.
            </div>
            
            <input type="hidden" id="input_card_id" name="card_id" value=<?php echo $_GET['card_id']?>> 
            <!-- input type="hidden" id="input_predefinada" name="predefinada" value="expositor/resources/card_amarelo.png" -->
            <!-- input type="hidden" id="input_card_ordem" name="card_ordem" value="0" -->
            
            <div class="row" >
                
                <div class="col-md-2">
                </div>
                
                <div class="col-md-8">
                    
                    <div class="card mt-3" >
                        <div class="card-header">
                            Permissão
                        </div>
                        
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label for="username">Nome do card</label>
                                <input type="text" class="form-control" id="username" name="username" readonly>
                                
                                <label for="select_permissao" class="mt-3">Nível de permissão</label>
                                <select id="select_permissao" name="permissao" class="form-control"> 
                                    <option value="T" >Todos (padrão)</option>
                                    <option value="TCS" >Todos com a senha</option>
                                    <option value="SE">Somente eu</option>
                                </select>
                                <br/>
                                <p>Nota: As restrições aqui se aplicam somente para os visitantes.<br/>Você como autor (logado) sempre terá acesso permitido.</p>
                            </div>
                            
                            <div class="form-group" id="div_senha">
                                <fieldset class="border p-2">
                                    
                                    <legend class="tamanho_fonte w-auto">
                                       Configurar senha
                                    </legend>
                                    
                                    <input type="password" 
                                           placeholder="senha" 
                                           class="form-control mr-4" 
                                           id="input_text_senha_nova"
                                           name="senha_nova"
                                           maxlength="50"
                                           />                    
                                    
                                    <input type="password" 
                                           placeholder="repita a senha" 
                                           class="form-control mt-2 mr-4"
                                           id="input_text_senha_repetida"
                                           name="senha_repetida"
                                           maxlength="50"
                                           />                    
                                   
                                </fieldset>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="col-md-2">
                </div>
                
            </div>
        </div>
        
        <div class="container-fluid page-container" >
                <!-- div class="row" id="footer" -->
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    
                    <div class="col-md-8">
                        <div id="div_botoes" class="card my-3" >
                            <div class="card-body">
                                <div class="row" >
                                    <div class="col-md-2">
                                        <a href="/expositor.php" >
                                            <img id="img_voltar2" />
                                        </a>
                                    </div>
                                    <div class="col-md-9" style="text-align: center;">
                                        <img id="img_salvar2" class="botao" style="display:inline;" />
                                    </div>
                                    <div class="col-md-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                    </div>
                    
                </div>
        </div>
        
        </form>
        
        <span id="span_aguarde" style="display:none" class="aguarde position-relative">
            <img src="../resources/ajax-loader-red.gif" /> aguarde...
        </span>                
        
        <div id="div_dialog" title="Aviso" style="display:none">
            <p id="p_dialog">
            </p>
        </div>
    </body>
</html>

<?php
?>






