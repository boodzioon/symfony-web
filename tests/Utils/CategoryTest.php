<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Twig\Runtime\AppExtensionRuntime as AppExtension;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;

#[AllowMockObjectsWithoutExpectations]
class CategoryTest extends KernelTestCase
{

    protected $mockedCategoryTreeAdminList;
    protected $mockedCategoryTreeAdminOptionList;
    protected $mockedCategoryTreeFrontPage;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator = $kernel->getContainer()->get('router');
        $testedClasses = ['CategoryTreeAdminList', 'CategoryTreeAdminOptionList', 'CategoryTreeFrontPage'];

        foreach ($testedClasses as $class) {
            $name = 'mocked' . $class;
            $classNS = 'App\Utils\\' . $class;

            // $reflection = new \ReflectionClass($classNS);
            // $this->$name = $reflection->newInstanceWithoutConstructor();

            $this->$name = $this->getMockBuilder('App\Utils\\' . $class)
                ->disableOriginalConstructor()
                ->onlyMethods([])
                ->getMock();
            $this->$name->urlGenerator = $urlGenerator;
        }
    }

    #[DataProvider('dataForCategoryTreeAdminList')]
    public function testCategoryTreeAdminList($string, $array): void
    {
        $this->mockedCategoryTreeAdminList->categoriesArrayFromDb = $array;

        $array = $this->mockedCategoryTreeAdminList->buildTree();
        $this->assertSame($string, $this->mockedCategoryTreeAdminList->getCategoryList($array));
    }

    public static function dataForCategoryTreeAdminList(): \Generator
    {
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>Games <a href="/admin/edit-category/23">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/23">Usuń</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>Board Games <a href="/admin/edit-category/24">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/24">Usuń</a></li><li><i class="fa-li fa fa-arrow-right"></i>Computer Games <a href="/admin/edit-category/25">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/25">Usuń</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>RPG <a href="/admin/edit-category/29">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/29">Usuń</a></li><li><i class="fa-li fa fa-arrow-right"></i>Sport Games <a href="/admin/edit-category/32">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/32">Usuń</a></li><li><i class="fa-li fa fa-arrow-right"></i>Strategy <a href="/admin/edit-category/28">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/28">Usuń</a><ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>RTS <a href="/admin/edit-category/30">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/30">Usuń</a></li><li><i class="fa-li fa fa-arrow-right"></i>Turn-Based <a href="/admin/edit-category/31">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/31">Usuń</a></li></ul></li></ul></li></ul></li></ul>',
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ]
        ];
        yield [
            '<ul class="fa-ul text-left"><li><i class="fa-li fa fa-arrow-right"></i>Toys <a href="/admin/edit-category/2">Edytuj</a> <a onclick="return confirm(\'Are you sure?\');" href="/admin/delete-category/2">Usuń</a></li></ul>',
            [
                ['id' => 2, 'name' => 'Toys', 'parent_id' => null],
            ]
        ];
    }

    #[DataProvider('dataForCategoryTreeAdminOptionList')]
    public function testCategoryTreeAdminOptionList($arrayToCompare, $arrayFromDb): void
    {
        $this->mockedCategoryTreeAdminOptionList->categoriesArrayFromDb = $arrayFromDb;

        $array = $this->mockedCategoryTreeAdminOptionList->buildTree();
        $this->assertSame($arrayToCompare, $this->mockedCategoryTreeAdminOptionList->getCategoryList($array));
    }

    public static function dataForCategoryTreeAdminOptionList(): \Generator
    {
        yield [
            [
                ['name' => 'Games', 'id' => 23],
                ['name' => '--Board Games', 'id' => 24],
                ['name' => '--Computer Games', 'id' => 25],
                ['name' => '----RPG', 'id' => 29],
                ['name' => '----Sport Games', 'id' => 32],
                ['name' => '----Strategy', 'id' => 28],
                ['name' => '------RTS', 'id' => 30],
                ['name' => '------Turn-Based', 'id' => 31],
            ],
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ]
        ];
        yield [
            [
                ['name' => 'Toys', 'id' => 2],
            ],
            [
                ['id' => 2, 'name' => 'Toys', 'parent_id' => null],
            ]
        ];
    }

    #[DataProvider('dataForCategoryTreeFrontPage')]
    public function testCategoryTreeFrontPage($string, $array, ?int $id): void
    {
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDb = $array;
        $this->mockedCategoryTreeFrontPage->slugger = new AppExtension;
        $mainParentId = $this->mockedCategoryTreeFrontPage->getMainParent($id)['id'];

        $array = $this->mockedCategoryTreeFrontPage->buildTree($mainParentId);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }

    public static function dataForCategoryTreeFrontPage(): \Generator
    {
        yield [
            '<ul><li><a href="/video-list/category/board-games,24">Board Games</a></li><li><a href="/video-list/category/computer-games,25">Computer Games</a><ul><li><a href="/video-list/category/rpg,29">RPG</a></li><li><a href="/video-list/category/sport-games,32">Sport Games</a></li><li><a href="/video-list/category/strategy,28">Strategy</a><ul><li><a href="/video-list/category/rts,30">RTS</a></li><li><a href="/video-list/category/turn-based,31">Turn-Based</a></li></ul></li></ul></li></ul>',
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ],
            23
        ];
        yield [
            '<ul><li><a href="/video-list/category/board-games,24">Board Games</a></li><li><a href="/video-list/category/computer-games,25">Computer Games</a><ul><li><a href="/video-list/category/rpg,29">RPG</a></li><li><a href="/video-list/category/sport-games,32">Sport Games</a></li><li><a href="/video-list/category/strategy,28">Strategy</a><ul><li><a href="/video-list/category/rts,30">RTS</a></li><li><a href="/video-list/category/turn-based,31">Turn-Based</a></li></ul></li></ul></li></ul>',
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ],
            29
        ];
        yield [
            '<ul><li><a href="/video-list/category/board-games,24">Board Games</a></li><li><a href="/video-list/category/computer-games,25">Computer Games</a><ul><li><a href="/video-list/category/rpg,29">RPG</a></li><li><a href="/video-list/category/sport-games,32">Sport Games</a></li><li><a href="/video-list/category/strategy,28">Strategy</a><ul><li><a href="/video-list/category/rts,30">RTS</a></li><li><a href="/video-list/category/turn-based,31">Turn-Based</a></li></ul></li></ul></li></ul>',
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ],
            31
        ];
        yield [
            '<ul><li><a href="/video-list/category/board-games,24">Board Games</a></li><li><a href="/video-list/category/computer-games,25">Computer Games</a><ul><li><a href="/video-list/category/rpg,29">RPG</a></li><li><a href="/video-list/category/sport-games,32">Sport Games</a></li><li><a href="/video-list/category/strategy,28">Strategy</a><ul><li><a href="/video-list/category/rts,30">RTS</a></li><li><a href="/video-list/category/turn-based,31">Turn-Based</a></li></ul></li></ul></li></ul>',
            [
                ['id' => 23, 'name' => 'Games', 'parent_id' => null],
                ['id' => 24, 'name' => 'Board Games', 'parent_id' => 23],
                ['id' => 25, 'name' => 'Computer Games', 'parent_id' => 23],
                ['id' => 29, 'name' => 'RPG', 'parent_id' => 25],
                ['id' => 32, 'name' => 'Sport Games', 'parent_id' => 25],
                ['id' => 28, 'name' => 'Strategy', 'parent_id' => 25],
                ['id' => 30, 'name' => 'RTS', 'parent_id' => 28],
                ['id' => 31, 'name' => 'Turn-Based', 'parent_id' => 28],
            ],
            32
        ];
    }
}

