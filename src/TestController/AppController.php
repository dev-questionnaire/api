<?php

declare(strict_types=1);

namespace App\TestController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Controller For Testing
class AppController extends AbstractController
{
    #[Route('/token', name: 'app_token', methods: 'POST')]
    public function token(Request $request): JsonResponse
    {
        $requestContent = $request->getContent();
        $content = (array)json_decode($requestContent, true, 512, JSON_THROW_ON_ERROR);

        if (str_contains($content['tokenId'], 'negativTest@cec.valantic.com')) {
            return $this->json([
                'generated' => false,
            ]);
        }

        return $this->json([
            'generated' => true,
        ]);
    }
}
