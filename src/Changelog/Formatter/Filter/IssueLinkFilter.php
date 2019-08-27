<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter\Filter;

use function preg_replace;

final class IssueLinkFilter implements Filter
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $pattern A regular expression pattern for issue references
     * @param string $url     A URL replacement string
     */
    public function __construct(string $pattern, string $url)
    {
        $this->pattern = $pattern;
        $this->url = $url;
    }

    public function filter(string $line): string
    {
        return preg_replace(
            $this->pattern,
            '[$1](' . $this->url . ')',
            $line
        ) ?: $line;
    }
}
