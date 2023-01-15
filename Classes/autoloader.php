<?php
spl_autoload_register('Classesautoloader');
function Classesautoloader($className)
{
    $path = __DIR__."/$className.php";
    if(file_exists($path))
    {
       require_once $path;
    }
}