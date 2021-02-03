<?php

namespace App\Domain\Entity;

use Symfony\Component\Security\Core\User\UserInterface;


class User implements UserInterface
{


    public function __construct(private string $id,
                                private string $email,
                                private string $password,
                                private array $roles)
    {
        $this->changeRoles($roles);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function changeEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }


    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function changeRoles(array $roles): void
    {
        $this->roles = array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function changePassword(string $password):void
    {
        $this->password = $password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

}
