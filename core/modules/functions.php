<?php
function i($array = array(), $sum=''){
    return implode($array,$sum);
}
function g($name){
    return isset($_POST[$name])?$_POST[$name]:(isset($_GET[$name])?$_GET[$name]:false);
}
function getInt($string){
    $d = 0;
    $res = 0;
    for($i=strlen($string)-1; $i>=0; $i--){
        if(is_numeric($string[$i])){
            $res+=pow(10,$d)*$string[$i];
            $d+=1;
        }
    }
    return $res;
}
?>