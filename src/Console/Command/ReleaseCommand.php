<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use InvalidArgumentException;
use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Console\InteractiveInformationCollector;
use Leviy\ReleaseTool\ReleaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function sprintf;

final class ReleaseCommand extends Command
{
    /**
     * @var ReleaseManager
     */
    private $releaseManager;

    /**
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

    public function __construct(ReleaseManager $releaseManager, ChangelogGenerator $changelogGenerator)
    {
        $this->releaseManager = $releaseManager;
        $this->changelogGenerator = $changelogGenerator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Releases a new version')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number for the new release')
            ->addOption(
                'pre-release',
                'p',
                InputOption::VALUE_NONE,
                'Generate a pre-release (alpha/beta/rc) version. Ignored when <comment>version</comment> is provided'
            )
            ->addUsage('')
            ->addUsage('--pre-release')
            ->addUsage('1.0.0')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command releases a new version of a project. Based on interactive questions, it can
determine the next version number for you:

  <info>%command.full_name%</info>

You can release a pre-release version by using the <comment>--pre-release</comment> option:

  <info>%command.full_name% --pre-release</info>

If you wish, you can skip the interactive questions and choose the version number yourself:

  <info>%command.full_name% 1.0.0</info>
EOF
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getArgument('version') === null) {
            $style = new SymfonyStyle($input, $output);

            $currentVersion = $this->releaseManager->getCurrentVersion();

            $style->text('The previous version on this branch is <info>' . $currentVersion . '</info>.');
            $style->newLine();

            $style->text('A new release will introduce the following changes:');
            $style->listing($this->changelogGenerator->getChanges());

            $informationCollector = new InteractiveInformationCollector($style);

            if ($input->getOption('pre-release')) {
                $version = $this->releaseManager->determineNextPreReleaseVersion($informationCollector);

                $input->setArgument('version', $version);

                return;
            }

            $version = $this->releaseManager->determineNextVersion($informationCollector);

            $input->setArgument('version', $version);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var StyleInterface $style */
        $style = new SymfonyStyle($input, $output);

        $version = $input->getArgument('version');

        if (!$this->releaseManager->isValidVersion($version)) {
            throw new InvalidArgumentException(sprintf('Version "%s" is not a valid version number', $version));
        }

        $style->text(sprintf('This will release version <info>%s</info>.', $version));

        if (!$style->confirm('Do you want to continue?')) {
            return;
        }

        $informationCollector = new InteractiveInformationCollector($style);

        $this->releaseManager->release($version, $informationCollector);

        $style->success('Version ' . $version . ' has been released.');
    }
}
