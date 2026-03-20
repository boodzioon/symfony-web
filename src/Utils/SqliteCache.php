<?php

namespace App\Utils;

use App\Kernel;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\DoctrineDbalAdapter;

class SqliteCache implements CacheInterface
{

    public $cache;

    public function __construct(Kernel $kernel)
    {
        $connection = \Doctrine\DBAL\DriverManager::getConnection([
            'path' => $kernel->getProjectDir() . '/var/db/cache_'. $kernel->getEnvironment() . '.db',
            'driver' => 'sqlite3'
        ]);

        $this->cache =  new TagAwareAdapter(
            new DoctrineDbalAdapter($connection)
        );
    }
}
