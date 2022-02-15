<?php
declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Service\App;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class AppTest extends KernelTestCase
{
    public function testSendToken(): void
    {
        $container = self::getContainer();

        $mockResponseJson = json_encode(['generated' => true], JSON_THROW_ON_ERROR);
        $mockResponse = new MockResponse($mockResponseJson, [
           'http_code' => 201,
        ]);

        $client = new MockHttpClient($mockResponse, 'localhost:8000/token');
        $app = new App($client, $container->get(ParameterBagInterface::class));

        $responseData = $app->sendTokenId('test', 'test');

        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/token', $mockResponse->getRequestUrl());

        self::assertTrue($responseData['generated']);
    }
}