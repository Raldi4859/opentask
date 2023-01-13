<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class registerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // Registration test
    public function test_registration_page_can_be_accessed()
    {
        $response = $this->get('register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('register/action', [
            'email' => 'testemail@example.com',
            'name' => 'namauser',
            'password' => 'secret'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}