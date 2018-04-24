<?php
/**
 * Created by PhpStorm.
 * User: TiagoGouvea
 * Date: 08/08/15
 * Time: 13:32
 */

namespace gameme\PHPGamification\Model;


use Exception;

abstract class Entity
{
    abstract function __construct($stdClass = null);

    protected function fillAtributes($stdClass, $obj)
    {
        $sourceVars = array_keys(get_object_vars($stdClass));
        $destinationVars = array_keys(get_object_vars($obj));
        foreach ($sourceVars as $var) {
//            var_dump($var);
            $fixedVar = $var;
            $underLinePos = strpos($fixedVar, '_');
            while ($underLinePos !== false) {
                if ($underLinePos !== false) {
                    $letter = $fixedVar[$underLinePos + 1];
//                    $fixedVar[$underLinePos + 1] = strtoupper($letter);
                    $fixedVar = str_replace("_$letter", strtoupper($letter), $fixedVar);
                }
                $underLinePos = strpos($fixedVar, '_');
            }
            if (!in_array($fixedVar, $destinationVars)) {
                throw new Exception("Invalid atribute on destination object: " . get_class($obj) . ': ' . $fixedVar);
            }
            $obj->$fixedVar = $stdClass->$var;
//            echo $fixedVar . '=' . $stdClass->$var . '<br>';
//            var_dump($var);
//            var_dump($fixedVar);
//            echo "<br>";

        }
    }

    public function get($atribute)
    {
        return $this->$atribute;
    }

}