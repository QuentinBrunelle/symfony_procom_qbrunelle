<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployeRepository")
 * @ORM\Table(name="employes")
 */
class Employe
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     * @Assert\Email(message="L'email {{ value }} n'est pas valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $coutJournalier;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $dateEmbauche;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metier", inversedBy="employes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="metier_id")
     *
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide")
     */
    private $metier;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archivage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TempsProductionEmployeProjet", mappedBy="employe", orphanRemoval=true)
     */
    private $tempsProductionEmploye;

    public function __construct()
    {
        $this->tempsProductionEmploye = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getCoutJournalier(): ?float
    {
        return $this->coutJournalier;
    }

    public function setCoutJournalier(float $coutJournalier): self
    {
        $this->coutJournalier = $coutJournalier;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): self
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    public function getMetier(): ?Metier
    {
        return $this->metier;
    }

    public function setMetier(?Metier $metier): self
    {
        $this->metier = $metier;

        return $this;
    }

    public function getArchivage(): ?bool
    {
        return $this->archivage;
    }

    public function setArchivage(bool $archivage): self
    {
        $this->archivage = $archivage;

        return $this;
    }

    /**
     * @return Collection|TempsProductionEmployeProjet[]
     */
    public function getTempsProductionEmploye(): Collection
    {
        return $this->tempsProductionEmploye;
    }

    public function addTempsProductionEmploye(TempsProductionEmployeProjet $tempsProductionEmploye): self
    {
        if (!$this->tempsProductionEmploye->contains($tempsProductionEmploye)) {
            $this->tempsProductionEmploye[] = $tempsProductionEmploye;
            $tempsProductionEmploye->setEmploye($this);
        }

        return $this;
    }

    public function removeTempsProductionEmploye(TempsProductionEmployeProjet $tempsProductionEmploye): self
    {
        if ($this->tempsProductionEmploye->contains($tempsProductionEmploye)) {
            $this->tempsProductionEmploye->removeElement($tempsProductionEmploye);
            // set the owning side to null (unless already changed)
            if ($tempsProductionEmploye->getEmploye() === $this) {
                $tempsProductionEmploye->setEmploye(null);
            }
        }

        return $this;
    }
}
