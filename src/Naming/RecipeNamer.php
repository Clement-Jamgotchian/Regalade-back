<?php
namespace App\Naming;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
 
class RecipeNamer implements NamerInterface
{

    public function name($obj,PropertyMapping $field):string
    {
        return "images/recipe-picture/" . uniqid().".".$obj->getPictureFile()->guessExtension();

    }
}