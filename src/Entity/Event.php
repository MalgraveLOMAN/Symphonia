<?php

namespace App\Entity;

use App\Repository\EventRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read', 'artist:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['event:read', 'artist:read'])]
    private string $name;

    #[ORM\Column(type: 'date')]
    #[Groups(['event:read'])]
    private DateTimeInterface $date;

    #[ORM\Column(type: 'text')]
    #[Groups(['event:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read'])]
    private ?string $location = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['event:read'])]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'organizedEvents')]
    #[Groups(['event:read'])]
    private ?User $organizer = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'events')]
    #[Groups(['event:read'])]
    private Collection $artists;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'participatedEvents')]
    #[Groups(['event:read'])]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->artists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
            $artist->addEvent($this);
        }
        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->removeElement($artist)) {
            $artist->removeEvent($this);
        }
        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $user): self
    {
        if (!$this->participants->contains($user)) {
            $this->participants->add($user);
            $user->addParticipatedEvent($this);
        }
        return $this;
    }

    public function removeParticipant(User $user): self
    {
        if ($this->participants->removeElement($user)) {
            $user->removeParticipatedEvent($this);
        }
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;
        return $this;
    }
}
