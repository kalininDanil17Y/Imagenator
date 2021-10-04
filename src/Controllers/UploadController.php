<?php
namespace Imagenator\Main\Controllers;
use Ramsey\Uuid\Uuid;

class UploadController
{
    public function showPage($response, $request)
    {
        $directory       = __DIR__ . '/../../images'; // путь к директории с изображениями
        $allowed_types = ['png', 'jpg', 'jpeg', 'gif']; // показывать расширения
        $images = [];
        $i = 0;
        //пробуем открыть папку
        $dir_handle = @opendir($directory) or die("Ошибка при открытии папки !!!");
        while ($file = readdir($dir_handle))
        {
            if($file == "." || $file == "..") continue;
            $file_parts = explode(".",$file);
            $ext = strtolower($file_parts[1]);

            if(in_array($ext,$allowed_types))
            {
                $images[$file] = $file_parts[0];
                $i++;
            }

        }
        closedir($dir_handle);

        return $response->view("imagenator/show", ['images' => $images]);
    }

    public function uploadPage($response, $request)
    {
        return $response->view("imagenator/upload");
    }

    public function upload($response, $request)
    {
        $uuid = Uuid::uuid4();

        $image = $request->files->get('image');


        if ($image->getSize() > 200000) {
            return $response->setBody('error');
        }

        $imageFormat = explode('.', $image->getClientOriginalName());
        $imageFormat = $imageFormat[1];

        $imageType = $image->getClientMimeType();
        if ($imageType !== 'image/jpeg' && $imageType !== 'image/png') {
            return $response->setBody('error');
        }

        $imageFullName = __DIR__ . '/../../images/' . $uuid->toString() . '.' . $imageFormat;

        if (move_uploaded_file($image->getPathname(), $imageFullName)) {
            return $response->setBody($uuid->toString());
        }
        return $response->setBody('error');
    }

    public function result($response, $request)
    {
        $name = $request->request->get('name');
        return $response->view("imagenator/result", ["id" => $name]);
    }
}