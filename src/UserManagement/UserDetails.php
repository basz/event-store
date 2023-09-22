<?php

/**
 * This file is part of prooph/event-store.
 * (c) 2014-2022 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2015-2022 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\UserManagement;

use DateTimeImmutable;
use Prooph\EventStore\Exception\RuntimeException;
use Prooph\EventStore\Internal\DateTimeStringBugWorkaround;
use Prooph\EventStore\Util\DateTime;

/** @psalm-immutable */
final class UserDetails
{
    private string $loginName;

    private string $fullName;

    /** @var list<string> */
    private array $groups = [];

    private ?DateTimeImmutable $dateLastUpdated = null;

    private bool $disabled;

    /** @var list<RelLink> */
    private array $links = [];

    /**
     * @param list<string> $groups
     * @param list<RelLink> $links
     *
     * @psalm-mutation-free
     */
    private function __construct(
        string $loginName,
        string $fullName,
        array $groups,
        ?DateTimeImmutable $dateLastUpdated,
        bool $disabled,
        array $links
    ) {
        $this->loginName = $loginName;
        $this->fullName = $fullName;
        $this->groups = $groups;
        $this->dateLastUpdated = $dateLastUpdated;
        $this->disabled = $disabled;
        $this->links = $links;
    }

    /** @internal */
    public static function fromArray(array $data): self
    {
        $dateLastUpdated = isset($data['dateLastUpdated'])
            ? DateTime::create(DateTimeStringBugWorkaround::fixDateTimeString((string) $data['dateLastUpdated']))
            : null;

        $links = [];

        if (isset($data['links'])) {
            /** @var list<array<string, string>> $data['links'] */
            foreach ($data['links'] as $link) {
                $links[] = new RelLink($link['href'], $link['rel']);
            }
        }

        /** @var list<string> $data['groups'] */

        return new self(
            (string) $data['loginName'],
            (string) $data['fullName'],
            $data['groups'],
            $dateLastUpdated,
            (bool) $data['disabled'],
            $links
        );
    }

    /** @psalm-mutation-free */
    public function loginName(): string
    {
        return $this->loginName;
    }

    /** @psalm-mutation-free */
    public function fullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return list<string>
     * @psalm-mutation-free
     */
    public function groups(): array
    {
        return $this->groups;
    }

    /** @psalm-mutation-free */
    public function dateLastUpdated(): ?DateTimeImmutable
    {
        return $this->dateLastUpdated;
    }

    /** @psalm-mutation-free */
    public function disabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return list<RelLink>
     * @psalm-mutation-free
     */
    public function links(): array
    {
        return $this->links;
    }

    /**
     * @throws RuntimeException if rel not found
     * @psalm-mutation-free
     */
    public function getRelLink(string $rel): string
    {
        $rel = \strtolower($rel);

        foreach ($this->links() as $link) {
            if (\strtolower($link->rel()) === $rel) {
                return $link->href();
            }
        }

        throw new RuntimeException('Rel not found');
    }
}
