<?php

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\Plat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StorePlatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fails_validation_if_titre_is_not_unique()
    {
        $user = User::factory()->createOne()->assignRole('administrator');
        $existingPlat = Plat::factory()->create(['titre' => 'Plat Unique', 'user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/plats', [
            'titre' => 'Plat Unique',
            'recette' => 'Recette du plat',
            'Likes' => 10,
            'Image' => 'pfkd',
            'user_id' => '4'
        ]);


        $response->assertStatus(302);
        $response->assertSessionHasErrors('titre');
    }

    /** @test */
    public function it_passes_validation_if_titre_is_unique()
    {

        $existingPlat = Plat::factory()->create(['titre' => 'Plat Unique']);


        $response = $this->post('/plats', [
            'titre' => 'Plat Différent',
            'recette' => 'Recette du plat',
            'likes' => 10,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_fails_delete_if_user_is_not_admin()
    {
        $user = User::factory()->createOne();
        $plat = Plat::factory()->createOne(['user_id' => 4]);

        $response = $this->actingAs($user)->delete("/plats/{$plat->id}");

        $response->assertStatus(302);
        $this->assertDatabaseHas('plats', ['id' => $plat->id]);
    }

    /** @test */
    public function it_passes_delete_if_user_is__admin()
    {
        $user = User::factory()->createOne()->assignRole('administrator');
        $plat = Plat::factory()->createOne(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/plats/{$plat->id}");

        $response->assertStatus(302);
        $response->assertRedirect(route('plats.index'));
        $this->assertDatabaseMissing('plats', ['id' => $plat->id]);
    }

    /** @test */
    public function it_passes_edit_if_title_is_not_modify_in_edit__view()
    {
        $plat = Plat::factory()->create(['titre' => 'Plat Unique']);
        $user = User::factory()->createOne()->assignRole('administrator');
        $response = $this->actingAs($user)->patch("/plats/{$plat->id}", [
            'titre' => 'Plat Unique',
            'recette' => 'Recette du plat',
        ]);

        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function it_add__favoris(): void
    {
        $user = User::factory()->createOne();
        $plat = Plat::factory()->create(['likes' => 0]);
        $this->actingAs($user);

        $response = $this->post("/plats/{$plat->id}/favori");

        $response->assertStatus(302);

        $this->assertTrue($user->favoris()->where('plat_id', $plat->id)->exists());

        // Vérifie que le nombre de likes a été incrémenté
        $this->assertEquals(1, $plat->fresh()->Likes);
    }

    /** @test */
    public function it_del__favoris(): void
    {
        $user = User::factory()->createOne();
        $plat = Plat::factory()->create(['likes' => 1]);

        $user->favoris()->toggle($plat->id);

        $this->actingAs($user);

        $response = $this->post("/plats/{$plat->id}/favori");

        $response->assertStatus(302);

        $this->assertFalse($user->favoris()->where('plat_id', $plat->id)->exists());

        // Vérifie que le nombre de likes a été incrémenté
        $this->assertEquals(0, $plat->fresh()->Likes);
    }

    /** @test */
    public function it_can_search_plat_by__title(): void
    {
        $user = User::factory()->createOne();
        $plat1 = Plat::factory()->create(['titre' => "Delicius dick", 'user_id' => $user->id, 'likes' => 1]);
        $plat2 = Plat::factory()->create(['titre' => "Delicius kcid", 'user_id' => $user->id, 'likes' => 1]);

        $response = $this->actingAs($user)->get("/plats?search=Delicius kcid");

        $response->assertStatus(200);
        $response->assertSee('Delicius kcid');
        $response->assertDontSee('Delicius dick');
    }

    /** @test */
    public function it_can_search_plat_by_user__name(): void
    {
        $user3 = User::factory()->createOne(['name' => 'John Moe']);
        $user4 = User::factory()->createOne(['name' => 'Jane Moe']);
        $plat1 = Plat::factory()->create(['user_id' => $user3->id, 'likes' => 1]);
        $plat2 = Plat::factory()->create(['user_id' => $user4->id, 'likes' => 1]);

        $response = $this->actingAs($user3)->get("/plats?search=Delicius kcid");

        $response->assertStatus(200);
        $response->assertSee('Delicius kcid');
        $response->assertDontSee('Delicius dick');
    }
    /** @test */
    public function it_observe_and_deletes_favoris_when_plat_is_deleted()
    {
        $user = User::factory()->createOne();

        $plat = Plat::factory()->create(['user_id' => $user->id]);

        $user->favoris()->toggle($plat->id);

        $this->assertDatabaseHas('favoris', [
            'user_id' => $user->id,
            'plat_id' => $plat->id,
        ]);
        $plat->delete();

        $this->assertDatabaseMissing('favoris', [
            'user_id' => $user->id,
            'plat_id' => $plat->id,
        ]);
    }

    /** @test */
    public function it_can_sort_plats(): void
    {
        $user = User::factory()->createOne();
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();
        $user3 = User::factory()->createOne();

        $this->actingAs($user);

        $plat1 = Plat::factory()->create(['titre' => 'Plat A', 'likes' => 10, 'user_id' => $user1->id]);
        $plat2 = Plat::factory()->create(['titre' => 'Plat B', 'likes' => 20, 'user_id' => $user2->id]);
        $plat3 = Plat::factory()->create(['titre' => 'Plat C', 'likes' => 30, 'user_id' => $user3->id]);

        $user->favoris()->toggle($plat1->id);
        $user->favoris()->toggle($plat2->id);

        $sortOptions = [
            'Likes' => [
                'asc' => [10, 20, 30],
                'desc' => [30, 20, 10],
            ],
            'Titre' => [
                'asc' => ['Plat A', 'Plat B', 'Plat C'],
                'desc' => ['Plat C', 'Plat B', 'Plat A'],
            ],
            'user_id' => [
                'asc' => [$user1->id, $user2->id, $user3->id],
                'desc' => [$user3->id, $user2->id, $user1->id],
            ],
            'is_favori' => [
                'asc' => [0, 1, 1],
                'desc' => [1, 1, 0],
            ],
        ];

        foreach ($sortOptions as $sort => $directions) {
            foreach ($directions as $direction => $expected) {
                $response = $this->get("/plats?sort={$sort}&direction={$direction}");
                $response->assertStatus(200);
                $plats = $response->viewData('plats');
                $this->assertEquals($expected, $plats->pluck($sort)->toArray());
            }
        }
    }

    public function test_administrator_can_create_plat()
    {
        $admin = User::factory()->createOne()->assignRole('administrator');

        $response = $this->actingAs($admin)->post('/plats', [
            'titre' => 'Nouveau Plat',
            'recette' => 'Description du plat',

        ]);

        $response->assertStatus(Response::HTTP_FOUND); // Vérifie que la réponse est une redirection
        $response->assertRedirect(route('plats.show', Plat::first())); // Vérifie la redirection vers la route 'plats.show'
        $this->assertDatabaseHas('plats', ['titre' => 'Nouveau Plat']);
    }

    public function test_administrator_can_delete_any_plat()
    {
        $admin = User::factory()->create()->assignRole('administrator');
        $plat = Plat::factory()->create();

        $response = $this->actingAs($admin)->delete("/plats/{$plat->id}");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('plats.index'));
        $this->assertDatabaseMissing('plats', ['id' => $plat->id]);
    }

    public function test_owner_can_delete_their_own_plat()
    {
        $user = User::factory()->create()->assignRole('user');

        $plat = Plat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/plats/{$plat->id}");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('plats.index'));
        $this->assertDatabaseMissing('plats', ['id' => $plat->id]);
    }

    public function test_non_owner_cannot_delete_plat()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');
        $plat = Plat::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('plats.destroy', $plat));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Vous devez être administrateur ou propriétaire pour supprimer ce plat');
    }
    public function test_non_owner_cannot_edit_plat()
    {
    $user = User::factory()->createOne()->assignRole('user');
    $otherUser = User::factory()->createOne()->assignRole('user');
        $plat = Plat::factory()->create(['user_id' => $otherUser->id,'likes' => 5,'titre' =>'Plat A']);

        $response = $this->actingAs($user)->patch("/plats/{$plat->id}", ['titre' => 'Nouveau Plat', 'recette' => 'Description du plat']);

        $this->assertDatabaseHas('plats', ['titre' => 'Plat A']); // Vérifie que le plat n'a pas été modifié

    }


}
