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
        /** @var string $url */
        $url = $parameterBag->get('app.url');
        $this->appUrl = $url;
    }

    #[Route('/', name: 'api_redirect', methods: 'GET')]
    public function redirectToApp(): Response
    {
        //Redirect to App
        return $this->redirect($this->appUrl);
    }
}
