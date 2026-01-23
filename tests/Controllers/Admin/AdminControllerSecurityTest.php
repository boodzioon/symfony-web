<?php

namespace App\Tests\Controllers\Front;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerSecurityTest extends WebTestCase
{

    #[DataProvider('getUrlsForRegularUsers')]
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $client = $this->createClient([], [
            'PHP_AUTH_USER' => 'bn@symf8.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $crawler = $client->request($httpMethod, $url);

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testAdminSu(): void
    {
        $client = $this->createClient([], [
            'PHP_AUTH_USER' => 'jw@symf8.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $crawler = $client->request('GET', '/admin/su/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
    }

    public static function getUrlsForRegularUsers(): \Generator
    {
        yield ['GET', '/admin/su/categories'];
        yield ['GET', '/admin/su/edit-category/1'];
        yield ['GET', '/admin/su/delete-category/2'];
        yield ['GET', '/admin/su/users'];
        yield ['GET', '/admin/su/upload-video'];
    }
}
