<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CoachControllerTest extends WebTestCase  {

    public function testCreatePlayerInvalidData()
    {
        $client = static::createClient();
        $this->sendRequest($client, ['name' => '', 'surname' => '']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreateCoachEmptyData()
    {
        $client = static::createClient();
        $this->sendRequest($client, []);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testSuccess()
    {
        $client = static::createClient();
        $this->sendRequest($client, [
            'name' => 'Marcelino',
            'surname' => 'García Toral'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    private function sendRequest(KernelBrowser $client, array $json)
    {
        $client->request(
            'POST',
            '/api/coaches',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($json)
        );
    }
}
