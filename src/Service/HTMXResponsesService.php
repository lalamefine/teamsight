<?php namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class HTMXResponsesService
{
    public function __construct()
    { }

    public function pageRefresh(): Response
    {
        return new Response('', Response::HTTP_OK, ['HX-Refresh' => 'true']);
    }

    public function redirectTo(string $url): Response
    {
        return new Response('', Response::HTTP_OK, ['HX-Redirect' => $url]);
    }
}