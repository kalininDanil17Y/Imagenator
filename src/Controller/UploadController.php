<?php
namespace Imagenator\Controller;
use Ramsey\Uuid\Uuid;
use Imagenator\Models\Images;

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
        $images = Images::orderBy('dateUploaded', 'DESC')->get();

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

        $imageName = $uuid->toString() . '.' . $imageFormat;
        $imageFullName = $this->Imagedirectory . "/" . $imageName;

        if (move_uploaded_file($image->getPathname(), $imageFullName)) {
            $image = Images::create([
                'uuid' => $uuid,
                'name' => $imageName,
                'ipAddress' => $request->getClientIp()
            ]);
            if (empty($image)) {
                unlink($imageFullName);
                return $response->setBody('error');
            }
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