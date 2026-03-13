<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTranslationTest extends WebTestCase
{

    use RoleUser;

    public function testTranslations(): void
    {
        $this->client->request('GET', '/admin/');
        $this->assertStringContainsString('My profile', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('/admin/videos', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/pl/admin/');
        $this->assertStringContainsString('Mój profil', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('/pl/admin/lista-wideo', $this->client->getResponse()->getContent());
    }
}
