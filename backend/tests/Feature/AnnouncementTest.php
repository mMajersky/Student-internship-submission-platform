<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->garantUser = User::create([
            'name' => 'Garant User',
            'email' => 'garant@test.com',
            'password' => Hash::make('password123'),
            'role' => 'garant',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_get_single_announcement()
    {
        // Create a published announcement
        $announcement = Announcement::create([
            'content' => '<h1>Test Announcement</h1><p>This is a <strong>test</strong> announcement.</p>',
            'is_published' => true,
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson('/api/announcement');

        $response->assertStatus(200)
            ->assertJson([
                'content' => $announcement->content,
                'is_published' => true,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function garant_can_get_single_announcement()
    {
        // Create a published announcement
        $announcement = Announcement::create([
            'content' => '<p>Test announcement content</p>',
            'is_published' => true,
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->garantUser, 'api')
            ->getJson('/api/announcement');

        $response->assertStatus(200)
            ->assertJson([
                'content' => $announcement->content,
                'is_published' => true,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_single_announcement()
    {
        // Create initial announcement
        $announcement = Announcement::create([
            'content' => '<p>Original content</p>',
            'is_published' => true,
            'created_by' => $this->adminUser->id,
            'updated_by' => $this->adminUser->id,
        ]);

        $newContent = '<h1>Updated Announcement</h1><p>This is an <strong>updated</strong> announcement with <em>HTML</em>.</p>';

        $response = $this->actingAs($this->adminUser, 'api')
            ->putJson('/api/announcement', [
                'content' => $newContent,
                'is_published' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'content' => $newContent,
                'is_published' => true,
            ]);

        // Verify the announcement was updated in database
        $announcement->refresh();
        $this->assertEquals($newContent, $announcement->content);
        $this->assertNotEmpty($announcement->content_sanitized);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function garant_can_update_single_announcement()
    {
        $newContent = '<h1>Garant Announcement</h1><p>This announcement was created by a <strong>Garant</strong> user.</p>';

        $response = $this->actingAs($this->garantUser, 'api')
            ->putJson('/api/announcement', [
                'content' => $newContent,
                'is_published' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'content' => $newContent,
                'is_published' => true,
            ]);

        // Verify announcement was created in database
        $announcement = Announcement::where('content', $newContent)->first();
        $this->assertNotNull($announcement);
        $this->assertTrue($announcement->is_published);
        $this->assertEquals($this->garantUser->id, $announcement->updated_by);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_cannot_access_single_announcement_endpoint()
    {
        $studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($studentUser, 'api')
            ->getJson('/api/announcement');

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function company_cannot_access_single_announcement_endpoint()
    {
        $companyUser = User::create([
            'name' => 'Company User',
            'email' => 'company@test.com',
            'password' => Hash::make('password123'),
            'role' => 'company',
        ]);

        $response = $this->actingAs($companyUser, 'api')
            ->getJson('/api/announcement');

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_access_single_announcement_endpoint()
    {
        $response = $this->getJson('/api/announcement');
        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function html_content_is_sanitized()
    {
        $htmlContent = '<h1>Test Header</h1><p>This is <strong>bold</strong> and <em>italic</em> text.</p><ul><li>List item 1</li><li>List item 2</li></ul><p><a href="https://example.com">Link</a></p>';

        $response = $this->actingAs($this->adminUser, 'api')
            ->putJson('/api/announcement', [
                'content' => $htmlContent,
                'is_published' => true,
            ]);

        $response->assertStatus(200);

        // Get the updated announcement
        $announcement = Announcement::where('content', $htmlContent)->first();
        $this->assertNotNull($announcement);
        
        // Check that HTML content was sanitized
        $this->assertStringContainsString('<h1>', $announcement->content_sanitized);
        $this->assertStringContainsString('<strong>', $announcement->content_sanitized);
        $this->assertStringContainsString('<em>', $announcement->content_sanitized);
        $this->assertStringContainsString('<a href=', $announcement->content_sanitized);
        
        // Check that dangerous content would be removed (test with script tag)
        $dangerousContent = '<script>alert("xss")</script><p>Safe content</p>';
        $announcement->content = $dangerousContent;
        $announcement->save();
        
        $this->assertStringNotContainsString('<script>', $announcement->content_sanitized);
        $this->assertStringContainsString('<p>Safe content</p>', $announcement->content_sanitized);
    }
}
