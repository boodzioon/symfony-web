<?php


namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminOptionList extends CategoryTreeAbstract
{
    public function getCategoryList(array $categoriesArray, $prefix = ''): array
    {
        foreach ($categoriesArray as $value) {
            $catName = $value['name'];
            $catId = $value['id'];

            $this->categoryList[] = ['name' => $prefix . $catName, 'id' => $catId];
            if (isset($value['children'])) {
                $this->getCategoryList($value['children'], $prefix . '--');
            }
        }

        return $this->categoryList;
    }
}
