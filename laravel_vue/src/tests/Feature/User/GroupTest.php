<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection $groups
     */
    private Collection $groups;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create();

        $this->groups = Group::factory(2)
            ->create()
            ->sortByDesc('id', SORT_NUMERIC)
            ->values();
    }

        /**
     * @return void
     */
    public function testfindAll(): void
    {
        $groups = Group::all();

        $this->assertInstanceOf(Collection::class, $groups);
        $this->assertCount(2, $groups);

        for ($i = 0; $i < count($groups); $i++) {
            $this->assertSame($this->groups[$i]->id, $groups[$i]->id);
            $this->assertSame($this->groups[$i]->name, $groups[$i]->name);
            $this->assertSame($this->groups[$i]->owner_user_id, $groups[$i]->owner_user_id);
            $this->assertSame((string)$this->groups[$i]->created_at, (string)$groups[$i]->created_at);
            $this->assertSame((string)$this->groups[$i]->updated_at, (string)$groups[$i]->updated_at);
            $this->assertSame((string)$this->groups[$i]->deleted_at, (string)$groups[$i]->deleted_at);
        }
    }
}