<?php
define('CONFIG_NAMESPACE', '\\chilimatic\\lib\\config\\Config');

use chilimatic\lib\config\Config as Config;

/**
 * @param $file
 * @return bool
 */
function rq_file($file)
{
    if (!file_exists($file) || !is_readable($file)) return false;
    require_once $file;
    return true;
}


/**
 * load current framework library
 *
 * @param string $class_name
 * @return void
 */
function lib_loader( $class_name )
{

    if ( !class_exists(CONFIG_NAMESPACE) ) return;

    if (mb_strpos($class_name, '\\') !== false)
    {
        $test = explode('\\', $class_name);
        $class_name = array_pop($test);
    }

    // convert to lowercase
    $class_name = strtolower($class_name);
    $is_trait = mb_strpos($class_name, 'trait') !== false;

    $t_path = '';

    $path = (string) Config::get('lib_root') . (string) "/$class_name/class.php";

    if ($is_trait) $t_path = (string) Config::get('lib_root') . (string) "/$class_name/trait.php";

    if ( strpos($class_name, '_') !== false )
    {
        $tmp = explode('_', $class_name);
        $c = (int) count($tmp);
        $path = (string) Config::get('lib_root');

        for ( $i = 0 ; $i < $c ; $i++ )
        {
            $path .= (string) "/{$tmp[$i]}";
        }

        // if it's a trait try to add is at a trait
        if ($is_trait) $t_path = (string) $path . (string) ".trait.php";
        $path .= (string) ".class.php";

        unset($c, $tmp);
    }

    // only try to include it if the file exists
    if ( !$is_trait && file_exists($path) )
    {
        require_once $path;
    }

    if ($is_trait && file_exists($t_path))
    {
        require_once $t_path;
    }

    return;
}


/**
 * Autoloader for Libs view
 *
 * @param string $class_name            
 * @return void
 */
function main_loader( $class_name )
{

    if ( !class_exists(CONFIG_NAMESPACE) ) return;

    // convert to lowercase
    $class_name = strtolower($class_name);

    $folder_name = '';
    if ( strpos($class_name, '\\') !== false )
    {
        $root = explode('\\', $class_name);
        array_shift($root);
        $class_name = array_pop($root);
        $folder_name = implode('/', $root);
        unset($root);
    }

    switch (true) {
        case ( strpos($class_name, '_') !== false ):
            $part = explode('_', $class_name);
            $file_name = str_replace((string) array_shift($part) . '_', '', $class_name) . '.class.php';
            unset($part);
            break;
        default:
            $file_name = 'class.php';
            break;
    }

    $base_path[] = (string) APPLICATION_PATH . (string) "/$folder_name/$file_name";
    $base_path[] = (string) APPLICATION_PATH . (string) "/$folder_name/" . $class_name . '.class.php';
    unset($class_name, $file_name, $folder_name);

    while ($class = array_pop($base_path)) {
        if ( rq_file($class) ) break;
    }

    return;
}

spl_autoload_register('main_loader');
spl_autoload_register('lib_loader');
