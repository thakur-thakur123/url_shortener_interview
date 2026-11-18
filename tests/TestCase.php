<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Breeze layouts use @vite(); tests must not require a built frontend.
        $this->withoutVite();
    }
}
