<?php

it('can execute make:service command', function () {
    $this->artisan('make:pivot Table1 Table2')
        ->assertExitCode(0);
});
