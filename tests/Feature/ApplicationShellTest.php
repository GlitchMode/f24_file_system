<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationShellTest extends TestCase
{
    public function test_application_shell_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<div id="app"></div>', false);
    }
}
