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
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);

        File::create([
            'name' => 'report-old.pdf',
            'folder_id' => null,
        ]);

        $response = $this->getJson('/api/files/search?name=report.pdf');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'name' => 'report.pdf',
            ]);
    }

    /**
     * A scoped search test
     */
    public function test_exact_scoped_file_search(): void
    {
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        $otherFolder = Folder::create([
            'name' => 'Archive',
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => $otherFolder->id,
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => null,
        ]);

        $response = $this->getJson("/api/files/search?name=report.pdf&folder_id={$folder->id}");

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'name' => 'report.pdf',
                'folder_id' => $folder->id,
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
            ])
            ->assertJsonMissing([
                'name' => 'notes.txt',
            ]);
    }

    /**
     * A suggestion prefix test
     */
    public function test_file_suggestions_only_match_prefix(): void
    {
        File::create(['name' => 'report.pdf']);
        File::create(['name' => 'report-old.pdf']);
        File::create(['name' => 'annual-report.pdf']);

        $response = $this->getJson('/api/files/suggestions?q=report');

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'name' => 'report.pdf',
            ])
            ->assertJsonFragment([
                'name' => 'report-old.pdf',
            ])
            ->assertJsonMissing([
                'name' => 'annual-report.pdf',
            ]);
    }

    /**
     * A suggestion limit test
     */
    public function test_file_suggestions_have_a_maximum_of_ten_results(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            File::create([
                'name' => "report-{$i}.pdf",
            ]);
        }

        $response = $this->getJson('/api/files/suggestions?q=report');

        $response
            ->assertOk()
            ->assertJsonCount(10);
    }
}
