<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;
use App\Http\Controllers\ChirpController;

class ChirpControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing that the index method returns the chirps index view.
     */
    public function test_index_method_returns_index_view(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a new instance of the ChirpController
        $controller = new ChirpController();

        // Call the index method on the controller
        $response = $controller->index();

        // Assert that the response is an Inertia response
        $this->assertInstanceOf(\Inertia\Response::class, $response);
    }

    /**
     * Testing that the index method retrieves chirps from database if available.
     */
    public function test_index_method_retrieves_chirps(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a chirp
        $chirp = Chirp::factory()->create([
            'user_id' => $user->id,
            'message' => 'Test chirp',
        ]);

        // Make a GET request to the chirps index route
        $response = $this->get(route('chirps.index'));

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the response contains the chirp
        $response->assertSee('Test chirp');
    }

    /**
     * Testing that the store method saves a chirp to the database.
     */
    public function test_store_method_creates_chirp(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Make a POST request to the chirps store route
        $response = $this->post(route('chirps.store'), [
            'message' => 'Test chirp',
        ]);

        // Assert that the chirp was created in the database
        $this->assertDatabaseHas('chirps', [
            'message' => 'Test chirp',
            'user_id' => $user->id,
        ]);

        // Assert that the response redirects to the chirps index route
        $response->assertRedirect(route('chirps.index'));
    }
}
