<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * POST '/v1/settings/password'
     */
    public function can_update_user_password()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings/password", [
                'current' => 'password',
                'new' => 'new-password',
                'confirm' => 'new-password'
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /**
     * @test
     * POST '/v1/settings/password'
     */
    public function password_change_must_provide_current_new_confirm()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings/password", []);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('data.current.0', 'The current field is required.');
        $response->assertJsonPath('data.new.0', 'The new field is required.');
        $response->assertJsonPath('data.confirm.0', 'The confirm field is required.');
        $response->assertJsonPath('message', 'validation-error');
    }

    /**
     * @test
     * POST '/v1/settings/password'
     */
    public function can_not_change_password_unless_current_is_correct()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings/password", [
                'current' => 'wrong-password',
                'new' => 'new-password',
                'confirm' => 'new-password'
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('data.current.0', 'Current password is invalid');
        $response->assertJsonPath('message', 'validation-error');
    }

    /**
     * @test
     * POST '/v1/settings/password'
     */
    public function can_not_change_password_unless_confirm_matches()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings/password", [
                'current' => 'password',
                'new' => 'new-password',
                'confirm' => 'wrong-password'
            ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('data.confirm.0', 'Confirmation is invalid');
        $response->assertJsonPath('message', 'validation-error');
    }

    /**
     * @test
     * PUT '/v1/settings'
     */
    public function can_post_permitted_settings()
    {
        $this->spew();
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings", [
                'default_location_id' => $location->id,
                'default_today_view' => 'patients',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.settings.default_location_id', $location->id);
        $response->assertJsonPath('data.settings.default_today_view', 'patients');

        tap($user->fresh(), function($user) use($location) {
            $this->assertEquals($location->id, $user->settings['default_location_id']);
            $this->assertEquals('patients', $user->settings['default_today_view']);
        });
    }

    /**
     * @test
     * PUT '/v1/settings'
     */
    public function invalid_settings_get_ignored()
    {
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $response = $this->actingAs($user, 'airlock')
            ->json('PUT', "v1/settings", [
                'foo' => 'bar',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.first_name', $user->first_name);
        $response->assertJsonPath('data.settings.foo', null);
        $this->assertCount(0, $user->fresh()->settings);
    }
}
