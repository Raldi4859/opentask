<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class loginTest extends TestCase
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

    // Login test
    public function test_login_page_can_be_accessed()
    {
        $response = $this->get('/');
    
        $response->assertStatus(200);
    }

    public function test_users_can_login()
    {
        $this->post('register/action', [
            'email' => 'testemail@example.com',
            'name' => 'namauser',
            'password' => 'secret'
        ]);

        $response = $this->post('actionlogin', [
            'email' => 'testemail@example.com',
            'password' => 'secret'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('home');
    }

    public function test_users_can_logout()
    {
        $response = $this->get('actionlogout');
    
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
