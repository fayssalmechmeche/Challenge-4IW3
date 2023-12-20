<?php

namespace App\Entity;

use App\Repository\SocietyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SocietyRepository::class)]
#[UniqueEntity('email')]
class Society
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 12, max: 50)]
    #[Assert\Regex(
        pattern: '/^(?:(?:\+|00)33|0)\d{9}$/',
        message: "Votre numéro de téléphone n'est pas valide",
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    // #[Assert\Email(
    //     message: "Cet email {{ value }} n'est pas valide.",
    // )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^(?:\d{3}\s){3}\d{5}$|^\d{14}$/',
        message: "Votre numéro de siret n'est pas valide, vérifiez qu'il contient 14 chiffres",
    )]
    private ?string $siret = null;

    #[ORM\OneToMany(mappedBy: 'society', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'society', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setSociety($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSociety() === $this) {
                $user->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setSociety($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSociety() === $this) {
                $product->setSociety(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setSociety($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getSociety() === $this) {
                $devi->setSociety(null);
            }
        }

        return $this;
    }
}
