<?php

namespace usuario;

class Usuario
{

    /**
     * Método para iniciar a sessão dentro da aplicação
     * @return boolean
     */
    private static function init()
    {
        return session_status() !== PHP_SESSION_ACTIVE ? session_start() : true;
    }

    /**
     * Método para encerrar a sessão do usuário
     * @return void
     */

    public static function logOut()
    {
        self::init();  // Verifica se a sessão está ativa

        session_unset(); // Remove todas as variáveis de sessão
        session_destroy(); // Destroi a sessão
        header("Location: ../paginas/pagina_inicial.php"); // Redireciona para a página inicial
        exit();
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

        $tipoUsuario = $_SESSION['usuario']['tipo'];

        if ($tipoUsuario == 'admin' && $tipoPermitido == 'admin') {
            // Se o usuário for admin, não faz nada
            return;
        } else if ($tipoUsuario !== $tipoPermitido) {
            // Redireciona para a pagina de login se o usuário não tiver permissão EMPRESA ou CLIENTE
            if ($tipoPermitido == 'empresa') {
                header("Location: ../paginas/login_empresas.php");
                exit();
            } else if ($tipoPermitido == 'cliente') {
                header("Location: ../paginas/login_clientes.php");
                exit();
            }
            header("Location: ../paginas/pagina_inicial.php");
            exit();
        }
    }
}
