<?php

namespace Leviy\ReleaseTool\Host;

use Symfony\Component\Console\Style\StyleInterface;

interface Host
{
    public function setStyle(StyleInterface $style);

    public function createRelease(string $version);
}
