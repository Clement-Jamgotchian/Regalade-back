<?php

namespace App\Services;

use App\Entity\Recipe;
use App\Entity\User;

class UploadImageService
{
    public function upload($entity)
    {
        if (!method_exists($entity, 'getPicture') || empty($entity->getPicture()) || str_starts_with($entity->getPicture(), 'images/')) {
            return $entity;
        }
        if (get_class($entity) === User::class) {
            $folder = 'user-picture/';
        }

        if (get_class($entity) === Recipe::class) {
            $folder = 'recipe-picture/';
        }

        $base64 = explode("base64,", $entity->getPicture());
        $format = trim(str_replace('data:image/', '', $base64[0]), ';');

        $stringToConvert = base64_decode($base64[1]);
        
        $file = "images/" . $folder . uniqid() . '.' . $format;

        file_put_contents($file, $stringToConvert);

        $entity->setPicture($file);

        return $entity;
    }
}