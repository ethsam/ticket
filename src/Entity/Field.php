<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FieldRepository;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Form $form = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null; // text, email, date, select...

    #[ORM\Column]
    private bool $required = false;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $options = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): static
    {
        $this->options = $options;

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
        if ($this->label) {
            $requiredMark = $this->required ? ' *' : '';
            return $this->label . $requiredMark . ' - Type : ' . $this->type;
        }

        return 'Champ #' . $this->id;
    }

    #[ORM\PrePersist]
    public function generateNameOnCreate(): void
    {
        if (!$this->name && $this->label) {
            $this->name = strtolower((new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($this->label));
        }
    }

    #[ORM\PreUpdate]
    public function generateNameOnUpdate(): void
    {
        if (!$this->name && $this->label) {
            $this->name = strtolower((new \Symfony\Component\String\Slugger\AsciiSlugger())->slug($this->label));
        }
    }

    #[ORM\PrePersist]
    public function autoPosition(): void
    {
        if ($this->position === 0 && $this->form instanceof Form) {
            $this->position = count($this->form->getFields()) + 1;
        }
    }

    public function getOptionsArray(): array
    {
        if (!$this->options) {
            return [];
        }

        return json_decode($this->options, true) ?? [];
    }

}
