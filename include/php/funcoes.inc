<?php
//retorna a url de um css com o tempo do arquivo
//evitando que o usuario rode o site
//com um arquivo desatualizado ?t=6543131
function link_href($caminho){
    $caminho_absoluto = $_SESSION['DOCUMENT_ROOT'] . "/" . $caminho;
    $tempo = filemtime($caminho_absoluto);
    
    //Ex: <link rel='stylesheet' type='text/css' href='home/home.css?t=1581606035'>
    echo "\n        ";
    echo "<link rel='stylesheet' type='text/css' href='" . $caminho . "?t=" . $tempo . "'>";
}

//retorna a url de um link com o tempo do arquivo
//evitando que o usuario rode o site
//com um arquivo desatualizado ?t=6543131
//IMPORTANTE: Não esqueça a tag de fechamento </a> no seu html
function a_href($caminho){
    $caminho_absoluto = $_SESSION['DOCUMENT_ROOT'] . "/" . $caminho;
    $tempo = filemtime($caminho_absoluto);
    
    //Ex: <a href="login.php?t=1620156495">
    echo "<a href='" . $caminho . "?t=" . $tempo . "'>\n";
}

//retorna a url de um script com o tempo do arquivo
//evitando que o usuario rode o site
//com um arquivo desatualizado ?t=6543131
//IMPORTANTE: Não esqueça a tag de fechamento </a> no seu html
function script_src($caminho){
    $caminho_absoluto = $_SESSION['DOCUMENT_ROOT'] . "/" . $caminho;
    $tempo = filemtime($caminho_absoluto);
    
    //Ex:
    echo "\n        ";
    echo "<script type='text/javascript' src='" . $caminho . "?t=" . $tempo . "'> </script>";
}

//retorna window.location = 'arquivo.ext?t=000000';
//assim evita que o usuario rode o site
//com um arquivo desatualizado ?t=6543131
function window_location($caminho){
    $caminho_absoluto = $_SESSION['DOCUMENT_ROOT'] . "/" . $caminho;
    $tempo = filemtime($caminho_absoluto);
    
    //Ex:
    echo "window.location = '" . $caminho . "?t=" . $tempo . "';\n";
}
?>