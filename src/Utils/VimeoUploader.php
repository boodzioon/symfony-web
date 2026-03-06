<?php

namespace App\Utils;

use App\Utils\Interfaces\UploaderInterface;

class VimeoUploader implements UploaderInterface
{

    public $file;

    public function __construct()
    {
    }

    public function upload($file)
    {
        return null;
        // $videoNumber = random_int(1, 10000000);
        // $fileName = $videoNumber . '.' . $file->guessExtension();

        // try {
        //     $file->move($this->getTargetDirectory(), $fileName);
        // } catch (FileException $e) {

        // }
        // $originalFileName = $this->clear(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        // return [$fileName, $originalFileName];
    }

    public function delete($path)
    {
        // $fileSystem = new Filesystem;

        // try {
        //     $fileSystem->remove("." . $path);
        // } catch (IOExceptionInterface $e) {
        //     echo 'An error occurred while deleting your file at ' . $e->getPath();
        // }

        return true;
    }

}