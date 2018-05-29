<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Configuration;

use RuntimeException;
use function file_get_contents;

class GlobalConfiguration
{
    private const GLOBAL_CONFIGURATION_DIRECTORY = '/.release-tool/';
    private const GLOBAL_CONFIGURATION_FILENAME = 'config.json';

    /** @var mixed[] */
    public $configuration;

    public function __construct()
    {
        $file = $this->getGlobalFilePath();

        if (file_exists($file)) {
            $this->configuration = json_decode(
                file_get_contents(
                    $file
                ),
                true
            );
        }
    }

    /**
     * @param string $key
     * @param string $parent
     *
     * @return mixed
     */
    public function findByKey(string $key, string $parent = null)
    {
        $scope = $this->configuration;

        if (!empty($parent)) {
            $scope = $this->configuration[$parent];
        }

        if (empty($scope[$key])) {
            return null;
        }

        return $scope[$key];
    }

    public function createGlobal(): void
    {
        if (!file_exists($this->getGlobalDirectoryPath())) {
            $this->createGlobalDirectory();
        }

        if (!file_exists($this->getGlobalFilePath())) {
            $this->createGlobalFile();
        }
    }

    private function getGlobalDirectoryPath(): string
    {
        $homeDirectory = rtrim(getenv('HOME') ?: getenv('USERPROFILE'), '/\\');

        return $homeDirectory . self::GLOBAL_CONFIGURATION_DIRECTORY;
    }

    private function getGlobalFilePath(): string
    {
        return $this->getGlobalDirectoryPath() . self::GLOBAL_CONFIGURATION_FILENAME;
    }

    private function createGlobalDirectory(): void
    {
        $directory = $this->getGlobalDirectoryPath();

        if (!mkdir($directory, 0777, true)) {
            throw new RuntimeException($directory . ' does not exist and could not be created.');
        }
    }

    private function createGlobalFile(): void
    {
        $filename = $this->getGlobalFilePath();

        if (!touch($filename)) {
            throw new RuntimeException($filename . ' does not exist and could not be created.');
        }
    }
}
