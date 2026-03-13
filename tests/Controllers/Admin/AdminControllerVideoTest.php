<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Video;
use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerVideoTest extends WebTestCase
{

    use RoleAdmin;

    public function testDeleteVideo(): void
    {
        $video = $this->em->getRepository(Video::class)->find(1);
        $this->assertSame(1, $video->getId());

        $crawler = $this->client->request('GET', '/admin/su/delete-video/1/137857207');

        $video = $this->em->getRepository(Video::class)->find(1);
        $this->assertNull($video);
    }
}
