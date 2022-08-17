<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min=5,
     *      max=50, 
     *      minMessage="Votre pseudo doit contenir au moins {{ limit }} caractères",
     *      maxMessage="Votre pseudo ne doit pas contenir plus de {{ limit }} caractères")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @link https://symfony.com/doc/current/reference/constraints/Email.html
     * @Assert\Email(
     *     message = "Votre e-mail '{{ value }}' n'est pas valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(min=10)
     */
    private $content;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank
     * @link https://symfony.com/doc/current/reference/constraints/Choice.html#basic-usage
     * @Assert\Choice(choices = {1, 2, 3, 4, 5})
     * ! même si la valeur est un float, on doit valider les valeurs fournit par le formulaire
     * ! Comme on fournit des entier au formulaire, on valide des entiers ici
     */
    private $rating;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     * @link https://symfony.com/doc/current/reference/constraints/Choice.html#basic-usage
     * @link https://symfony.com/doc/current/reference/constraints/Choice.html#multiple
     * @Assert\Choice(
     *  choices = {
     *      "smile",
     *      "cry",
     *      "think",
     *      "sleep",
     *      "dream"},
     *   multiple = true
     * )
     *
     */
    private $reactions = [];

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotBlank
     * ! On n'utilise pas Assert\Date car il ne valide que les dates au format "string"
     */
    private $watchedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Movie::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    public function __construct()
    {
        // Le fait de mettre une valeur par défaut à un effet dans le formulaire
        // il va auto-remplir la date avec celle-ci, à savoir la date du jour
        $this->watchedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReactions(): ?array
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): self
    {
        $this->reactions = $reactions;

        return $this;
    }

    public function getWatchedAt(): ?\DateTimeImmutable
    {
        return $this->watchedAt;
    }

    public function setWatchedAt(\DateTimeImmutable $watchedAt): self
    {
        $this->watchedAt = $watchedAt;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }
}