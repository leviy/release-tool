<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use InvalidArgumentException;
use function array_pop;
use function explode;
use function implode;
use function preg_match;
use function sprintf;
use function strpos;

/**
 * See https://semver.org/
 */
final class SemanticVersion implements Version
{
    /**
     * @var int
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $patch;

    /**
     * @var string|null
     */
    private $preRelease;

    public function __construct(int $major, int $minor, int $patch, ?string $preRelease = null)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
    }

    public static function createFromVersionString(string $version): self
    {
        if (!preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-([0-9a-zA-Z.]+))?$/', $version, $matches)) {
            throw new InvalidArgumentException(
                sprintf('Version number "%s" is not a valid semantic version.', $version)
            );
        }

        return new self((int) $matches[1], (int) $matches[2], (int) $matches[3], $matches[4] ?? null);
    }

    public function createAlphaRelease(): self
    {
        return $this->createPreRelease('alpha');
    }

    public function createBetaRelease(): self
    {
        return $this->createPreRelease('beta');
    }

    public function createReleaseCandidate(): self
    {
        return $this->createPreRelease('rc');
    }

    public function incrementPatchVersion(): self
    {
        $clone = clone $this;
        $clone->patch++;

        return $clone;
    }

    public function incrementMinorVersion(): self
    {
        $clone = clone $this;
        $clone->patch = 0;
        $clone->minor++;

        return $clone;
    }

    public function incrementMajorVersion(): self
    {
        $clone = clone $this;
        $clone->patch = $clone->minor = 0;
        $clone->major++;

        return $clone;
    }

    public function getVersion(): string
    {
        if ($this->isPreRelease()) {
            return sprintf('%d.%d.%d-%s', $this->major, $this->minor, $this->patch, $this->preRelease);
        }

        return sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);
    }

    public function getMajorVersion(): int
    {
        return $this->major;
    }

    public function getMinorVersion(): int
    {
        return $this->minor;
    }

    public function getPatchVersion(): int
    {
        return $this->patch;
    }

    public function getPreReleaseIdentifier(): ?string
    {
        return $this->preRelease;
    }

    public function isPreRelease(): bool
    {
        return !empty($this->preRelease);
    }

    private function createPreRelease(string $type): self
    {
        if ($this->preRelease !== null && strpos($this->preRelease, $type) === 0) {
            $identifiers = explode('.', $this->preRelease);
            $number = array_pop($identifiers);
            $identifiers[] = ++$number;

            return $this->withPreReleaseLabel(implode('.', $identifiers));
        }

        return $this->withPreReleaseLabel($type . '.1');
    }

    private function withPreReleaseLabel(string $label): self
    {
        $clone = clone $this;
        $clone->preRelease = $label;

        return $clone;
    }
}
