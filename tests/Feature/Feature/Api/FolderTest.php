<?php

namespace Tests\Feature\Feature\Api;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FolderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A test for creating a root folder
    */
    public function test_root_creation() : void {
        $response = $this->postJson('/api/folders', [
            'name' => 'Documents',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Documents',
            ]);

        $this->assertDatabaseHas('folders', [
            'name' => 'Documents',
            'parent_id' => null,
        ]);
    }

    /**
     * A test for child folder creation
     */
    public function test_child_folder_creation() : void {
        $parent = Folder::create([
            'name' => 'Documents',
        ]);

        $response = $this->postJson('/api/folders', [
            'name' => 'Work',
            'parent_id' => $parent->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Work',
                'parent_id' => $parent->id,
            ]);
        
        $this->assertDatabaseHas('folders', [
            'name' => 'Work',
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * A test for browsing the root folder
     */
    public function test_browse_root(): void
    {
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        Folder::create([
            'name' => 'Work',
            'parent_id' => $folder->id,
        ]);

        File::create([
            'name' => 'root-file.txt',
            'folder_id' => null,
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);

        $response = $this->getJson('/api/folders');

        $response
            ->assertOk()
            ->assertJsonPath('folder', null)
            ->assertJsonCount(1, 'folders')
            ->assertJsonCount(1, 'files')
            ->assertJsonFragment([
                'name' => 'Documents',
                'parent_id' => null,
            ])
            ->assertJsonFragment([
                'name' => 'root-file.txt',
                'folder_id' => null,
            ]);
    }

    /**
     * A test for browsing a folder
     */
    public function test_browse_folder(): void
    {
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        Folder::create([
            'name' => 'Work',
            'parent_id' => $folder->id,
        ]);

        File::create([
            'name' => 'report.pdf',
            'folder_id' => $folder->id,
        ]);

        $response = $this->getJson("/api/folders/{$folder->id}");

        $response
            ->assertOk()
            ->assertJsonPath('folder.id', $folder->id)
            ->assertJsonCount(1, 'folders')
            ->assertJsonCount(1, 'files')
            ->assertJsonFragment([
                'name' => 'Work',
                'parent_id' => $folder->id,
            ])
            ->assertJsonFragment([
                'name' => 'report.pdf',
                'folder_id' => $folder->id,
            ]);
    }

    /**
     * A validation for name
     */
    public function test_folder_name_is_required(): void {
        $response = $this->postJson('/api/folders', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * A validation for parent
     */
    public function test_parent_exists() : void {
        $response = $this->postJson('/api/folders', [
            'name' => 'Work',
            'parent_id' => 999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['parent_id']);
    }

    /**
     * A test for deletion
     */
    public function test_folder_deletion(): void
    {
        $folder = Folder::create([
            'name' => 'Documents',
        ]);

        $response = $this->deleteJson("/api/folders/{$folder->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('folders', [
            'id' => $folder->id,
        ]);
    }

    /**
     * A test for deletion of child folders and files
     */
    public function test_parent_deletion_removes_child_folders_and_files(): void
    {
        $parent = Folder::create([
            'name' => 'Documents',
        ]);

        $child = Folder::create([
            'name' => 'Work',
            'parent_id' => $parent->id,
        ]);

        $file = File::create([
            'name' => 'report.pdf',
            'folder_id' => $child->id,
        ]);

        $response = $this->deleteJson("/api/folders/{$parent->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('folders', [
            'id' => $parent->id,
        ]);

        $this->assertDatabaseMissing('folders', [
            'id' => $child->id,
        ]);

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
    }
}
