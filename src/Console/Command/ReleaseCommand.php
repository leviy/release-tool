<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ReleaseCommand extends Command
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    public function __construct(VersionControlSystem $versionControlSystem)
    {
        $this->versionControlSystem = $versionControlSystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Release a new version')
            ->addArgument('version', InputArgument::REQUIRED, 'The version number for the new release');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Releasing the new version...</info>');

        $this->versionControlSystem->createVersion($input->getArgument('version'));

        $output->writeln('<info>Done.</info>');
    }
}
