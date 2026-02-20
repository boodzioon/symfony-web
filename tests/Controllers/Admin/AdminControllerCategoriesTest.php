<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Category;
use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{

    use RoleAdmin;

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertCount(22, $crawler->filter('option'));
    }

    public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

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
        $crawler = $this->client->request('GET', '/admin/su/edit-category/1');

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
        $crawler = $this->client->request('GET', '/admin/su/delete-category/1');

        $category = $this->em->getRepository(Category::class)->find(1);
        $this->assertNull($category);
    }
}
