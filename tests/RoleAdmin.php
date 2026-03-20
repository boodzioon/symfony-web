<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;

trait RoleAdmin
{

    private $client;
    private ?EntityManagerInterface $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'jw@symf8.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $this->client->disableReboot();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);

        $cache = $this->client->getContainer()->get('App\Utils\Interfaces\CacheInterface');
        $this->cache = $cache->cache;
        $this->cache->clear();
    }

    public function tearDown(): void
    {
        $this->cache->clear();
        $this->em->rollBack();
        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }
}