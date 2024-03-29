<?php

namespace App\Entity;

use Serializable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[Vich\Uploadable]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('message')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups('message')]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[Vich\UploadableField(mapping: 'utilisateur_photo', fileNameProperty: 'photo')]
    private ?File $photoFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('message')]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'utilisateurs')]
    private Collection $rooms;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Tournoi::class, orphanRemoval: true)]
    private Collection $mesTournois;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: Follow::class)]
    private Collection $follows;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Equipe::class)]
    private Collection $equipes;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: ParticipantTournoi::class, orphanRemoval: true)]
    private Collection $participantTournois;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: PseudoEnJeu::class, orphanRemoval: true)]
    private Collection $pseudosEnJeux;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isBanned = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $banExpireIn = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->mesTournois = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->equipes = new ArrayCollection();
        $this->participantTournois = new ArrayCollection();
        $this->pseudosEnJeux = new ArrayCollection();
    }

    // VichUploader
    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;
        if ($this->photoFile instanceof UploadedFile) {

            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    // VichUploader end

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->addUtilisateur($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            $room->removeUtilisateur($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->pseudo;
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getMesTournois(): Collection
    {
        return $this->mesTournois;
    }

    public function addMesTournoi(Tournoi $mesTournoi): static
    {
        if (!$this->mesTournois->contains($mesTournoi)) {
            $this->mesTournois->add($mesTournoi);
            $mesTournoi->setOrganisateur($this);
        }

        return $this;
    }

    public function removeMesTournoi(Tournoi $mesTournoi): static
    {
        if ($this->mesTournois->removeElement($mesTournoi)) {
            // set the owning side to null (unless already changed)
            if ($mesTournoi->getOrganisateur() === $this) {
                $mesTournoi->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follow $follow): static
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setFollower($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): static
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getFollower() === $this) {
                $follow->setFollower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): static
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
            $equipe->setProprietaire($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): static
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getProprietaire() === $this) {
                $equipe->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ParticipantTournoi>
     */
    public function getParticipantTournois(): Collection
    {
        return $this->participantTournois;
    }

    public function addParticipantTournoi(ParticipantTournoi $participantTournoi): static
    {
        if (!$this->participantTournois->contains($participantTournoi)) {
            $this->participantTournois->add($participantTournoi);
            $participantTournoi->setUtilisateur($this);
        }

        return $this;
    }

    public function removeParticipantTournoi(ParticipantTournoi $participantTournoi): static
    {
        if ($this->participantTournois->removeElement($participantTournoi)) {
            // set the owning side to null (unless already changed)
            if ($participantTournoi->getUtilisateur() === $this) {
                $participantTournoi->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PseudoEnJeu>
     */
    public function getPseudosEnJeux(): Collection
    {
        return $this->pseudosEnJeux;
    }

    public function addPseudoEnJeu(PseudoEnJeu $pseudosEnJeux): static
    {
        if (!$this->pseudosEnJeux->contains($pseudosEnJeux)) {
            $this->pseudosEnJeux->add($pseudosEnJeux);
            $pseudosEnJeux->setUtilisateur($this);
        }

        return $this;
    }

    public function removePseudoEnJeu(PseudoEnJeu $pseudosEnJeux): static
    {
        if ($this->pseudosEnJeux->removeElement($pseudosEnJeux)) {
            // set the owning side to null (unless already changed)
            if ($pseudosEnJeux->getUtilisateur() === $this) {
                $pseudosEnJeux->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function isIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(?bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getBanExpireIn(): ?\DateTimeInterface
    {
        return $this->banExpireIn;
    }

    public function setBanExpireIn(?\DateTimeInterface $banExpireIn): static
    {
        $this->banExpireIn = $banExpireIn;

        return $this;
    }

    public function serialize() {

        return serialize(array(
            $this->id,
            $this->email,
            $this->pseudo,
            $this->password,
            $this->isVerified,
            $this->photo,
            $this->isBanned,
            $this->banExpireIn,
        )); 
    }
        
    public function unserialize($serialized) {
        
        list (
            $this->id,
            $this->email,
            $this->pseudo,
            $this->password,
            $this->isVerified,
            $this->photo,
            $this->isBanned,
            $this->banExpireIn,
        ) = unserialize($serialized);
    }
}
