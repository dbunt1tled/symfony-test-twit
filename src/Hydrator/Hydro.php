<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.10.18
 * Time: 16:16
 */

namespace App\Hydrator;


class Hydro
{
    public function __construct($attributes = []){
        foreach($attributes as $field=>$value){
            $this->$field = $value;
        }
    }

    function __set($name,$value){
        if(method_exists($this, $name)){
            $this->$name($value);
        }
        else{
            // Getter/Setter not defined so set as property of object
            $this->$name = $value;
        }
    }

    function __get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        elseif(property_exists($this,$name)){
            // Getter/Setter not defined so return property if it exists
            return $this->$name;
        }
        return null;
    }
    function __call($method, $params) {

        $var = lcfirst(substr($method, 3));

        if (strncasecmp($method, "get", 3) === 0) {
            return $this->$var;
        }
        if (strncasecmp($method, "set", 3) === 0) {
            $this->$var = $params[0];
        }
    }
}