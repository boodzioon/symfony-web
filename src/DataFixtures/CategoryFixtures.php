<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadMainCategories($manager);
        $this->loadElectronics($manager);
        $this->loadBooks($manager);
        $this->loadMovies($manager);
    }

    private function loadMainCategories(ObjectManager $manager)
    {
        foreach ($this->getMainCategoriesData() as [$name, $id]) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }
 
        $manager->flush();
    }

    private function loadElectronics(ObjectManager $manager)
    {
        $this->loadSubcategories($manager, 'Electronics', 1);
        $this->loadSubcategories($manager, 'Computers', 6);
        $this->loadSubcategories($manager, 'Laptops', 8);
    }

    private function loadBooks(ObjectManager $manager)
    {
        $this->loadSubcategories($manager, 'Books', 3);
    }

    private function loadMovies(ObjectManager $manager)
    {
        $this->loadSubcategories($manager, 'Movies', 4);
        $this->loadSubcategories($manager, 'Romance', 19);
    }

    private function loadSubcategories(ObjectManager $manager, $category, $parentId)
    {
        $parentCategory = $manager->getRepository(Category::class)->find($parentId);
        $methodName = "get{$category}Data";

        foreach ($this->$methodName() as [$name, $id]) {
            $category = new Category();
            $category->setName($name);
            $category->setParent($parentCategory);

            $manager->persist($category);
        }
 
        $manager->flush();
    }

    private function getMainCategoriesData(): array
    {
        return [
            ['Electronics', 1],
            ['Toys', 2],
            ['Books', 3],
            ['Movies', 4]
        ];
    }

    private function getElectronicsData(): array
    {
        return [
            ['Cameras', 5],
            ['Computers', 6],
            ['Cell Phones', 7]
        ];
    }

    private function getComputersData(): array
    {
        return [
            ['Laptops', 8],
            ['Desktops', 9],
            ['AIOs', 10]
        ];
    }

    private function getLaptopsData(): array
    {
        return [
            ['Apple', 11],
            ['Asus', 12],
            ['Dell', 13],
            ['Lenovo', 14],
            ['HP', 15]
        ];
    }

    private function getBooksData(): array
    {
        return [
            ['Children\'s books', 16],
            ['Kindle eBooks', 17]
        ];
    }

    private function getMoviesData(): array
    {
        return [
            ['Family', 18],
            ['Romance', 19]
        ];
    }

    private function getRomanceData(): array
    {
        return [
            ['Romantic Comedy', 20],
            ['Romantic Drama', 21]
        ];
    }
}
