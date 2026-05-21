<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BorrowingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('fine_per_day', '5');

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);

        $category = Category::factory()->create();

        $this->book = Book::create([
            'title' => 'Test Book',
            'slug' => 'test-book',
            'author_name' => 'Test Author',
            'category_id' => $category->id,
            'quantity_total' => 3,
            'quantity_available' => 3,
            'is_published' => true,
            'isbn' => '9781234567890',
            'status' => 'available',
            'is_active' => true,
        ]);
    }

    public function test_user_can_request_borrow(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/borrowings', [
            'book_id' => $this->book->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('borrowing.status', 'pending')
            ->assertJsonPath('message', "Demande d'emprunt envoyée. En attente de confirmation.");

        $this->assertDatabaseHas('borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'status' => 'pending',
        ]);

        $this->book->refresh();
        $this->assertEquals(3, $this->book->quantity_available);
    }

    public function test_user_cannot_request_duplicate_pending(): void
    {
        Sanctum::actingAs($this->user);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/borrowings', [
            'book_id' => $this->book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Vous avez déjà une demande en attente pour ce livre.');
    }

    public function test_user_cannot_request_duplicate_borrowed(): void
    {
        Sanctum::actingAs($this->user);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->postJson('/api/borrowings', [
            'book_id' => $this->book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Vous avez déjà un emprunt en cours pour ce livre.');
    }

    public function test_user_cannot_request_when_no_stock(): void
    {
        Sanctum::actingAs($this->user);

        $this->book->update(['quantity_available' => 0]);

        $response = $this->postJson('/api/borrowings', [
            'book_id' => $this->book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Aucun exemplaire disponible.');
    }

    public function test_user_cannot_request_unpublished_book(): void
    {
        Sanctum::actingAs($this->user);

        $this->book->update(['is_published' => false]);

        $response = $this->postJson('/api/borrowings', [
            'book_id' => $this->book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', "Ce livre n'est pas disponible.");
    }

    public function test_admin_can_confirm_pending_request(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/confirm", [
            'borrowed_at' => now()->format('Y-m-d'),
            'due_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('borrowing.status', 'borrowed')
            ->assertJsonPath('message', 'Emprunt confirmé.');

        $this->book->refresh();
        $this->assertEquals(2, $this->book->quantity_available);
    }

    public function test_admin_cannot_confirm_non_pending(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/confirm", [
            'borrowed_at' => now()->format('Y-m-d'),
            'due_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Cette demande a déjà été traitée.');
    }

    public function test_admin_can_refuse_pending_request(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/refuse", [
            'reason' => 'Stock épuisé',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('borrowing.status', 'rejected')
            ->assertJsonPath('message', 'Demande refusée.');

        $this->book->refresh();
        $this->assertEquals(3, $this->book->quantity_available);
    }

    public function test_admin_can_update_due_date(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $newDate = now()->addDays(21)->format('Y-m-d');

        $response = $this->putJson("/api/admin/borrowings/{$borrowing->id}/due-date", [
            'due_at' => $newDate,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Date de retour modifiée.');

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
        ]);
    }

    public function test_admin_can_return_borrowed_book(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->addDays(4),
            'status' => 'borrowed',
        ]);

        $this->book->decrement('quantity_available');

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/return");

        $response->assertStatus(200)
            ->assertJsonPath('borrowing.status', 'returned');

        $this->book->refresh();
        $this->assertEquals(3, $this->book->quantity_available);
    }

    public function test_overdue_return_creates_fine(): void
    {
        Sanctum::actingAs($this->admin);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(20),
            'due_at' => now()->subDays(5),
            'status' => 'borrowed',
        ]);

        $this->book->decrement('quantity_available');

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/return");

        $response->assertStatus(200);

        $this->assertDatabaseHas('fines', [
            'borrowing_id' => $borrowing->id,
            'user_id' => $this->user->id,
            'status' => 'unpaid',
        ]);

        $fine = Fine::where('borrowing_id', $borrowing->id)->first();
        $this->assertNotNull($fine);
        $this->assertEquals(25, $fine->amount);

        $this->book->refresh();
        $this->assertEquals(3, $this->book->quantity_available);
    }

    public function test_non_admin_cannot_confirm(): void
    {
        Sanctum::actingAs($this->user);

        $borrowing = Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/admin/borrowings/{$borrowing->id}/confirm", [
            'borrowed_at' => now()->format('Y-m-d'),
            'due_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_pending_not_in_my_borrowings(): void
    {
        Sanctum::actingAs($this->user);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/my-borrowings');

        $response->assertStatus(200);
        $data = $response->json('data') ?? [];
        $this->assertCount(0, $data);
    }

    public function test_user_can_view_own_borrowings(): void
    {
        Sanctum::actingAs($this->user);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->getJson('/api/my-borrowings');

        $response->assertStatus(200);
    }

    public function test_user_can_view_own_history(): void
    {
        Sanctum::actingAs($this->user);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(20),
            'due_at' => now()->subDays(5),
            'returned_at' => now(),
            'status' => 'returned',
        ]);

        $response = $this->getJson('/api/my-borrowings/history');

        $response->assertStatus(200);
    }

    public function test_admin_store_creates_borrowed_directly(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/admin/borrowings', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'due_at' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('borrowing.status', 'borrowed');

        $this->book->refresh();
        $this->assertEquals(2, $this->book->quantity_available);
    }

    public function test_admin_can_list_borrowings_with_status_filter(): void
    {
        Sanctum::actingAs($this->admin);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now(),
            'due_at' => now()->addDays(14),
            'status' => 'pending',
        ]);

        Borrowing::create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->addDays(4),
            'status' => 'borrowed',
        ]);

        $response = $this->getJson('/api/admin/borrowings?status=pending');
        $response->assertStatus(200);

        $response = $this->getJson('/api/admin/borrowings?status=borrowed');
        $response->assertStatus(200);

        $response = $this->getJson('/api/admin/borrowings/history');
        $response->assertStatus(200);
    }
}
