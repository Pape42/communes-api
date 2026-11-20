<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CodePostalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CodePostalRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['codepostal:lire']],
    denormalizationContext: ['groups' => ['codepostal:ecrire']],
)]
#[UniqueEntity('code')] 
class CodePostal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['codepostal:lire'])]
    private ?int $id = null;


    #[ORM\Column(length: 5, unique: true)]
    #[Groups(['codepostal:lire', 'codepostal:ecrire', 'commune:lire'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    private ?string $code = null;


    /**
     * @var Collection<int, Commune>
     */
    #[ORM\ManyToMany(targetEntity: Commune::class, mappedBy: 'codesPostaux')]
    #[Groups(['codepostal:lire'])]
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

    /**
     * @return Collection<int, Commune>
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }
}