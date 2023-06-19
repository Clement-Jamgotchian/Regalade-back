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

        $file = "images/" . $folder . uniqid() . '.png';

        file_put_contents($file, base64_decode($entity->getPicture()));

        $entity->setPicture($file);

        return $entity;
    }
}