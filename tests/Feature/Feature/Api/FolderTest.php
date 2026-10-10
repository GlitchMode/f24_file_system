<?php

namespace Tests\Feature\Feature\Api;

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
}
