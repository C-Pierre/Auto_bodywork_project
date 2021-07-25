<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $open = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailFrom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailTo;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $send = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }
    

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getOpen(): ?bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getMailFrom(): ?string
    {
        return $this->mailFrom;
    }

    public function setMailFrom(?string $mailFrom): self
    {
        $this->mailFrom = $mailFrom;

        return $this;
    }

    public function getMailTo(): ?string
    {
        return $this->mailTo;
    }

    public function setMailTo(?string $mailTo): self
    {
        $this->mailTo = $mailTo;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getSend(): ?bool
    {
        return $this->send;
    }

    public function setSend(bool $send): self
    {
        $this->send = $send;

        return $this;
    }
}
