<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommuneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommuneRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['commune:lire']],     // groupes utilisés quand on lit
    denormalizationContext: ['groups' => ['commune:ecrire']], // groupes utilisés quand on écrit
)]
class Commune
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['commune:lire'])] // lu jamais écrit
    private ?int $id = null;



    #[ORM\Column(length: 5, unique: true, nullable: true)]
    private ?string $codeInsee = null;

    #[ORM\Column(length: 255)]
    #[Groups(['commune:lire', 'commune:ecrire'])] // lu et écrit
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    // Peut être à 0, jamais négative
    #[ORM\Column(nullable: true)]
    #[Groups(['commune:lire', 'commune:ecrire'])]
    #[Assert\PositiveOrZero]
    private ?int $population = null;

    // En hectares
    #[ORM\Column(nullable: true)]
    #[Groups(['commune:lire', 'commune:ecrire'])]
    #[Assert\Positive]
    private ?float $surface = null;

    // Une commune est dans un seul département
    #[ORM\ManyToOne(inversedBy: 'communes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commune:lire', 'commune:ecrire'])]
    #[Assert\NotNull]
    private ?Departement $departement = null;

    // Une commune peut avoir plusieurs codes postaux
    /**
     * @var Collection<int, CodePostal>
     */
    #[ORM\ManyToMany(targetEntity: CodePostal::class, inversedBy: 'communes')]
    #[Groups(['commune:lire', 'commune:ecrire'])]
    private Collection $codesPostaux;

    public function __construct()
    {
        $this->codesPostaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeInsee(): ?string
    {
        return $this->codeInsee;
    }

    public function setCodeInsee(?string $codeInsee): static
    {
        $this->codeInsee = $codeInsee;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(?float $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): static
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * @return Collection<int, CodePostal>
     */
    public function getCodesPostaux(): Collection
    {
        return $this->codesPostaux;
    }

    // Nom de méthode attendu par Symfony pour la propriété $codesPostaux
    public function addCodesPostaux(CodePostal $codePostal): static
    {
        if (!$this->codesPostaux->contains($codePostal)) {
            $this->codesPostaux->add($codePostal);
        }

        return $this;
    }

    public function removeCodesPostaux(CodePostal $codePostal): static
    {
        $this->codesPostaux->removeElement($codePostal);

        return $this;
    }
}