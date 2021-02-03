<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\Entity\User;
use App\Domain\Exception\PersistenceException;
use App\Domain\Exception\User\UserAlreadyExists;
use App\Domain\Exception\User\UserDoesNotExist;
use App\Domain\Query\UserQuery;
use App\Domain\Repository\UserWriteRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserWriteRepository, UserQuery
{
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->changePassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByEmail(string $email): User
    {
        $user = $this->_em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            throw new UserDoesNotExist();
        }

        return $user;
    }

    public function saveNew(User $newUser): void
    {
        if($this->userExists($newUser)){
            throw new UserAlreadyExists();
        }

        try {
            $this->_em->persist($newUser);
            $this->_em->flush();
        } catch (ORMException $ORMException) {
            throw new PersistenceException('Cannot persist User', $ORMException);
        }
    }

    protected function userExists(User $newUser):bool
    {
        try{
            $this->findByEmail($newUser->getEmail());
            return true;
        }catch (UserDoesNotExist){
            return false;
        }
    }
}
