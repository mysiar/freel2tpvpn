<?php
declare(strict_types=1);

namespace App\Tests;

use Mysiar\TestBundle\Unit\UnitTestCase;

class EnvArrayTest extends UnitTestCase
{
    public function test(): void
    {
        $emails = explode(',', $_ENV['EMAILS']);

        $this->assertGreaterThan(0, count($emails));
    }
}
