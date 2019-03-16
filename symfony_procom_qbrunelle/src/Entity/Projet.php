<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 * @ORM\Table(name="projets")
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $intitule;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estLivre;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TempsDeProduction", mappedBy="projet")
     */
    private $tempsDeProductions;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->tempsDeProductions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEstLivre(): ?bool
    {
        return $this->estLivre;
    }

    public function setEstLivre(bool $estLivre): self
    {
        $this->estLivre = $estLivre;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|TempsDeProduction[]
     */
    public function getTempsDeProductions(): Collection
    {
        return $this->tempsDeProductions;
    }

    public function addTempsDeProduction(TempsDeProduction $tempsDeProduction): self
    {
        if (!$this->tempsDeProductions->contains($tempsDeProduction)) {
            $this->tempsDeProductions[] = $tempsDeProduction;
            $tempsDeProduction->addProjet($this);
        }

        return $this;
    }

    public function removeTempsDeProduction(TempsDeProduction $tempsDeProduction): self
    {
        if ($this->tempsDeProductions->contains($tempsDeProduction)) {
            $this->tempsDeProductions->removeElement($tempsDeProduction);
            $tempsDeProduction->removeProjet($this);
        }

        return $this;
    }
}
