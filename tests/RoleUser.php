<?php

namespace App\Tests;

trait RoleUser
{

    private $client;
    private $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'bn@symf8.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
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