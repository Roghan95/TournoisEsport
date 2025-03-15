<?php

namespace App\Entity;

use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[ORM\UniqueConstraint(name: 'follow_unique', columns: ['follower_id', 'following_id'])]
class Follow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $follower = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $following = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollower(): ?Utilisateur
    {
        return $this->follower;
    }

    public function setFollower(?Utilisateur $follower): static
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowing(): ?Utilisateur
    {
        return $this->following;
    }

    public function setFollowing(?Utilisateur $following): static
    {
        $this->following = $following;

        return $this;
    }
}
