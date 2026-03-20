<?php

namespace App\Utils;

use App\Utils\Interfaces\CacheInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\DoctrineDbalAdapter;

class SqliteCache implements CacheInterface
{

    public $cache;
    public function __construct()
    {
        $connection = \Doctrine\DBAL\DriverManager::getConnection([
            'path' => __DIR__ . '/../../var/cache.db',
            'driver' => 'sqlite3'
        ]);
        dump($connection);
        $this->cache =  new TagAwareAdapter(
            new DoctrineDbalAdapter($connection)
        );
    }
}
