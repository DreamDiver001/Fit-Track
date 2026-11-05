<?php
namespace Tests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    /**
     * Run artisan commands (e.g. the migrations RefreshDatabase triggers) directly
     * instead of through the console-output mock, which needs mockery/mockery —
     * a package this project doesn't install.
     */
    public $mockConsoleOutput = false;
}
