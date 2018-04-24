<?php
/**
 * PHPGamification SPL autoloader.
 * @param string $classname The name of the class to load
 */
function PHPGamificationAutoload($className)
{
//    var_dump($class);
    $classArray = explode("\\", $className);
    $class = array_pop($classArray);
    //    var_dump($class);
    //    $class = str_replace('TiagoGouvea\\', '', $class);
    //    $class = str_replace('\\', '', $class);
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.class.php';
    //Can't use __DIR__ as it's only in PHP 5.3+
    if (is_readable($file))
        require_once $file;
    else {
//        var_dump($file);
//        die();
        $class = array_pop($classArray) . DIRECTORY_SEPARATOR . $class;
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.class.php';
        if (is_readable($file))
            require_once $file;
        else {
            // Notfound.
        }
    }
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
    //SPL autoloading was introduced in PHP 5.1.2
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        spl_autoload_register('PHPGamificationAutoload', true, true);
    } else {
        spl_autoload_register('PHPGamificationAutoload');
    }
} else {
    /**
     * Fall back to traditional autoload for old PHP versions
     * @param string $classname The name of the class to load
     */
    function __autoload($classname)
    {
        PHPGamificationAutoload($classname);
    }
}
