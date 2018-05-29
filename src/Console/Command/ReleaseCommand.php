<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Console\InteractiveInformationCollector;
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
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

    public function __construct(
        VersionControlSystem $versionControlSystem,
        Strategy $versioningStrategy,
        ChangelogGenerator $changeLogGenerator
    ) {
        $this->versionControlSystem = $versionControlSystem;
        $this->versioningStrategy = $versioningStrategy;
        $this->changelogGenerator = $changeLogGenerator;

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
            $style->newLine();

            $style->text('A new release will introduce the following changes:');
            $style->listing($this->changelogGenerator->getChanges());

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
}
