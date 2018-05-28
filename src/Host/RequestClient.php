<?php

namespace Leviy\ReleaseTool\Host;

interface RequestClient
{
    /**
     * @param string   $version
     * @param string[] $authenticationInformation
     * @param string[] $repositoryInformation
     */
    public function createRelease(string $version, array $authenticationInformation, array $repositoryInformation): void;
}
