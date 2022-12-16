<?php
    //logout
    if(session_start()):
        session_destroy();
    endif;
    
    //$_SERVER não funciona com PHP CLI (php.exe)
    $_SESSION["DOCUMENT_ROOT"] = __DIR__;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        require $_SESSION['DOCUMENT_ROOT'] . "/include/html/meta.inc";
        require $_SESSION['DOCUMENT_ROOT'] . "/include/js/jquery-bootstrap.inc";
        require $_SESSION['DOCUMENT_ROOT'] . "/include/php/funcoes.inc";
        
        link_href('home/home.css');
        ?>
        
        <title>Topic Tree</title>
    </head>
    <body class="tela">        
        <div class="container">
            <div class="row painel_principal">
                <div class="col-xl-12">
                    <div id="div_painel_principal" class="painel clearfix">
                        <?php a_href("login.php") ?>
                            <button type="button" class="btn btn-info float-right">
                                FAZER LOGIN
                            </button>
                        </a>                     
                        <span class="cor_texto">
                        Compartilhe<br/>        
                        Seus dados organizados e sempre a mão.<br/>
                        Contribuindo com toda a comunidade.
                        </span>
                        
                        <div class="d-flex justify-content-center">
                        <div style="padding: 5% 0">
                        <div style="padding: 5% 0">
                            <?php a_href("login.php") ?>
                                <button type="button" class="btn botao_crie_seu_tutorial sombra">
                                    Crie seu tutorial
                                </button>
                            </a>
                        </div>
                        </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="row painel2_cor">
	            <div class="col-xl-3 painel2_cor">				    
	                <div  id="div_painel2_texto" class="painel">
                        <span class="cor_texto">        
                        Tutorial<br/>
                        Crie vários tutoriais com<br/>
                        esta magnífica ferramenta.
                        </span>	                
	                </div>
                </div>
                <div class="col-xl-9 painel2_cor">                                  
                    <div id="div_painel2_imagem" class="painel">
                    </div>                    
                </div>
            </div>
            <div class="row painel3_cor">
                <div class="col-xl-3 painel3_cor">                  
                    <div  class="painel">
                        <span class="cor_texto">        
                        Ajuda Integrada<br/>
                        Adicione as informações, fornecendo ajuda com rapidez, <br/>
                        atualizada e de forma fácil aos usuários do seu sistema.<br/>                        
                        Integre o seu aplicativo com esta ferramenta<br/>
                        </span>                 
                    </div>
                </div>
                <div class="col-xl-9 painel3_cor">
                    <div id="div_painel3_imagem" class="painel">
                    </div>                                                      
                </div>                                                                
            </div>   
            
            <div class="row painel4_cor">
                <div class="col-xl-3 painel4_cor">                  
                    <div  id="div_painel4_texto" class="painel4">
                        <span class="cor_texto">
                        Exemplo de tutorial        
                        </span>                 
                    </div>
                </div>
                <div class="col-xl-9 painel4_cor">                                  
                    <div id="div_painel4_imagem" class="painel4">
                    </div>                    
                </div>
            </div>
            
             
        </div>
    </body>
</html>