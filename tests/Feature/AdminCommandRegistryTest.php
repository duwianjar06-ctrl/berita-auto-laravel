<?php

namespace Tests\Feature;

use App\Models\AdminCommandRun;
use App\Services\AdminConsole\AdminCommandRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AdminCommandRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_commands_are_parsed_without_regex_warnings(): void
    {
        $this->assertSame('berita:publish', AdminCommandRegistry::parse('berita:publish')[0]);
        $this->assertSame('berita:pipeline-status', AdminCommandRegistry::parse('berita:pipeline-status')[0]);
        $this->assertSame('berita:gemini-health', AdminCommandRegistry::parse('berita:gemini-health')[0]);
        $this->assertSame(20, AdminCommandRegistry::parse('berita:debug-rejected --limit=20')[1]['limit']);
        $this->assertSame(100, AdminCommandRegistry::parse('berita:debug-rejected --limit=100')[1]['limit']);
        $this->assertSame(30, AdminCommandRegistry::parse('berita:debug-rejected --limit=30')[1]['limit']);
    }

    public function test_malicious_and_unknown_commands_are_rejected(): void
    {
        foreach ([
            'berita:publish;whoami',
            'berita:publish && whoami',
            'berita:publish | whoami',
            'berita:publish > file',
            'berita:publish < file',
            'berita:publish $(whoami)',
            'berita:publish `whoami`',
            'berita:publish\\whoami',
            'abc:test',
            'berita:debug-rejected --foo=1',
            'berita:debug-rejected --limit=101',
        ] as $command) {
            try {
                AdminCommandRegistry::parse($command);
                $this->fail('Command should be rejected: '.$command);
            } catch (InvalidArgumentException $exception) {
                $this->assertNotSame('', $exception->getMessage());
            }
        }
    }

    public function test_admin_can_submit_a_valid_custom_command(): void
    {
        config(['berita.admin_emails' => ['admin@example.com']]);

        $response = $this->withSession(['admin_email' => 'admin@example.com'])
            ->post(route('admin.console.run'), [
                'command' => 'berita:pipeline-status',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Command masuk antrean.');
        $this->assertDatabaseHas('admin_command_runs', [
            'command_name' => 'berita:pipeline-status',
            'status' => 'pending',
        ]);
    }

    public function test_malicious_custom_command_is_not_queued(): void
    {
        config(['berita.admin_emails' => ['admin@example.com']]);

        $response = $this->withSession(['admin_email' => 'admin@example.com'])
            ->post(route('admin.console.run'), [
                'command' => 'berita:publish;whoami',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('command');
        $this->assertSame(0, AdminCommandRun::count());
    }
}
