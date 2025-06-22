<?php
namespace usuario;

class Usuario
{
    private static function init()
    {
        return session_status() !== PHP_SESSION_ACTIVE ? session_start() : true;
    }

    public static function logOut()
    {
        self::init();
        session_unset();
        session_destroy();
        header("Location: ../paginas/pagina_inicial.php");
        exit();
    }

    public static function verificarPermissao($tipoPermitido)
    {
        self::init();

        if (!isset($_SESSION['usuario'])) {
            header("Location: ../paginas/pagina_inicial.php");
            exit();
        }

        $tipoUsuario = $_SESSION['usuario']['tipo'];

        if ($tipoUsuario == 'admin' && $tipoPermitido == 'admin') {
            return;
        } else if ($tipoUsuario !== $tipoPermitido) {
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

    /**
     * Método para validar se a sessão da empresa está ativa
     * @return bool
     */
 public static function validarSessaoEmpresa()
{
    self::init();
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'empresa';
}

}
