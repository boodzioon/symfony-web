<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\User;
use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerUserAccountTest extends WebTestCase
{
    use RoleUser;

    public function testUserDeletedAccount()
    {
        $crawler = $this->client->request('GET', '/admin/');

        $link = $crawler->filter('a:contains("delete account")')->link();
        $this->client->click($link);

        $deletedUser = $this->em->getRepository(User::class)->find(3);
        $this->assertNull($deletedUser);
    }

    public function testUserChangedData()
    {
        $crawler = $this->client->request('GET', '/admin/');

        $form = $crawler->selectButton('Save')->form([
            'user[name]' => 'name',
            'user[last_name]' => 'last_name',
            'user[email]' => 'email@email.email',
            'user[password][first]' => '12345',
            'user[password][second]' => '12345',
        ]);
        $this->client->submit($form);

        $user = $this->em->getRepository(User::class)->find(3);
        $this->assertSame('name', $user->getName());
        $this->assertSame('last_name', $user->getLastName());
        $this->assertSame('email@email.email', $user->getEmail());
    }
}
