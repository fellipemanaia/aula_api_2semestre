<?php

    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'etecmcm';
    

    $conexao = new mysqli($host,$usuario,$senha,$banco);

    if ($conexao->error){
        die('Falha ao conectar no bd: ' . $conexao-> error);
    }
    // else{
    //     echo 'Conectado com sucesso';
    // }
?>