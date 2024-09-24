<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Plat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_dishes(): void
    {
        $user = User::factory()->create();
        // Créer des plats avec factory
           Plat::factory()->count(3)->create();
           $response = $this->actingAs($user)->get('/plats');
           $response->assertStatus(200);}


    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_plat(): void
    {
        $user = User::factory()->create();


        $response = $this->actingAs($user)->post('/plats', [
            'titre' => 'Nouveau Plat',
            'recette' => 'Description du plat',

        ]);

        $response->assertStatus(302); // Vérifie que la réponse est une redirection
        $response->assertRedirect(route('plats.show', Plat::first())); // Vérifie la redirection vers la route 'plats.show'
        $this->assertDatabaseHas('plats', ['titre' => 'Nouveau Plat']);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_a_plat()
    {
        $user = User::factory()->create();
        $plat = Plat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/plats/{$plat->id}", [
            'titre' => 'Plat Modifié',
            'recette' => 'Description modifiée',

        ]);

        $response->assertStatus(302); // Vérifie que la réponse est une redirection
        $response->assertRedirect(route('plats.show', Plat::first())); // Vérifie la redirection vers la route 'plats.show'
        $this->assertDatabaseHas('plats', ['id' => $plat->id, 'titre' => 'Plat Modifié']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_delete_a_plat()
    {
        $user = User::factory()->create()->assignRole('administrator');
        $plat = Plat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/plats/{$plat->id}");

        $response->assertStatus(302);
        $response->assertRedirect(route('plats.index'));
        $this->assertDatabaseMissing('plats', ['id' => $plat->id]);
    }
}
