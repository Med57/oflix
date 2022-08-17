<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("show_movie")
     * @Groups("api_genres")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("show_movie")
     * @Groups("show_genre")
     * @Groups("api_genres")
     * 
     * @Assert\NotBlank
     * @Assert\Length(
     *      min=5,
     *      max=50, 
     *      minMessage="Le nom du genre doit contenir au moins {{ limit }} caractères",
     *      maxMessage="Le nom du genre ne doit pas contenir plus de {{ limit }} caractères")
     */
    private $name;

    /**
     * function de démo pour personaliser un affichage d'une instance
     * utilisé pour les formulaire ou twig
     */
    public function getTagada(): string
    {
        return $this->name;
    }
    
    /**
     * renvoit le nombre de films
     * @Groups("show_genre")
     * @Groups("api_genres")
     * 
     * @return int nombre de films pour ce genre
     */
    public function getCountMovies(): int
    {
        return count($this->movies);
    }

    /**
     * @ORM\ManyToMany(targetEntity=Movie::class, mappedBy="genres")
     * @Groups("show_genre")
     */
    private $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->addGenre($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            $movie->removeGenre($this);
        }

        return $this;
    }
}
