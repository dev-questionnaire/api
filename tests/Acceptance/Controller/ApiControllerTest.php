<?php
declare(strict_types=1);

namespace App\Tests\Acceptance\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ApiControllerTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected string $apiUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();

        $this->apiUrl = self::getContainer()->get(ParameterBagInterface::class)->get('api.url');
    }

    public function testRedirect(): void
    {
        $this->client->request('GET', $this->apiUrl . '/');

        self::assertInstanceOf(RedirectResponse::class, $this->client->getResponse());
    }
}