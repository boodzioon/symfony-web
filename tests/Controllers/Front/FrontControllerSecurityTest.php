<?php

namespace App\Tests\Controllers\Front;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSecurityTest extends WebTestCase
{
    
    #[DataProvider('getSecureUrls')]
    public function testSecureUrls(string $url): void
    {
        $client = $this->createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', $url);
        
        $this->assertStringContainsString('/login', $client->getRequest()->getRequestUri());
        $this->assertStringContainsString('Please sign in', $client->getResponse()->getContent());
    }

    public function testVideoForMembersOnly(): void
    {
        $client = $this->createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/video-list/category/movies,4');

        $this->assertStringContainsString('Video for MEMBERS only.', $crawler->filter('p.card-text.text-danger')->first()->text());
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $client->getResponse()->getContent());
    }

    public static function getSecureUrls(): \Generator
    {
        // yield ['/login'];
        yield ['/admin'];
        // yield ['/admin/videos'];
        // yield ['/admin/su/categories'];
        // yield ['/admin/su/delete_category/1'];
    }
}
