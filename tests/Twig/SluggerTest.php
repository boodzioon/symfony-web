<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\Runtime\AppExtensionRuntime as AppExtension;
use PHPUnit\Framework\Attributes\DataProvider;

class SluggerTest extends TestCase
{

    #[DataProvider('getSlugs')]
    public function testSlugify(string $string, string $slug): void
    {
        $slugger = new AppExtension;

        $this->assertSame($slug, $slugger->slugify($string));
    }

    // public static function getSlugs(): array
    // {
    //     return [
    //         ['Cell Phones', 'cell-phones'],
    //         ['Lorem Ipsum', 'lorem-ipsum'],
    //         ['  Lorem  Ipsum  ', 'lorem-ipsum'],
    //     ];
    // }

    public static function getSlugs(): \Generator
    {
        yield ['Lorem Ipsum', 'lorem-ipsum'];
        yield ['  Lorem  Ipsum  ', 'lorem-ipsum'];
        yield ['lOrEm iPsUm', 'lorem-ipsum'];
        yield ['!Lorem Ipsum!', 'lorem-ipsum'];
        yield ['lorem-ipsum', 'lorem-ipsum'];
        yield ['Children\'s books', 'childrens-books'];
        yield ['Five star movies', 'five-star-movies'];
        yield ['Adults 60+', 'adults-60'];
        yield ['Cell Phones', 'cell-phones'];
    }
}
