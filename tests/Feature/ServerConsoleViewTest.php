<?php

namespace Tests\Feature;

use App\Services\AdminConsole\AdminCommandRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ServerConsoleViewTest extends TestCase
{
    public function test_admin_server_console_view_renders_with_empty_history(): void
    {
        $html = view('admin.server-console', [
            'runs' => new Collection(),
            'registry' => AdminCommandRegistry::all(),
        ])->withErrors(new ViewErrorBag())->render();

        $this->assertStringContainsString('REMOTE SERVER CONSOLE', $html);
        $this->assertStringContainsString('CUSTOM COMMAND', $html);
    }
}
