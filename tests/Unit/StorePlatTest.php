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

}
