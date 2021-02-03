<?php

namespace App\Tests\Infrastructure\Symfony\Repository;

use App\Domain\Entity\User;
use App\Domain\Exception\PersistenceException;
use App\Domain\Exception\User\UserAlreadyExists;
use App\Domain\Exception\User\UserDoesNotExist;
use App\Domain\Query\UserQuery;
use App\Domain\Repository\UserWriteRepository;
use App\Infrastructure\Symfony\Repository\UserRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Repository\UserRepository
 */
class UserRepositoryTest extends RepositoryTestCase
{

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(UserWriteRepository::class, $this->userRepository);
        $this->assertInstanceOf(UserQuery::class, $this->userRepository);
        $this->assertInstanceOf(PasswordUpgraderInterface::class, $this->userRepository);
    }

    /**
     * @covers ::saveNew
     * @covers ::userExists
     */
    public function testSaveNew()
    {
        $userId = 'e964139b-d9cc-413a-a8f1-17bee785e431';
        $user = new User($userId, 'repo@user.com', 'secret', ['ROLE_USER']);

        $this->userRepository->saveNew($user);

        $row = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('user')
            ->where('user.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);

        $this->assertNotEmpty($row);
        $this->assertSame($userId, $row['id']);
    }

    /**
     * @covers ::saveNew
     * @covers ::userExists
     */
    public function testCannotSaveSameEmail(){
        $this->expectException(UserAlreadyExists::class);

        $user = new User('81c27522-9c9e-4429-8133-da25c5517005', 'test@user.com', 'secret', ['ROLE_USER']);
        $this->userRepository->saveNew($user);
    }

    /**
     * @covers ::saveNew
     */
    public function testSaveNewThrowsExceptionIfCannotPersistObject(){

        $this->expectException(PersistenceException::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('persist')->willThrowException(new ORMException());
        $entityManager->method('getClassMetadata')
            ->willReturn($this->createMock(ClassMetadata::class));

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager);

        $newUser = $this->createMock(User::class);
        $repository = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->onlyMethods(['userExists'])
            ->getMock();

        $repository->method('userExists')->willReturn(false);
        $repository->saveNew($newUser);
    }

    /**
     * @covers ::upgradePassword
     */
    public function testUpgradePassword()
    {
        $user = $this->userRepository->findByEmail('test@user.com');
        $this->userRepository->upgradePassword($user, 'newEncodedPassword');
        $user = $this->userRepository->findByEmail('test@user.com');
        $this->assertSame('newEncodedPassword', $user->getPassword());
    }

    /**
     * @covers ::upgradePassword
     */
    public function testUpgradePasswordThrowsExceptionIfUserIsInvalidType(){
        $this->expectException(UnsupportedUserException::class);
        
        $invalidUser = $this->createMock(UserInterface::class);
        $this->userRepository->upgradePassword($invalidUser, 'test');
    }


    /**
     * @covers ::findByEmail
     */
    public function testFindByEmail()
    {
        $user = $this->userRepository->findByEmail('test@user.com');
        $this->assertSame('test@user.com', $user->getEmail());
    }

    /**
     * @covers ::findByEmail
     */
    public function testFindByEmailThrowsExceptionIfUserDoesNotExist(){
        $this->expectException(UserDoesNotExist::class);

        $this->userRepository->findByEmail('wrong');
    }

}
