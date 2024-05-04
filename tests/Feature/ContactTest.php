<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactFindSeed;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contacts',
            [
                'first_name' => 'Javier',
                'last_name' => 'Pena',
                'email' => 'javier@pzn.com',
                'phone' => '03324234',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)->assertJson([
            'data' => [
                'first_name' => 'Javier',
                'last_name' => 'Pena',
                'email' => 'javier@pzn.com',
                'phone' => '03324234',
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post(
            '/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'Pena',
                'email' => 'javier.com',
                'phone' => '03324234',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => ['The first name field is required.'],
                'email' => ['The email field must be a valid email address.'],
            ]
        ]);
    }
    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Khannedy',
            'email' => 'eko',
            'phone' => '03242343243'
        ], [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@pzn.com',
                    'phone' => '111111',
                ]
            ]);
    }
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }
    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test2@pzn.com',
            'phone' => '1111112',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test2',
                    'last_name' => 'test2',
                    'email' => 'test2@pzn.com',
                    'phone' => '1111112',
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'test2',
            'email' => 'test2@pzn.com',
            'phone' => '1111112',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testSearchFirstName()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);
        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(10, count($response['data']));
        assertEquals(30, $response['meta']['total']);
    }
    public function testSearchLastName()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);
        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])->assertStatus(200)->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(10, count($response['data']));
        assertEquals(30, $response['meta']['total']);
    }
    public function testSearchEmail()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(10, count($response['data']));
        assertEquals(30, $response['meta']['total']);
    }
    public function testSearchPhone()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);

        $response = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(10, count($response['data']));
        assertEquals(30, $response['meta']['total']);
    }
    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);

        $response = $this->get('/api/contacts?name=empty_name', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(0, count($response['data']));
        assertEquals(0, $response['meta']['total']);
    }
    public function testSearchPage()
    {
        $this->seed([UserSeeder::class, ContactFindSeed::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        assertEquals(5, count($response['data']));
        assertEquals(30, $response['meta']['total']);
        assertEquals(2, $response['meta']['current_page']);
    }
}
