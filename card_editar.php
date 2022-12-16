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
        
        link_href("card/card_editar.css");
        link_href("css/aguarde.css");
        script_src("card/card_editar.js");
        script_src("js/topictree/topictree.js");
        ?>
        
        <!--  link rel="stylesheet" type="text/css" href="../css/black_overlay.css?ultimaAtualizacao=<php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '../css/black_overlay.css')?>" -->
        
        <script type="text/javascript">     
            
            $(document).ready(function(){
                
                //Nota: tentei usar CSS e as propriedades background-image e content, 
                //porém elas apresentam problemas entre o chrome e o firefox 
                $('#img_voltar2').attr('src', '/card/resources/botao-voltar.png');
                $('#img_salvar2').attr('src', '/card/resources/botao-salvar-4.png');
                
                $("#input_file").filestyle({buttonBefore: true, text: "Procurar"});
                
                $.blockUI.defaults.message = $('#span_aguarde');
                $.blockUI.defaults.baseZ = 1200;
                    
                //ATENÇÃO: o dado async deve ser true
                $(document).ajaxStart(function(){ 
                    $.blockUI();
                });                
                $(document).ajaxStop($.unblockUI);
                
                //Ao escolher a cor, troca a cor de fundo do card
                $("[type='radio']").on("click", function(e){
                    carregar_imagem(e.target.value);
                });
                
                //Ao clicar no botão enviar
                $("#button_enviar").on("click", function(e){
                    e.preventDefault();
                    ajax_resquest_enviar_imagem();
                });
                
                //Ao clicar no botão atualizar
                $("#button_url_imagem").on("click", function(e){
                    e.preventDefault();
                    trocar_imagem3();
                });
                
                //Ao clicar no botão salvar
                $("#img_salvar2").on("click", function(e){
                    e.preventDefault();
                    ajax_resquest_salvar();
                });
                
                ajax_resquest_carregar_card();
            });
            
            $(window).on('load', function()
            {
                
                document.getElementById('div_card_modelo').setAttribute('contenteditable', true);
                
                $('#div_card_modelo').ckeditor();
                
                CKEDITOR.inline('div_card_modelo', {
                    customConfig: '../ckeditor4/config_inline.js'                    
                });                
                CKEDITOR.addCss('.cke_editable p { margin: 0 !important; }');
            });
            
        </script>        
        
    </head>
    
    <body>
        <div class="container-fluid page-container" >
            
            <div id="dica_card" class="alert alert-success alert-dismissible" >
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                Modifique o <i>card</i> e clique no botão salvar.                
            </div>
            
            <form id="form_card">
                
                <input type="hidden" id="input_card_id" name="card_id" value=<?php echo $_GET['card_id']?>> 
                <!-- input type="hidden" id="input_predefinada" name="predefinada" value="expositor/resources/card_amarelo.png" -->
                <input type="hidden" id="input_card_ordem" name="card_ordem" value="0">                                
                
                <div class="row">
                    
                    <div class="col-md-2">
                    </div>
                    
                    <div class="col-md-7">
                        
                        <div class="card mt-3">
                            <div class="card-header">Título do card</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <input type="text" id="input_card_nome" name="card_nome" class="form-control" maxlength="60">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            
                            <a data-toggle="collapse" href="#div_imagem_fundo" aria-expanded="false" aria-controls="div_imagem_fundo">
                                <div class="card-header">(Opcional) fundo personalizado. Suba um fundo de imagem ou informe um link para a imagem</div>
                            </a>                            
                            
                            <div class="card-body collapse" id="div_imagem_fundo" >
                                <div class="row">
                                    <div class="col-md-1">
                                    </div>
                                    
                                    <!-- form class="col-md-6" -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Suba um arquivo de imagem aqui ou informe um link para uma imagem abaixo.</label>
        							        <input type="file" id="input_file">
        						        </div>
        						        <button type="button" id="button_enviar" class="btn btn-primary">Enviar</button>
        						        <hr/>
        						        <div class="form-group">
                                            <label>Link para imagem</label>
                                            <div class="form-group">
                                                <input type="text" id="input_url_imagem" name="input_url_imagem" class="form-control" >
                                            </div>
        						        </div>
        						        <button id="button_url_imagem" class="btn btn-primary">Atualizar</button>

        						    </div>
                                    <!-- /form -->
                                    
                                    <div class="col-md-4">
                                        <div id="grade">
                                            <div id="div_card_imagem" >
                                            </div>
                                        </div>                      
                                    </div>
                                    
                                    <div class="col-md-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">Escolha o fundo e insira um texto</div>
                            <div class="card-body">
                                <div class="row">                        
                                    <div class="col-md-1">
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="cor" value="amarelo" id="radio_amarelo" >
                                            <label class="form-check-label" for="radio_amarelo">Amarelo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="cinza" id="radio_cinza" checked>
                                            <label class="form-check-label" for="radio_cinza">Cinza</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="rosa" id="radio_rosa">
                                            <label class="form-check-label" for="radio_rosa">Rosa</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="verde" id="radio_verde">
                                            <label class="form-check-label" for="radio_verde">Verde</label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="imagem" id="radio_imagem">
                                            <label class="form-check-label" for="radio_imagem">Imagem (acima) </label>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="cor" value="azul" id="radio_azul">
                                            <label class="form-check-label" for="radio_azul">Azul</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="laranja" id="radio_laranja">
                                            <label class="form-check-label" for="radio_laranja">Laranja</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="roxo" id="radio_roxo">
                                            <label class="form-check-label" for="radio_roxo">Roxo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="cor" value="vermelho" id="radio_vermelho">
                                            <label class="form-check-label" for="radio_vermelho">Vermelho</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div id="grade">
                                            <div id="div_card_modelo" class="cke_editable" contenteditable="true" style="background-image: url(expositor/resources/card_cinza.png)">
                                            </div>
                                        </div>                      
                                    </div>
                                    
                                    <div class="col-md-1">
                                    </div>
                                </div>                        
                            </div>
                        </div>
                        
                        <div id="div_botoes" class="card my-3">
                            <div class="card-body">
                                <div class="row">
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
                    
                    <div class="col-md-3">
                    </div>
                    
                </div>
            </form>
            
        </div>
        
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






