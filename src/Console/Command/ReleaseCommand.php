<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        /** @var StyleInterface $style */
        $style = new SymfonyStyle($input, $output);

        $version = $input->getArgument('version');

        if (!$style->confirm('This will release version ' . $version . '. Do you want to continue?')) {
            return;
        }

        $style->text('Releasing the new version...');

        $this->versionControlSystem->createVersion($version);

        $style->success('Version ' . $version . ' has been released.');
    }
}
