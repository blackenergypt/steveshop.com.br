<?php

namespace app\lib;

class Upload
{

    public static function image($dir, $file)
    {
        if (!empty($file["name"])) {
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $file["type"])){
                return [ 'status' => false, 'message' => 'O arquivo não é uma imagem' ];
            }

            preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $file["name"], $ext);
            $image = md5(uniqid(time())) . "." . $ext[1];
            $dir_image = $dir . $image;

            if(!move_uploaded_file($file["tmp_name"], $dir_image))
            {
                return [ 'status' => false, 'message' => 'Sistema sem permissão.' ];
            }

            return [ 'status' => true, 'src' => substr($dir_image, 1) ];
        }
        return [ 'status' => true, 'src' => 'NULL' ];
    }

}