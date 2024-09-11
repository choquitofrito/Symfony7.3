<?php
namespace App\Form\DataTransformer;

use App\Entity\Ingredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IngredientTransformer implements DataTransformerInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    
    public function transform($value):string
    {
        if (!$value instanceof Ingredient) {
            return '';
        }

        return $value->getId();
    }


    // quand on fait le submit
    public function reverseTransform($value):Ingredient
    {
        if (!$value) {
            return null;
        }

        // Load the Ingredient entity using the ID
        $Ingredient = $this->entityManager->getRepository(Ingredient::class)->find($value);

        if (!$Ingredient) {
            throw new TransformationFailedException(sprintf(
                'The Ingredient with ID "%s" does not exist!',
                $value
            ));
        }

        return $Ingredient;
    }
}
