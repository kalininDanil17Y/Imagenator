<?php
namespace Imagenator\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class UploadController
 * @package Imagenator\Main\Controller
 */
class UploadController
{
    /**
     * @var string
     */
    private $Imagedirectory = __DIR__ . '/../../public/images'; // путь к директории с изображениями

    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function showPage($response, $request)
    {
        $allowed_types = ['png', 'jpg', 'jpeg']; // показывать расширения
        $images = [];
        $i = 0;
        //пробуем открыть папку
        $dir_handle = @opendir($this->Imagedirectory) or die("Ошибка при открытии папки !!!");
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

    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function uploadPage($response, $request)
    {
        return $response->view("imagenator/upload");
    }

    /**
     * @param $response
     * @param $request
     * @return mixed
     */
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

        $imageFullName = $this->Imagedirectory . "/" . $uuid->toString() . '.' . $imageFormat;

        if (move_uploaded_file($image->getPathname(), $imageFullName)) {
            return $response->setBody(json_encode(['name' => $uuid->toString(), 'format' => $imageFormat]));
        }
        return $response->setBody('error');
    }

    /**
     * @param $response
     * @param $request
     * @return mixed
     */
    public function result($response, $request)
    {
        $name = $request->request->get('name');
        $format = $request->request->get('format');
        return $response->view("imagenator/result", ["id" => $name, "format" => $format, ]);
    }
}