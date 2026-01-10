<?php

namespace Tests;

use Illuminate\Support\Facades\Vite;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Vite::fake();
    }
}
