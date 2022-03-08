<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PlayerControllerTest extends WebTestCase  {

    public function testCreatePlayerInvalidData()
    {
        $client = static::createClient();
        $this->sendRequest($client, ['name' => '', 'surname' => '', 'position' => '']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreatePlayerEmptyData()
    {
        $client = static::createClient();
        $this->sendRequest($client, []);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testSuccess()
    {
        $client = static::createClient();
        $this->sendRequest($client, [
            'name' => 'Aritz',
            'surname' => 'Aduriz',
            'position' => 'DL'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
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
            ],
            json_encode($json)
        );
    }
}
