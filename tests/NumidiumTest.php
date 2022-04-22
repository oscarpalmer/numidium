<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test;

use oscarpalmer\Numidium\Numidium;
use PHPUnit\Framework\TestCase;

class NumidiumTest extends TestCase
{
    public function testConstructor(): void
    {
        $numidium = new Numidium;

        $this->assertInstanceOf('oscarpalmer\Numidium\Numidium', $numidium);
    }

    public function testVersion(): void
    {
        $this->assertIsString(Numidium::VERSION);
    }
}
