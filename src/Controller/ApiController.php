<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class ApiController extends AbstractController
{
    private string $appUrl;

    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $url = $parameterBag->get('app.url');

        if (!is_string($url)) {
            throw new \Exception('url musst be string');
        }

        $this->appUrl = $url;
    }

    #[Route('/', name: 'api_redirect')]
    public function redirectToApp(): Response
    {
        //Redirect to App
        return $this->redirect($this->appUrl);
    }
}
