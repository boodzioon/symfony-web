<?php

namespace App\Tests;

trait GuestUser
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
}