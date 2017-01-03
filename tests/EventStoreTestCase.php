<?php
/**
 * This file is part of the prooph/event-store.
 * (c) 2014-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore;

use ArrayIterator;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use ProophTest\EventStore\Mock\UserCreated;

abstract class EventStoreTestCase extends BaseTestCase
{
    /**
     * @var InMemoryEventStore
     */
    protected $eventStore;

    protected function setUp(): void
    {
        $this->eventStore = new InMemoryEventStore();
    }

    protected function getTestStream(): Stream
    {
        $streamEvent = UserCreated::with(
            ['name' => 'Alex', 'email' => 'contact@prooph.de'],
            1
        );

        return new Stream(new StreamName('user'), new ArrayIterator([$streamEvent]), ['foo' => 'bar']);
    }
}
