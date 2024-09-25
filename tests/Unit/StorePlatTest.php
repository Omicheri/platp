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

        $existingPlat = Plat::factory()->create(['titre' => 'Plat Unique']);

        $response = $this->post('/plats', [
            'titre' => 'Plat Unique',
            'recette' => 'Recette du plat',
            'Likes' => 10,
            'Image' => 'pfkd'
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
        $plat = Plat::factory()->createOne(['user_id' => $user->id]);

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
        $response = $this->actingAs($plat->user)->patch("/plats/{$plat->id}", [
            'titre' => 'Plat Unique',
            'recette' => 'Recette du plat',
        ]);
        $response->assertStatus(302);
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
        $plat1 = Plat::factory()->create(['titre' => "Delicius dick",'user_id' => $user->id,'likes' => 1]);
        $plat2 = Plat::factory()->create(['titre' => "Delicius kcid",'user_id' => $user->id,'likes' => 1]);

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

        $response = $this->get("/plats?search=Delicius kcid");

        $response->assertStatus(200);
        $response->assertSee('Delicius kcid');
        $response->assertDontSee('Delicius dick');
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

        $response = $this->get('/plats?sort=Likes&direction=asc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([10, 20, 30], $plats->pluck('Likes')->toArray());

        $response = $this->get('/plats?sort=Likes&direction=desc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([30, 20, 10], $plats->pluck('Likes')->toArray());

        $response = $this->get('/plats?sort=Titre&direction=asc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals(['Plat A', 'Plat B', 'Plat C'], $plats->pluck('Titre')->toArray());

        $response = $this->get('/plats?sort=Titre&direction=desc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals(['Plat C', 'Plat B', 'Plat A'], $plats->pluck('Titre')->toArray());

        $response = $this->get('/plats?sort=user_id&direction=asc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([$user1->id, $user2->id, $user3->id], $plats->pluck('user_id')->toArray());

        $response = $this->get('/plats?sort=user_id&direction=desc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([$user3->id, $user2->id, $user1->id], $plats->pluck('user_id')->toArray());

        $response = $this->get('/plats?sort=is_favori&direction=desc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([1, 1, 0], $plats->pluck('is_favori')->toArray());

        // Test de tri par Favori en ordre croissant
        $response = $this->get('/plats?sort=is_favori&direction=asc');
        $response->assertStatus(200);
        $plats = $response->viewData('plats');
        $this->assertEquals([0, 1, 1], $plats->pluck('is_favori')->toArray());

    }
}
