<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function sprintf;

final class CurrentCommand extends Command
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
            ->setName('current')
            ->setDescription('Display the current version number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $currentVersion = $this->versionControlSystem->getLastVersion();

        $output->writeln(sprintf('Current version: <info>%s</info>', $currentVersion));
    }
}
