<?php
class Session
{

    static function setcookie($name = '', $value = '', $time = 31556926, $domain = '/')
    {
        setcookie($name, $value = '', time() + $time, $domain);
    }

    static function delcookie($name)
    {
        Session::setcookie($name, '', 0);
    }

    static function setFlash($text)
    {

        $_SESSION['appication']['flash_message'] = $text;
    }

    static function getFlash()
    {
        if (isset($_SESSION['appication']['flash_message'])) {
            $ret = $_SESSION['appication']['flash_message'];
            $_SESSION['appication']['flash_message'] = '';
            return $ret;
        } else {
            return '';
        }
    }

}
