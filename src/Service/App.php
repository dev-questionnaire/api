<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class App
{
    private string $appUrl;
    private const depth = 512;

    public function __construct(
        private HttpClientInterface $httpClient,
        ParameterBagInterface       $parameterBag,
    ) {
        /** @var string $url */
        $url = $parameterBag->get('app.url');
        $this->appUrl = $url;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function sendTokenId(string $tokenId, string $token): array
    {
        $url = $this->appUrl . '/token';


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

        /** @var array<array-key, mixed> $arrayRet */
        $arrayRet = json_decode($content, true, self::depth, JSON_THROW_ON_ERROR);
        return $arrayRet;
    }
}
