<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Redireciona para a página de login se não estiver logado
    header("Location: login.php");
    exit();
}

// Verifica o tipo de usuário
$tipoUsuario = $_SESSION['usuario']['tipo'];

// Função para verificar permissões
function verificarPermissao($tipoPermitido)
{
    global $tipoUsuario;
    if ($tipoUsuario == 'ADMIN') {
        // Se o usuário for admin, não faz nada
        return;
    } else if ($tipoUsuario !== $tipoPermitido) {
        // Redireciona para uma página inicial se o usuário não tiver permissão EMPRESA ou CLIENTE
        header("Location: pagina_inicial.php");
        exit();
    }
}
