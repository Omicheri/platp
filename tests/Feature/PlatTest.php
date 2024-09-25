<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Plat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class PlatTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_dishes(): void
    {
        $user = User::factory()->create();

           Plat::factory()->count(3)->create();
           $response = $this->actingAs($user)->get('/plats');
           $response->assertStatus(Response::HTTP_OK);}


    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_plat(): void
    {
        $user = User::factory()->create();


        $response = $this->actingAs($user)->post('/plats', [
            'titre' => 'Nouveau Plat',
            'recette' => 'Description du plat',

        ]);

        $response->assertStatus(Response::HTTP_FOUND); // Vérifie que la réponse est une redirection
        $response->assertRedirect(route('plats.show', Plat::first())); // Vérifie la redirection vers la route 'plats.show'
        $this->assertDatabaseHas('plats', ['titre' => 'Nouveau Plat']);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_a_plat(): void
    {
        $plat = Plat::factory()->create();

        $response = $this->actingAs($plat->user)->patch("/plats/{$plat->id}", [
            'titre' => 'Plat Modifié',
            'recette' =>'Description modifiée'
        ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('plats.show', $plat));
        $this->assertEquals($plat->refresh()->Titre,'Plat Modifié');
        $this->assertEquals($plat->refresh()->Recette,'Description modifiée');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_delete_a_plat()
    {
        $user = User::factory()->create()->assignRole('administrator');
        $plat = Plat::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/plats/{$plat->id}");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('plats.index'));
        $this->assertDatabaseMissing('plats', ['id' => $plat->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_classify_likes()
    {
        $user = User::factory()->createOne();
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();

        $user1->plats()->createMany([
            ['titre' => 'Plat 1', 'recette' => 'tu', 'Likes' => 10, 'Image' => 'kdm'],
            ['titre' => 'Plat 2', 'recette' => 'me', 'Likes' => 20, 'Image' => 'kdm'],
        ]);

        $user2->plats()->createMany([
            ['titre' => 'Plat 3', 'recette' => 'manque', 'Likes' => 30, 'Image' => 'kdm'],
            ['titre' => 'Plat 4', 'recette' => 'bcp', 'Likes' => 40, 'Image' => 'kdm'],
        ]);

        $response = $this->actingAs($user)->get('/plats/topcrea');

        $response->assertStatus(Response::HTTP_OK);
        //Vérification que la vue contient une variable users et exécution d’une fonction de rappel pour valider son contenu
        $response->assertViewHas('users', function ($users) use ($user1, $user2) {
            $user1TotalLikes = $user1->plats->sum('Likes');
            $user2TotalLikes = $user2->plats->sum('Likes');
        // Vérification que le premier utilisateur dans la liste a plus de likes que le dernier utilisateur.
            return $users->first()->plats->sum('Likes') > $users->last()->plats->sum('Likes');
        });

    }
}
