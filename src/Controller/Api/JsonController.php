<?php

namespace App\Controller\Api;

use App\Models\CustomJsonError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Ajoute des méthodes pour les erreurs communes d'API
 */
class JsonController extends AbstractController
{
    /**
     * Renvoit un objet JsonError avec le code 404
     *
     * @param string $message le message qui apparaitra dans l'objet d'erreur
     * @return JsonResponse
     */
    public function json404(string $message): JsonResponse
    {
        // notre objet custom pour avoir un message d'erreur personalisé
        $error = new CustomJsonError();
        $error->errorCode = Response::HTTP_NOT_FOUND;
        $error->message = $message;

        // je renvoit un JsonResponse 
        // pour que ça soit compatible avec toutes les méthodes d'API
        return $this->json(
            $error,
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Renvoit un objet JsonError avec le code 422
     *
     * @param ConstraintViolationListInterface $errors
     * @return JsonResponse
     */
    public function json422($errors): JsonResponse
    {
        // notre objet custom pour avoir un message d'erreur personalisé
        $error = new CustomJsonError();
        $error->errorCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        $error->setErrorValidation($errors);
        $error->message = "Erreurs sur la validation de l'objet";

        // je renvoit un JsonResponse 
        // pour que ça soit compatible avec toutes les méthodes d'API
        return $this->json(
            $error,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * renvoit une réponse HTTP_OK avec les datas et les groupes de sérialisation
     *
     * @param mixed $data données à sérialiser
     * @param array $groups groupes de serialisation : ["group1", "group2"]
     * @return JsonResponse
     */
    public function json200($data, array $groups = []): JsonResponse
    {
        return $this->json(
            // data
            $data,
            // CODE http
            Response::HTTP_OK,
            // pas d'entete supplémentaires
            [],
            // on spécifie les groupes de serialisations
            [
                "groups" => $groups
            ]
        );
    }

    /**
     * renvoit une réponse HTTP_CREATED avec les data et les groupes de sérialisation
     *
     * @param mixed $data données à sérialiser
     * @param array $groups groupes de serialisation : ["group1", "group2"]
     * @return JsonResponse
     */
    public function json201($data, array $groups = []): JsonResponse
    {
        return $this->jsonByCode(
            Response::HTTP_CREATED,
            $data,
            $groups
        );
    }

    /**
     * version générique pour tout les codes
     *
     * @param integer $httpCode
     * @param mixed $data
     * @param array $groups
     * @return JsonResponse
     */
    private function jsonByCode(int $httpCode, $data, array $groups = []): JsonResponse
    {
        return $this->json(
            $data,
            $httpCode,
            // pas d'entete supplémentaires
            [],
            // on spécifie les groupes de serialisations
            [
                "groups" => $groups
            ]
        );
    }
}