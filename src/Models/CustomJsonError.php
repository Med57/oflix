<?php

namespace App\Models;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Objet custom pour les erreurs dans mon API
 */
class CustomJsonError
{
    /**
     * Code d'erreur
     *
     * @var int
     */
    public $errorCode;

    /**
     * Message d'errreur
     *
     * @var string
     */
    public $message;

    /**
     * Liste de messages d'erreur lors d'une validation
     *
     * @var array
     */
    public $messagesValidation = [];

    public function setErrorValidation(ConstraintViolationListInterface $errors)
    {
        // dump($errors);
         /* eg avec une seule erreur
        Symfony\Component\Validator\ConstraintViolationList {#1034 ▼
        -violations: array:1 [▼
            0 => Symfony\Component\Validator\ConstraintViolation {#1051 ▼
            -message: "Your genre name must be at least 5 characters long"
            -messageTemplate: "Your genre name must be at least {{ limit }} characters long"
            -parameters: array:2 [▶]
            -plural: 5
            -root: App\Entity\Genre {#1019 ▶}
            -propertyPath: "name"
            -invalidValue: "az"
            -constraint: Symfony\Component\Validator\Constraints\Length {#1030 ▶}
            -code: "9ff3fdc4-b214-49db-8718-39c315e33d45"
            -cause: null
            }
        ]
        }
        */

        foreach ($errors as $error) {
            $this->messagesValidation[] = "La valeur ". $error->getInvalidValue() . " ne respecte pas les règles de validation de la propriété '" . $error->getPropertyPath() . "'";
        }
    }
}