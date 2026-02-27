<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\User;
use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerUsersTest extends WebTestCase
{

    use RoleAdmin;

    public function testSomething(): void
    {
        $user = $this->em->getRepository(User::class)->find(4);
        $this->assertNotNull($user);

        $this->client->request('GET', '/admin/su/delete-user/4');

        $deletedUser = $this->em->getRepository(User::class)->find(4);
        $this->assertNull($deletedUser);
    }
}
