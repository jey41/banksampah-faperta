<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $nasabah;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->petugas()->create();
        $this->nasabah = User::factory()->nasabah()->create();
    }

    public function test_admin_can_view_articles_index(): void
    {
        Article::factory()->count(3)->published()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('cms.articles.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_article(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.articles.store'), [
                'title' => 'Test Article',
                'content' => 'This is test content for the article.',
                'status' => 'published',
                'image' => UploadedFile::fake()->image('article.jpg', 800, 600),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_update_article(): void
    {
        $article = Article::factory()->published()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('cms.articles.update', $article), [
                'title' => 'Updated Title',
                'content' => 'Updated content.',
                'status' => 'draft',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $article->refresh();
        $this->assertEquals('Updated Title', $article->title);
        $this->assertEquals('draft', $article->status);
    }

    public function test_admin_can_delete_article(): void
    {
        $article = Article::factory()->published()->create();

        // Only super_admin can delete articles (per ArticlePolicy)
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)
            ->delete(route('cms.articles.destroy', $article));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_nasabah_cannot_access_admin_articles(): void
    {
        $response = $this->actingAs($this->nasabah)
            ->get(route('cms.articles.index'));

        // Nasabah is redirected away from admin panel
        $response->assertRedirect();
    }

    public function test_article_requires_title(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.articles.store'), [
                'title' => '',
                'content' => 'Content without title.',
                'status' => 'draft',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_article_requires_content(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.articles.store'), [
                'title' => 'Title without content',
                'content' => '',
                'status' => 'draft',
            ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_article_slug_is_unique(): void
    {
        Article::factory()->create(['slug' => 'test-article']);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.articles.store'), [
                'title' => 'Test Article',
                'content' => 'Content with duplicate slug.',
                'status' => 'draft',
            ]);

        // Should either auto-generate unique slug or show error
        // This depends on implementation - check if slug is auto-generated
        $this->assertDatabaseCount('articles', 2); // Should succeed with auto-generated slug
    }

    public function test_article_image_is_stored_in_public_disk(): void
    {
        $image = UploadedFile::fake()->image('test.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.articles.store'), [
                'title' => 'Article with Image',
                'content' => 'Content with image.',
                'status' => 'published',
                'image' => $image,
            ]);

        $response->assertRedirect();

        $article = Article::where('title', 'Article with Image')->first();
        $this->assertNotNull($article->image_path);
        Storage::disk('public')->assertExists($article->image_path);
    }
}
