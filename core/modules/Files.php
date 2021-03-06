<?php
class files
{
    static function renderfiles($files)
    {
        $res = array();
        for ($i = 0; $i < count($files['name']); $i++) {
            $res[] = array('name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]);
        }
        return $res;
    }

    static function getLastInt($dir = DIR, $ext = false)
    {
        $files = glob($dir."/*".($ext?'.'.$ext:''));
        $max = 0;
        foreach ($files as $file) {
            $name = explode('/', $file);
            $name = end($name);
            $name = explode('.', $name);
            $name = array_shift($name);
            if ($max < (int)$name) $max = $name;

        }
        return $max;
    }

    static function uploadfile($uploaddir, $file, $newname = false, $overwrite = false, $int = false)
    {

        if ($int) {
            $filee = explode('.', helpers::translitIt(basename($file['name'])));
            $ext = $filee[count($filee) - 1];
            if ($ext == '') $ext = false;
            $overwrite = true;
            $newname = '/'.(string)(self::getLastInt($uploaddir, $ext)+1).(($ext=='' or !$ext)?'':'.'.$ext);

        }
        $uploadfile = $newname === false ? $uploaddir . helpers::translitIt(basename($file['name'])) : $uploaddir . helpers::translitIt($newname);
        $i = 1;
        $lastname = explode('/', $uploadfile);
        $lastname = $lastname[count($lastname) - 1];
        $filee = explode('.', $uploadfile);
        $ext = $filee[count($filee) - 1];
        if (!$overwrite)
            while (file_exists($uploadfile)) {
                $f = $filee;
                $f[count($f) - 1] = $i++;
                $f [] = $ext;
                $uploadfile = implode('.', $f);
                $link = explode('/', $uploadfile);
                $lastname = $link[count($link) - 1];
            }
        $ret = (copy($file['tmp_name'], $uploadfile)) ? $lastname : false;

        return $ret;
    }

    static function file_exist($file)
    {
        return ($file['size'] > 0);
    }
}

?>