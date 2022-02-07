<?php
declare(strict_types=1);

namespace App\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TestCommandTest extends KernelTestCase
{
    public function testCommand(): void
    {
        $kernel = self::bootKernel();

        $application = new Application($kernel);

        $command = $application->find('test');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        self::assertStringContainsString('Test Command!', $commandTester->getDisplay());
    }
}