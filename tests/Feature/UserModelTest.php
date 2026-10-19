<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_unique_username_appends_a_suffix_on_collision(): void
    {
        User::factory()->create(['username' => 'ada']);

        $this->assertSame('ada-1', User::uniqueUsername('Ada'));

        User::factory()->create(['username' => 'ada-1']);
        $this->assertSame('ada-2', User::uniqueUsername('Ada'));
    }

    public function test_unique_username_falls_back_when_base_has_no_slug(): void
    {
        $this->assertSame('founder', User::uniqueUsername('!!!'));
    }

    public function test_public_username_falls_back_to_user_id(): void
    {
        $withHandle = User::factory()->create(['username' => 'grace']);
        $this->assertSame('grace', $withHandle->publicUsername());

        $noHandle = User::factory()->create(['username' => null]);
        $this->assertSame('user-'.$noHandle->id, $noHandle->publicUsername());
    }
}
