<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['region:lire']],     
    denormalizationContext: ['groups' => ['region:ecrire']], 
)]
#[UniqueEntity('code')] 
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['region:lire'])]
    private ?int $id = null;


    #[ORM\Column(length: 3, unique: true)]
    #[Groups(['region:lire', 'region:ecrire', 'departement:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['region:lire', 'region:ecrire', 'departement:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;


    /**
     * @var Collection<int, Departement>
     */
    #[ORM\OneToMany(targetEntity: Departement::class, mappedBy: 'region')]
    #[Groups(['region:lire'])]
    private Collection $departements;

    public function __construct()
    {
        $this->departements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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

    /**
     * @return Collection<int, Departement>
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }
}