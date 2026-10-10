<?php

namespace Tests\Feature\Feature\Api;

use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A creation test
     */
    public function test_file_creation(): void {
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        $response = $this->postJson('/api/files', [
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'report.pdf',
                'folder_id' => $folder->id,
            ]);

        $this->assertDatabaseHas('files', [
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);
    }

    /**
     * A root file creation
     */
    public function test_root_file_creation(): void {

        $response = $this->postJson('/api/files', [
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'report.pdf',
                'folder_id' => null,
            ]);

        $this->assertDatabaseHas('files', [
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);
    }

    /**
     * A validation for name
     */
    public function test_file_name_is_required(): void {
        $response = $this->postJson('/api/files', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * A validation for parent
     */
    public function test_parent_exists() : void {
        $response = $this->postJson('/api/files', [
            'name' => 'reports.pdf',
            'folder_id' => 999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['folder_id']);
    }
}
