<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed categories for every test
        $this->seed(CategorySeeder::class);
    }

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
    }

    public function test_register_page_renders(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
    }

    public function test_home_page_requires_authentication(): void
    {
        $response = $this->get('/home');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_home(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);
        $response->assertSee('Dasbor Keuangan');
        $response->assertSee(gethostname()); // Validasi visual indicator di footer
    }

    public function test_authenticated_user_can_create_expense(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::first();

        $response = $this->actingAs($user)->post('/expenses', [
            'category_id' => $category->id,
            'amount' => 150000,
            'description' => 'Makan bareng teman',
            'date' => '2026-06-03',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 150000.00,
            'description' => 'Makan bareng teman',
            'date' => '2026-06-03',
        ]);
    }

    public function test_authenticated_user_can_update_expense(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::first();
        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 150000,
            'description' => 'Makan bareng teman',
            'date' => '2026-06-03',
        ]);

        $response = $this->actingAs($user)->put("/expenses/{$expense->id}", [
            'category_id' => $category->id,
            'amount' => 200000,
            'description' => 'Makan malam mewah',
            'date' => '2026-06-03',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 200000.00,
            'description' => 'Makan malam mewah',
        ]);
    }

    public function test_authenticated_user_can_delete_expense(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::first();
        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 150000,
            'description' => 'Makan bareng teman',
            'date' => '2026-06-03',
        ]);

        $response = $this->actingAs($user)->delete("/expenses/{$expense->id}");

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_authenticated_user_can_create_income(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::where('type', 'income')->first();

        $response = $this->actingAs($user)->post('/incomes', [
            'category_id' => $category->id,
            'amount' => 5000000,
            'description' => 'Gaji bulanan',
            'date' => '2026-06-04',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('incomes', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 5000000.00,
            'description' => 'Gaji bulanan',
            'date' => '2026-06-04',
        ]);
    }

    public function test_authenticated_user_can_update_income(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::where('type', 'income')->first();
        $income = Income::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 5000000,
            'description' => 'Gaji bulanan',
            'date' => '2026-06-04',
        ]);

        $response = $this->actingAs($user)->put("/incomes/{$income->id}", [
            'category_id' => $category->id,
            'amount' => 5500000,
            'description' => 'Gaji bulanan + bonus',
            'date' => '2026-06-04',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'amount' => 5500000.00,
            'description' => 'Gaji bulanan + bonus',
        ]);
    }

    public function test_authenticated_user_can_delete_income(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::where('type', 'income')->first();
        $income = Income::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 5000000,
            'description' => 'Gaji bulanan',
            'date' => '2026-06-04',
        ]);

        $response = $this->actingAs($user)->delete("/incomes/{$income->id}");

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('incomes', [
            'id' => $income->id,
        ]);
    }

    public function test_dashboard_displays_correct_balance_and_totals(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $incomeCat = Category::where('type', 'income')->first();
        $expenseCat = Category::where('type', 'expense')->first();

        // Tambah income Rp 1.000.000
        Income::create([
            'user_id' => $user->id,
            'category_id' => $incomeCat->id,
            'amount' => 1000000,
            'description' => 'Freelance',
            'date' => '2026-06-04',
        ]);

        // Tambah expense Rp 400.000
        Expense::create([
            'user_id' => $user->id,
            'category_id' => $expenseCat->id,
            'amount' => 400000,
            'description' => 'Belanja',
            'date' => '2026-06-04',
        ]);

        $response = $this->actingAs($user)->get('/home');
        $response->assertStatus(200);
        
        // Verifikasi total pemasukan, pengeluaran, dan saldo bersih ditampilkan di dasbor
        $response->assertSee('Rp 1.000.000');
        $response->assertSee('Rp 400.000');
        $response->assertSee('Rp 600.000'); // Saldo bersih (1.000.000 - 400.000)
    }
}
