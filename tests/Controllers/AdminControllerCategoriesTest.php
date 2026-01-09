<?php

namespace App\Tests\Controllers;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{

    private $client;
    private $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        $this->em->rollBack();
        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $this->assertCount(22, $crawler->filter('option'));
    }

    public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'RTV'
        ]);
        $this->client->submit($form);

        $category = $this->em->getRepository(Category::class)->findOneBy(['name' => 'RTV']);
        $this->assertNotNull($category);
        $this->assertSame('RTV', $category->getName());
    }

    public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/edit-category/1');

        $form = $crawler->selectButton('Save')->form([
            'category[name]' => 'Electronics and RTV'
        ]);
        $this->client->submit($form);

        $category = $this->em->getRepository(Category::class)->find(1);
        $this->assertNotNull($category);
        $this->assertNotSame('Electronics', $category->getName());
        $this->assertSame('Electronics and RTV', $category->getName());
    }

    public function testDeleteCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/delete-category/1');

        $category = $this->em->getRepository(Category::class)->find(1);
        $this->assertNull($category);
    }
}
