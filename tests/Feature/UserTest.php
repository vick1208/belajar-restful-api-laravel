<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSucceed()
    {
        $this->post('/api/users', [
            'username' => 'edwin321',
            'password' => 'secret',
            'name' => 'Edwin Kurniawan Khannedy'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => 'edwin321',
                    "name" => 'Edwin Kurniawan Khannedy'
                ]
            ]);
    }
    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }
    public function testRegisterExistedUsername()
    {
        $this->testRegisterSucceed();
        $this->post('/api/users', [
            'username' => 'edwin321',
            'password' => 'secret',
            'name' => 'Edwin Kurniawan Khannedy'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                "username" => [
                    "username already registered"
                ]
            ]
        ]);
    }
}
