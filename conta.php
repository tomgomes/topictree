<?php    
    session_start();
    
    /*
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
    */
    
    //$_SERVER["DOCUMENT_ROOT"] não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
    
    require $_SESSION["DOCUMENT_ROOT"] . "/include/php/restrito.inc";
?>
<!DOCTYPE html>
<html>
    <head>        
        <title>Minha Conta</title>
        <?php
        require $_SESSION["DOCUMENT_ROOT"] . "/include/html/meta.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/js/jquery-block-ui.inc";
        require $_SESSION["DOCUMENT_ROOT"] . "/include/php/funcoes.inc";
        
        link_href("conta/conta.css");
        link_href("css/aguarde.css");
        script_src("conta/conta.js");
        script_src("js/topictree/topictree.js");
        script_src("js/jquery-mask/jquery.mask.min.js");
        script_src("js/jquery-capitalizar/capitalizar.js");
        ?>
        
        <!--  link rel="stylesheet" type="text/css" href="../css/black_overlay.css?ultimaAtualizacao=<php echo filemtime($_SERVER["DOCUMENT_ROOT"] . '/css/black_overlay.css')?>" -->
        
        <script>        
        $(document).ready(function () {
            
            var bloquear = true;            
            $.blockUI.defaults.message = $('#span_aguarde');
            $.blockUI.defaults.baseZ = 1200;            
            $(document).ajaxStart(function(){
                if(bloquear){ 
                    $.blockUI(); //ATENÇÃO: Para funcionar o valor de async deve ser true
                }//end if 
            });                
            $(document).ajaxStop($.unblockUI);
            
            //Nota: tentei usar CSS e as propriedades background-image e content, 
            //porém elas apresentam problemas entre o chrome e o firefox,
            //então usei o atributo src            
            $('#img_minha_conta').attr('src', '/resources/minha_conta.png');
            
            ajax_resquest_carregar();
            
            jQuery('#input_text_nome, #input_text_sobrenome').keyup(function(){ 
                this.value = this.value.replace(/[^A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]/g,'');
            });
            
            $('#input_text_datanascimento').mask('00/00/0000');
            
            $("#input_text_nome").blur(function(){
                capitalizar(this);
            });
            
            $("#input_text_sobrenome").blur(function(){
                capitalizar(this);
            });
            
            //Quando clicar no botão salvar
            var senha = "";
            $("#button_salvar").on('click', function(){
            	ajax_request_salvar(senha);
            });
            
            //Quando o modal e-mail é mostrado
            $('#modalEmail').on('shown.bs.modal', function (e) {                
                $('#input_text_email_novo').val('');
                $('#input_text_email_repetido').val('');            	
                $('#input_text_email_novo').trigger('focus');                                
            });            
            
            //Captura qualquer botão que foi clicado (dentro da janela modal e no footer)
            var $botao;
            $('#modalEmail .modal-footer button').on('click', function (e) {
                $botao = $(e.target);  
            });
            
            //Quando o modal e-mail fecha
            $('#modalEmail').on('hidden.bs.modal', function (e) {
                if($botao == undefined){
                    return
                };
            	var acao = $botao[0].innerText;
                if(acao == "Alterar"){
                    var novo = $('#input_text_email_novo').val();
                    var repetido = $('#input_text_email_repetido').val();
                    if(novo == "" && repetido == ""){
                        return;
                    }//end if                    
                    if(novo != repetido){
                    	var data = {mensagem:"Alteração de e-mail falhou!<br/>Por favor, repita o e-mail corretamente.",
                    			    pagina:""};
                    	mostrar_mensagem(data);
                    }else{
                    	$('#input_text_email').val(novo);                    	
                    }//end if
                }//end if    
                $botao = undefined;                    	
            });
            
            //Quando o modal senha é mostrado
            $('#modalSenha').on('shown.bs.modal', function (e) {                
                $('#input_text_senha_nova').val('');
                $('#input_text_senha_repetida').val('');                
                $('#input_text_senha_nova').trigger('focus');                               
            });            
            
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
                if(acao == "Alterar"){
                    var nova = $('#input_text_senha_nova').val();
                    var repetida = $('#input_text_senha_repetida').val();
                    if(nova == "" && repetida == ""){
                        return;
                    }//end if                    
                    if(nova != repetida){
                        var data = {mensagem:"Alteração de senha falhou!<br/>Por favor, repita a senha corretamente.",
                                    pagina:""};
                        mostrar_mensagem(data);
                    }else{
                        $('#input_text_senha').val(nova);
                    	senha = nova;                       
                    }//end if
                }//end if    
                $botao = undefined;                     
            });
            
        });//end ready
        
        </script>        
    </head>
    
    <body class="text-white">
        
        <div class="container-fluid page-container" >
            <div class="row p-3">
                <img id="img_minha_conta" /><span>Minha conta</span>
            </div>
            
            <form id="linha_formulario" class="form-horizontal py-3">                
                <div id="div_dialog" title="Aviso" style="display:none">
                    <p id="p_dialog">
                    </p>
                </div>
                
                <span id="span_aguarde" style="display:none" class="aguarde position-relative">
                    <img src="../resources/ajax-loader-red.gif" /> aguarde...
                </span>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                    <label for="input_text_usuario" 
                           class="col-md-2 col-form-label">
                        Nome de usuário
                    </label>
                    
                    <div class="col-md-4">
                        <input type="text" 
                               readonly 
                               class="form-control-plaintext"
                               style="color: white" 
                               id="input_text_usuario" 
                               value=""
                               />
                    </div>
                    <div class = "col-md-3">
                    </div>
                </div>
                                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                    
                    <label for="input_text_nome" 
                           class="col-md-2 col-form-label">
                        Nome
                    </label>
                    
                    <div class="col-md-4">
                    <input type="text" 
                           placeholder="Nome" 
                           class="form-control caixa_texto" 
                           id="input_text_nome"
                           name="nome"
                           maxlength="25"
                           />
                    </div>
                    
                    <div class = "col-md-3">
                    </div>                    
                </div>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                                        
                    <label for="input_text_sobrenome" 
                           class="col-md-2 col-form-label">
                        Sobrenome
                    </label>
                    
                    <div class="col-md-4">
                    <input type="text" 
                           placeholder="Sobrenome" 
                           class="form-control caixa_texto" 
                           id="input_text_sobrenome"
                           name="sobrenome"
                           maxlength="50"
                           />
                    </div>
                    
                    <div class = "col-md-3">
                    </div>                    
                </div>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                                        
                    <label for="input_text_datanascimento" 
                           class="col-md-2 col-form-label">
                        Data de nascimento
                    </label>
                    
                    <div class="col-md-4">
                    <input type="text" 
                           placeholder="dd/mm/aaaa nascimento" 
                           class="form-control caixa_texto" 
                           id="input_text_datanascimento"
                           name="datanascimento"
                           />
                    </div>
                    
                    <div class = "col-md-3">
                    </div>                    
                </div>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                    
                    <label for="input_text_email" 
                           class="col-md-2 col-form-label">
                        E-mail
                    </label>
                    
                    <div class="col-md-2">
                        <input type="text" 
                               readonly 
                               class="form-control-plaintext"
                               style="color: white" 
                               id="input_text_email" 
                               value=""
                               />
                    </div>
                    
                    <button type="button"
                            class="col-md-2 btn btn-primary"
                            data-toggle="modal" 
                            data-target="#modalEmail"
                            style="border-radius: 10px;" >
                        alterar e-mail
                    </button>
                    
                    <div class = "col-md-3">
                    </div>
                </div>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                    
                    <label for="input_text_usuario" 
                           class="col-md-2 col-form-label">
                        Senha
                    </label>
                    
                    <div class="col-md-2">
                        <input type="password" 
                               readonly 
                               class="form-control-plaintext"
                               style="color: white" 
                               id="input_text_senha" 
                               value="*****"
                               />
                    </div>
                    
                    <button type="button" 
                            class="col-md-2 btn btn-primary" 
                            style="border-radius: 10px;" 
                            data-toggle="modal" 
                            data-target="#modalSenha">
                        alterar senha
                    </button>
                    
                    <div class = "col-md-3">
                    </div>
                </div>
                
                <div class="form-group-md row my-1">
                    <div class = "col-md-3">
                    </div>
                    
                    <label for="input_text_usuario" 
                           class="col-md-2 col-form-label">
                    </label>
                    
                    <div class="col-md-2">
                    </div>
                    
                    <button type="button" 
                            class="col-md-2 btn btn-primary" 
                            style="border-radius: 10px;"
                            id="button_salvar"
                            >
                        Salvar
                    </button>
                    
                    <div class = "col-md-3">
                    </div>
                </div>
                
            </form>                        
		</div>             
        
        <div class="modal fade" id="modalEmail" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-dark" id="staticBackdropLabel">Alterar e-mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-dark">
                <!-- Por favor, informe o seu novo e-mail -->
                
                <input type="email" 
                       placeholder="e-mail" 
                       class="form-control m-2" 
                       id="input_text_email_novo"
                       name="email"
                       maxlength="50"
                       />                    
                
                <input type="email" 
                       placeholder="repita o e-mail" 
                       class="form-control m-2" 
                       id="input_text_email_repetido"
                       name="email_repetido"
                       maxlength="50"
                       />                    
              
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="button_cancelar" >Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="button_salvar">Alterar</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal fade" id="modalSenha" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-dark" id="staticBackdropLabel">Alterar senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body text-dark">
                <!-- Por favor, informe a sua nova senha -->
                
                <input type="password" 
                       placeholder="senha" 
                       class="form-control m-2" 
                       id="input_text_senha_nova"
                       name="senha_nova"
                       maxlength="50"
                       />                    
                
                <input type="password" 
                       placeholder="repita a senha" 
                       class="form-control m-2" 
                       id="input_text_senha_repetida"
                       name="senha_repetida"
                       maxlength="50"
                       />                    
              
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="button_cancelar2" >Cancelar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="button_salvar2">Alterar</button>
              </div>
            </div>
          </div>
        </div>
    </body>
</html>

<?php
?>










