<?php

namespace App\Tests\Infrastructure\Symfony\Command;

use App\Application\Exception\CommandHandlerFailed;
use App\Application\Service\CommandBus;
use App\Infrastructure\Symfony\Command\LoadTestData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @coversDefaultClass \App\Infrastructure\Symfony\Command\LoadTestData
 */
class LoadTestDataTest extends TestCase
{

    private LoadTestData $command;
    private EntityManagerInterface|MockObject $entityManager;
    private CommandBus|MockObject $commandBus;
    private MockObject|InputInterface $consoleInput;
    private MockObject|OutputInterface $consoleOutput;

    protected function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBus::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new LoadTestData($this->commandBus, $this->entityManager);

        $this->consoleInput = $this->createMock(InputInterface::class);

        $formatter = $this->createMock(OutputFormatterInterface::class);
        $formatter->method('isDecorated')->willReturn(false);
        $this->consoleOutput = $this->createMock(OutputInterface::class);
        $this->consoleOutput->method('getFormatter')->willReturn($formatter);
    }

    /**
     * @covers ::configure
     */
    public function testConfigure()
    {
        $this->assertSame('Loads test data into database using CommandBus', $this->command->getDescription());
        $this->assertSame('This command loads data into database using CommandBus.', $this->command->getHelp());
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testExecute()
    {

        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->commandBus->expects($this->atLeastOnce())
            ->method('execute');

        $this->entityManager->expects($this->once())
            ->method('commit');

        $this->command->run($this->consoleInput, $this->consoleOutput);

    }

    /**
     * @covers ::execute
     */
    public function testExecuteRollbackChangesIfCommandBusWillThrowException()
    {
        $this->expectException(CommandHandlerFailed::class);
        
        $this->entityManager->expects($this->once())
            ->method('beginTransaction');

        $this->commandBus->expects($this->atLeastOnce())
            ->method('execute')
            ->willThrowException($this->createMock(CommandHandlerFailed::class));

        $this->entityManager->expects($this->once())
            ->method('rollback');

        $this->command->run($this->consoleInput, $this->consoleOutput);
    }

}
