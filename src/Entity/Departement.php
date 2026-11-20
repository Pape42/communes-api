<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['departement:lire']],
    denormalizationContext: ['groups' => ['departement:ecrire']],
)]
#[UniqueEntity('code')]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['departement:lire'])]
    private ?int $id = null;

    #[ORM\Column(length: 3, unique: true)]
    #[Groups(['departement:lire', 'departement:ecrire', 'commune:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 3)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['departement:lire', 'departement:ecrire', 'commune:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'departements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['departement:lire', 'departement:ecrire'])]
    #[Assert\NotNull]
    private ?Region $region = null;

    /**
     * @var Collection<int, Commune>
     */
    #[ORM\OneToMany(targetEntity: Commune::class, mappedBy: 'departement')]
    #[Groups(['departement:lire'])]
    private Collection $communes;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, Commune>
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }
}