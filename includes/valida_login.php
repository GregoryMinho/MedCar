<?php
namespace includes;

class Usuario{
 
    /**
     * Método para iniciar a sessão dentro da aplicação
     * @return boolean
     */
private static function init(){
return session_status() !== PHP_SESSION_ACTIVE ? session_start() : true;
}

/**
 * Método para verificar tipo de usuario 
 * @param string $tipoPermitido
 */
public static function verificarPermissao($tipoPermitido)
{
    self::init();

    if (!isset($_SESSION['usuario'])) {
        // Redireciona para a página de login se o usuario de usuário não estiver definido
        header("Location: ../paginas/pagina_inicial.php");
        exit();
    }
    
    $tipoUsuario = $_SESSION['usuario']['tipo'] ;
    
    if ($tipoUsuario == 'ADMIN') {
        // Se o usuário for admin, não faz nada
        return;
    } else if ($tipoUsuario !== $tipoPermitido) {
        // Redireciona para uma página inicial se o usuário não tiver permissão EMPRESA ou CLIENTE
        header("Location: ../paginas/pagina_inicial.php");
        exit();
    }
}
}


