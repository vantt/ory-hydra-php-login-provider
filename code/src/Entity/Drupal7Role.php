<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drupal7Role
 *
 * @ORM\Table(name="role", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="name_weight", columns={"name", "weight"})})
 * @ORM\Entity
 */
class Drupal7Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="rid", type="integer", nullable=false, options={"comment"="Primary Key: Unique role ID."})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $rid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name = '';

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=false, options={"comment"="The weight of this role in listings and the user interface."})
     */
    private $weight = '0';

    public function getRid(): ?int
    {
        return $this->rid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }


}
