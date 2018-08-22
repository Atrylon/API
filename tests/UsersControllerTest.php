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
    public function testGetUsers(){
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = json_decode($content, true);

        $this->assertCount(10, $arrayContent);
    }

}