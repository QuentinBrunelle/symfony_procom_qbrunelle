<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TempsDeProductionRepository")
 * @ORM\Table(name="temps_de_production")
 */
class TempsDeProduction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Employe", inversedBy="tempsDeProductions")
     */
    private $employe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Projet", inversedBy="tempsDeProductions")
     */
    private $projet;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="float")
     */
    private $coutTotal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSaisie;

    public function __construct()
    {
        $this->employe = new ArrayCollection();
        $this->projet = new ArrayCollection();
        $this->dateSaisie = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Employe[]
     */
    public function getEmploye(): Collection
    {
        return $this->employe;
    }

    public function addEmploye(Employe $employe): self
    {
        if (!$this->employe->contains($employe)) {
            $this->employe[] = $employe;
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): self
    {
        if ($this->employe->contains($employe)) {
            $this->employe->removeElement($employe);
        }

        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjet(): Collection
    {
        return $this->projet;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projet->contains($projet)) {
            $this->projet[] = $projet;
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projet->contains($projet)) {
            $this->projet->removeElement($projet);
        }

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getCoutTotal(): ?float
    {
        return $this->coutTotal;
    }

    public function setCoutTotal(float $coutTotal): self
    {
        $this->coutTotal = $coutTotal;

        return $this;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): self
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }
}
