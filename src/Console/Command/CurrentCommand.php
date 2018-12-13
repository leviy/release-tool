<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
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
            ->setDescription('Displays the current version number')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command displays the version number of the current release.

<info>%command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $currentVersion = $this->versionControlSystem->getLastVersion();
        } catch (ReleaseNotFoundException $exception) {
            $output->writeln('<error>No existing version found</error>');

            return 1;
        }

        $output->writeln(sprintf('Current version: <info>%s</info>', $currentVersion));

        return 0;
    }
}
