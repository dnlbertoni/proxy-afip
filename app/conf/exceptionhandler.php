<?php
/*
set_error_handler(function (int $number, string $message) { echo "Handler captured error $number: '$message'" . PHP_EOL .'<br/>' ;});
*/

set_error_handler(function ($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // Este código de error no está incluido en error_reporting
        return false;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>Mi ERROR</b> [$errno] $errstr<br />\n";
        echo "  Error fatal en la línea $errline en el archivo $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Abortando...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>Mi WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>Mi NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Tipo de error desconocido: [$errno] $errstr, en la línea $errline en el archivo $errfile<br />\n";
        break;
    }

    /* No ejecutar el gestor de errores interno de PHP */
    return true;
});

?>