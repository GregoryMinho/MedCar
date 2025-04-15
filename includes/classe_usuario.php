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

    public static function logout()
    {
        self::init();
        require_once("./login/vendor/autoload.php");
        
        $client = new Google_Client(); // Instancia o cliente
        $client->setAuthConfig('../includes/client_secret_162031456903-j67l39klr0m4p0js3cf4pjsl7kleqmp2.apps.googleusercontent.com.json'); /* REPLACE WITH YOUR CREDENTIALS.json FILE NAME FROM GOOGLE */
        
        unset($_SESSION['upload_token']); // Remove o token de upload da sessão
        $client->revokeToken();
        
        session_unset(); // Remove todas as variáveis de sessão
        session_destroy(); // Destroi a sessão
        header("Location: ../paginas/pagina_inicial.php"); // Redireciona para a página inicial
        exit();
    }

    /**
     * Método para verificar se o usuário está logado
     * @return boolean
     */

    public function isLogged()
    {
        self::init();

        return isset($_SESSION['usuario']);
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
