<?php
// This file is NEVER included at runtime, only used by PHPStorm for static analysis

if (!function_exists('test')) {
    /**
     * @param string $description
     * @param callable $closure
     * @return void
     */
    function test(string $description, callable $closure): void {}
}

if (!function_exists('it')) {
    /**
     * @param string $description
     * @param callable $closure
     * @return void
     */
    function it(string $description, callable $closure): void {}
}
