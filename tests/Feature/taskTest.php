<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class taskTest extends TestCase
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

    public function test_users_can_access_create_task_form()
    {
        $response = $this->get('task/create');

        $response->assertStatus(200);
    }

    public function test_users_can_create_new_task()
    {
        $response = $this->post('addTask', [
            'name' => 'tasktitle',
            'description' => 'taskdescription',
            'due_date' => '2023-01-27',
            'status' => 'Todo'
        ]);

        $response->assertRedirect('home');
    }
}
