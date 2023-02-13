<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetAllCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('app:reset')
            ->setDescription('(for local development use) Reset all databases for the given environment. This includes MySQL, MongoDB and Redis. Also loads fixtures for easy testing. Also useful for a quick installation.')
            ->setAliases(['app:install'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // this is the list of commands that we run for a full install
        // Note: running it all within one PHP process seems to be faster than executing each separately
        $commands = [
            'doctrine:database:drop' => ['--force' => true, '--if-exists' => true],
            'doctrine:database:create' => [],
            'doctrine:migrations:migrate' => [],
            'doctrine:fixtures:load' => ['--append' => true],
        ];

        // execute each command with it's arguments non-interactively (this was done because
        // doctrine migrations and fixtures load both don't listen to --no-interaction argument).
        foreach ($commands as $commandName => $arguments) {
            $output->writeln("Executing $commandName...");
            $command = $this->getApplication()->find($commandName);
            $input = new ArrayInput($arguments);
            $input->setInteractive(false);
            $command->run($input, $output);
        }

        return 0;
    }
}
