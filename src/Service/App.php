<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class App
{
    private string $appUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
        ParameterBagInterface       $parameterBag,
    ) {
        $url = $parameterBag->get('app.url');

        if (!is_string($url)) {
            throw new \Exception('url musst be string');
        }

        $this->appUrl = $url;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function sendTokenId(string $tokenId, string $token): array
    {
        $url = $this->appUrl . '/token';

        try {
            $response = $this->httpClient->request(
                'POST',
                $url,
                [
                    'json' => [
                        'token' => $token,
                        'tokenId' => $tokenId,
                    ],
                ]
            );

            $content = $response->getContent();
        } catch (\Exception $exception) {
            return ['message' => $exception];
        }

        return (array)json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
