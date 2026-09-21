<?php

namespace Acme\Demo;

use Acme\Base;

/**
 * Greets somebody by name.
 */
#[Deprecated]
final class Greeter extends Base implements Contract
{
    /**
     * Greeting used when none is given.
     */
    public const string GREETING = 'hello';

    public const FIRST = 1, SECOND = 2;

    /**
     * Says hello.
     *
     * @param string $name Name to greet
     * @return string The greeting
     */
    public static function greet(string $name, int $times = 1): string
    {
        $anonymous = new class {
            public function hidden(): void
            {
            }
        };

        return $anonymous::class;
    }

    public function &items(): array
    {
        return $this->items;
    }

    public function __construct(private readonly string $name = 'nobody')
    {
    }
}

interface Contract
{
    public function greet(string $name): string;
}

enum Suit: string
{
    case HEARTS = 'H';
    case SPADES = 'S';
}
