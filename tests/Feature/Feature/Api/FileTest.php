<?php

namespace Tests\Feature\Feature\Api;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;
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
     * A root file creation test
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
     * A deletion test
     */
    public function test_file_deletion(): void
    {
        $file = File::create([
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);

        $response = $this->deleteJson("/api/files/{$file->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
    }

    /**
     * A search test
     */
    public function test_exact_file_search(): void
    {
        File::create([
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);

        File::create([
            'name' => 'report-old.pdf',
            'folder_id' => null,
        ]);

        $response = $this->getJson('/api/files/search?name=report.pdf');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'name' => 'report.pdf',
            ]);
    }

    /**
     * A suggestion test
     */
    public function test_file_suggestions(): void
    {
        File::create(['name' => 'report.pdf']);
        File::create(['name' => 'report-old.pdf']);
        File::create(['name' => 'notes.txt']);

        $response = $this->getJson('/api/files/suggestions?q=rep');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'name' => 'report.pdf',
            ]);
    }
}
