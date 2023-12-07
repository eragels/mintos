<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'account', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'client_currency_unique', columns: ['client_id', 'currency_id'])
])]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $currency = null;

    #[ORM\OneToMany(mappedBy: 'senderAccount', targetEntity: Transaction::class, cascade: ['persist'])]
    private Collection $sentTransactions;

    #[ORM\OneToMany(mappedBy: 'receiverAccount', targetEntity: Transaction::class, cascade: ['persist'])]
    private Collection $receivedTransactions;

    public function __construct() {
        $this->sentTransactions = new ArrayCollection();
        $this->receivedTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSentTransactions(): Collection
    {
        return $this->sentTransactions;
    }

    public function addSentTransaction(Transaction $transaction): self
    {
        if (!$this->sentTransactions->contains($transaction)) {
            $this->sentTransactions[] = $transaction;
            $transaction->setSenderAccount($this);
        }

        return $this;
    }

    public function removeSentTransaction(Transaction $transaction): self
    {
        if ($this->sentTransactions->removeElement($transaction)) {
            // set the sender account to null (unless already changed)
            if ($transaction->getSenderAccount() === $this) {
                $transaction->setSenderAccount(null);
            }
        }

        return $this;
    }

    public function getReceivedTransactions(): Collection
    {
        return $this->receivedTransactions;
    }

    public function addReceivedTransaction(Transaction $transaction): self
    {
        if (!$this->receivedTransactions->contains($transaction)) {
            $this->receivedTransactions[] = $transaction;
            $transaction->setReceiverAccount($this);
        }

        return $this;
    }

    public function removeReceivedTransaction(Transaction $transaction): self
    {
        if ($this->receivedTransactions->removeElement($transaction)) {
            // set the receiver account to null (unless already changed)
            if ($transaction->getReceiverAccount() === $this) {
                $transaction->setReceiverAccount(null);
            }
        }

        return $this;
    }
}
