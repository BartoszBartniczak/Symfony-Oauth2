<?php


namespace App\Infrastructure\Symfony\Command;


use App\Application\Command\Command as ApplicationCommand;
use App\Application\Exception\CommandHandlerFailed;
use App\Infrastructure\Symfony\Service\CommandBus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadTestData extends Command
{

    protected static $defaultName = 'dev:load-test-data';

    public function __construct(private CommandBus $commandBus, private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Loads test data into database using CommandBus');
        $this->setHelp('This command loads data into database using CommandBus.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting loading data into database...</info>');
        $this->entityManager->beginTransaction();

        $commandsToExecute = include __DIR__ . '/commands.php';

        $progressBar = new ProgressBar($output, count($commandsToExecute));

        try {
            foreach ($commandsToExecute as $command) {
                assert($command instanceof ApplicationCommand);
                $this->commandBus->execute($command);
                $progressBar->advance();
            }
        } catch (CommandHandlerFailed $commandHandlerFailed) {
            $output->writeln("<error>Something went wrong.</error>");
            $output->writeln("<error>All data is rolled back.</error>");
            $this->entityManager->rollback();
            throw $commandHandlerFailed;
        }
        $this->entityManager->commit();
        $progressBar->finish();
        $output->writeln("");
        $output->writeln('<info>Data has been successfully loaded into database.</info>');

        return Command::SUCCESS;
    }

}
