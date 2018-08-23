<?php
/**
 * Created by PhpStorm.
 * User: beren
 * Date: 22/08/2018
 * Time: 14:28
 */

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testGetUsersAll(){
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users',
            [],
            [],
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_AUTH-TOKEN' => '5b7e65ec8db4f7.47229317',
            ]
        );

        $response = $client->getResponse();
        $content =$response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = json_decode($content, true);

        $this->assertCount(10, $arrayContent);
    }

    public function testGetUsersOne(){
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users',
            [
                'id' => '1',
            ],
            [],
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_AUTH-TOKEN' => '5b7e65ec8db4f7.47229317',
            ]
        );

        $response = $client->getResponse();
        $content =$response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = json_decode($content, true);

        $this->assertCount(10, $arrayContent);
    }

    public function testPostUsers(){
        $data = [
            "firstname" => "ffff",
            "lastname"=> "Ebert",
            "email"=> "aletha.fley@muller.org",
            "birthday"=> "1983-06-22T13:54:31+00:00",
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            [
                'HTTP_CONTENT_TYPE' => 'application/json',
//                'AUTH-TOKEN' => '5b7e65ec8db4f7.47229317',
            ],
            json_encode($data)
        );

        $response = $client->getResponse();
        $content =$response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = json_decode($content, true);
    }

//    public function testDeleteUsers(){
//        $client = static::createClient();
//        $client->request(
//            'DELETE',
//            '/api/users/10',
//            [],
//            [],
//            [
//                'CONTENT_TYPE' => 'application/json',
//                'HTTP_AUTH-TOKEN' => '5b7e65ec8db4f7.47229317',
//            ]
//        );
//
//        $response = $client->getResponse();
//        $content =$response->getContent();
//
//        $this->assertEquals(200, $response->getStatusCode());
//        $this->assertJson($content);
//
//        $arrayContent = json_decode($content, true);
//
//        $this->assertCount(10, $arrayContent);
//    }

}