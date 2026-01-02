<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{

    public string $categoryList = '';
    public array $categoriesArrayFromDb;
    protected static $dbConnection;

    protected EntityManagerInterface $em;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->categoriesArrayFromDb = $this->getCategories();
    }

    abstract public function getCategoryList(array $categoriesArray);

    public function buildTree(int $parentId = null): array
    {
        $subcategory = [];

        foreach ($this->categoriesArrayFromDb as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $subcategory[] = $category;
            }
        }

        return $subcategory;
    }

    private function getCategories(): array
    {
        if (self::$dbConnection) {
            return self::$dbConnection;
        } else {
            $conn = $this->em->getConnection();
            $sql = "SELECT * FROM categories";
            $result = $conn->executeQuery($sql);

            return self::$dbConnection = $result->fetchAllAssociative();
        }
    }
}