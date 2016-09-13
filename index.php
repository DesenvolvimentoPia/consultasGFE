<?php

session_start();

if(empty($_SESSION['usuario'])) header("location: ../login.php");

else {

// Incluindo Cabeçalho Padrão
include "includes/cabecalho.html";

// Incluindo Corpo
include "includes/conteudo.html";

// Incluindo Scripts
include "includes/scripts.html";

// Incluindo rodapé Padrão
include "includes/rodape.html";

}

