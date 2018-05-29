<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Console\InteractiveInformationCollector;
use Leviy\ReleaseTool\Host\Host;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Strategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function sprintf;

final class ReleaseCommand extends Command
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var Strategy
     */
    private $versioningStrategy;

    /**
     * @var Host
     */
    private $hostStrategy;

    public function __construct(VersionControlSystem $versionControlSystem, Strategy $versioningStrategy, Host $hostStrategy)
    {
        $this->versionControlSystem = $versionControlSystem;
        $this->versioningStrategy = $versioningStrategy;
        $this->hostStrategy = $hostStrategy;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Release a new version')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number for the new release');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getArgument('version') === null) {
            $style = new SymfonyStyle($input, $output);
            $current = $this->versionControlSystem->getLastVersion();

            $informationCollector = new InteractiveInformationCollector($style);

            $style->text('The previous version on this branch is <info>' . $current . '</info>.');

            $input->setArgument('version', $this->versioningStrategy->getNextVersion($current, $informationCollector));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var StyleInterface $style */
        $style = new SymfonyStyle($input, $output);

        $version = $input->getArgument('version');

        $style->text(sprintf('This will release version <info>%s</info>.', $version));

        if (!$style->confirm('Do you want to continue?')) {
            return;
        }

        $this->createVersion($style, $version);

        $this->pushVersion($style, $version);

        $this->createRelease($style, $version);

        $style->success('Version ' . $version . ' has been released.');
    }

    private function createVersion(StyleInterface $style, string $version): void
    {
        $style->text('Tagging current branch with new version...');

        $this->versionControlSystem->createVersion($version);
    }

    private function pushVersion(StyleInterface $style, string $version): void
    {
        if (!$style->confirm('Do you want to push version ' . $version . ' to remote?')) {
            return;
        }

        $style->text('Pushing the new version to VCS...');

        $this->versionControlSystem->pushVersion($version);
    }

    private function createRelease(StyleInterface $style, string $version): void
    {
        if (!$style->confirm('Do you want to create a release for version: ' . $version . '?')) {
            return;
        }

        $this->hostStrategy->setStyle($style);
        $result = $this->hostStrategy->createRelease($version);

        if ($result === false) {
            $style->error('The release for version ' . $version . ' could not be created.');
        }
    }
}
