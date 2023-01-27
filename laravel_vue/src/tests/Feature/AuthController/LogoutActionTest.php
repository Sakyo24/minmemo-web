<?php

declare(strict_types=1);

namespace Tests\Feature\AuthController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * @return void
     */
    public function testLogout(): void
    {
        // リクエスト
        $response = $this->actingAs($this->user)->postJson('/logout', []);

        // 検証
        $response->assertNoContent();

        $this->assertGuest();
    }
}
