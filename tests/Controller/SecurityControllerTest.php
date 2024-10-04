<?php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageRendersSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testLoginPageWithError(): void
    {
        $client = static::createClient();
        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils
            ->method('getLastAuthenticationError')
            ->willReturn(new AuthenticationException('Invalid credentials.'));

        $controller = new SecurityController();
        $response = $controller->login($authenticationUtils);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSelectorExists('.alert-danger');
    }

    public function testLogoutRedirectsToLoginPage(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request('GET', '/logout');
        $response = $client->getResponse();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString($urlGenerator->generate('app_login'), $response->getTargetUrl());
    }
}
