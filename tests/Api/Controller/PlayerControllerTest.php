<?php

namespace App\Tests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PlayerControllerTest extends WebTestCase  {

    public function testCreatePlayer() {
        $client = static :: createClient();
        $client->request('POST', 'api/players');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testSuccess()
    {
        $client = static::createClient();
        $this->sendRequest($client, [
            'name' => 'Raúl',
            'surname' => 'Gonzalez Blanco',
            'salary' => 250000,
            'position' => 'DL'
        ]);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    private function sendRequest(KernelBrowser $client, array $json)
    {
        $client->request(
            'POST',
            '/api/players',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                //'HTTP_X-AUTH-TOKEN' => 'LALIGATOKEN'
            ],
            json_encode($json)
        );
    }
}
