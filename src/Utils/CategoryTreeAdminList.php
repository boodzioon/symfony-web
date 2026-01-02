<?php


namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{

    public $html_1 = '<ul class="fa-ul text-left">';
    public $html_2 = '<li><i class="fa-li fa fa-arrow-right"></i>';
    public $html_3 = ' <a href="';
    public $html_4 = '">';
    public $html_5 = '</a> <a onclick="return confirm(\'Are you sure?\');" href="';
    public $html_6 = '">';
    public $html_7 = '</a>';
    public $html_8 = '</li>';
    public $html_9 = '</ul>';

    public function getCategoryList(array $categoriesArray): string
    {
        $this->categoryList .= $this->html_1;

        foreach ($categoriesArray as $value) {
            $this->categoryList .= $this->html_2;
            $catName = $value['name'];
            $catId = $value['id'];
            $urlEdit = $this->urlGenerator->generate('admin_edit_category', ['id' => $catId]);
            $urlDelete = $this->urlGenerator->generate('admin_delete_category', ['id' => $catId]);

            $this->categoryList .= $catName . $this->html_3 . $urlEdit . $this->html_4  . 'Edytuj' . $this->html_5. $urlDelete . $this->html_6 . 'Usuń' . $this->html_7;
            if (isset($value['children'])) {
                $this->getCategoryList($value['children']);
            }
            $this->categoryList .= $this->html_8;   
        }
        $this->categoryList .= $this->html_9;

        return $this->categoryList;
    }
}
