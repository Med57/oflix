<?php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Entity denormalizer
 */
class DoctrineDenormalizer implements DenormalizerInterface
{
    /** @var EntityManagerInterface **/
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Ce denormalizer doit-il s'appliquer sur la donnée courante ?
     * Si oui, on appelle $this->denormalize()
     * 
     * $data => l'id du Genre
     * $type => le type de la classe vers laquelle on souhaite dénormaliser $data
     * 
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        // exemple pour Movie::genres
        // "genres" : [1,2]
        // $data = 1 (puis 2 car c'est un tableau)
        // $type = le FQCN de la classe à denormalizer App\Entity\Genre
        // pour savoir si je peut denormalizer, je regarde à la fois que l'on me demande:
        // une entité
        // avec un ID (numeric)
        // TRUE si je suis capable de le faire
        return strpos($type, 'App\\Entity\\') === 0 && (is_numeric($data));
    }

    /**
     * Cette méthode sera appelée si la condition du dessus est valide
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // $data = 1 (puis 2 car c'est un tableau)
        // $type = le FQCN de la classe à denormalizer App\Entity\Genre
        // avec l'entityManager on peut faire un find sans utiliser le repository
        // en donnant en plus de l'ID le FQCN de l'entité
        
        // ex : $this->em->find('App\Entity\Genre', 1);
        return $this->em->find($class, $data);
    }
}