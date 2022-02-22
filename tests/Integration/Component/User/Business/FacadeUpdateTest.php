<?php
declare(strict_types=1);

namespace App\Tests\Integration\Component\User\Business;

use App\Component\User\Business\FacadeUserInterface;
use App\Component\User\Persistence\EntityManager\UserEntityManagerInterface;
use App\DataFixtures\UserFixtures;
use App\DataProvider\UserDataProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FacadeUpdateTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;
    protected FacadeUserInterface $facade;
    protected UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $userFixtures = $container->get(UserFixtures::class);
        $userFixtures->load($this->entityManager);

        $this->facade = $container->get(FacadeUserInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeQuery('DELETE FROM user');
        $connection->executeQuery('ALTER TABLE user AUTO_INCREMENT=0');
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $connection->close();
    }

    public function testUpdatePositiv(): void
    {
        $user = $this->userRepository->find(1);
        self::assertSame('user@cec.valantic.com', $user->getEmail());

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $errors = $this->facade->update($userDataProvider);
        self::assertEmpty($errors);

        $user = $this->userRepository->find(1);

        self::assertSame('test@cec.valantic.com', $user->getEmail());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('test@cec.valantic.com', $user->getToken());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'Test_123!'));

        $this->facade->setToken('test@cec.valantic.com', 'token');

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test_2@cec.valantic.com')
            ->setPassword('Test_234!')
            ->setVerificationPassword('Test_234!');

        $errors = $this->facade->update($userDataProvider);
        self::assertEmpty($errors);

        $user = $this->userRepository->find(1);

        self::assertSame('test_2@cec.valantic.com', $user->getEmail());
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame('token', $user->getToken());
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, 'Test_234!'));
    }

    public function testCreateNegativEmail(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No data provided", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail('test234@emai.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No data provided", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No data provided", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('admin@cec.valantic.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Email is already taken", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('user@email.com')
            ->setPassword('Test_123!')
            ->setVerificationPassword('Test_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Email is not valid", $error[0]);
    }

    public function testUpdateNegativPassword(): void
    {
        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No Password provided!", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com')
            ->setPassword('test');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("No Verification Password provided!", $error[0]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com')
            ->setPassword('test')
            ->setVerificationPassword('t');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(5, $error);
        self::assertSame("Password too short!", $error[0]);
        self::assertSame("Password must include at least one number!", $error[1]);
        self::assertSame("Password must include at least one uppercase letter!", $error[2]);
        self::assertSame("Password must include at one special character!", $error[3]);
        self::assertSame("Password musst match Verification Password!", $error[4]);

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setId(1)
            ->setEmail('test@cec.valantic.com')
            ->setPassword('TEST_123!')
            ->setVerificationPassword('TEST_123!');

        $error = $this->facade->update($userDataProvider);

        self::assertCount(1, $error);
        self::assertSame("Password must include at least one lowercase letter!", $error[0]);
    }
}