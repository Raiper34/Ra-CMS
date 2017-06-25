<?php

namespace App\Model;

use Nette;

class CategoryModel extends BaseModel
{
    
    private $tableCategory = 'category';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) 
    {
        parent::__construct($database, $user);
    }
    
    public function getCategories()
    {
        return $this->database->table($this->tableCategory)->fetchAll();
    }
    
    public function createCategory($values)
    {
        $this->database->table($this->tableCategory)->insert($values);
    }
    
    public function getCategory($category_id)
    {
        return $this->database->table($this->tableCategory)->where('id', $category_id)->fetch();
    }
    
    public function deleteCategory($category_id)
    {
        $this->database->table($this->tableCategory)->where('id', $category_id)->delete();
    }
    
    public function editCategory($category_id, $values)
    {
        $this->database->table($this->tableCategory)->where('id', $category_id)->update($values);
    }
    
    public function getCategoriesForSelect()
    {
        $categories = array(NULL => '---');
        foreach($this->database->table($this->tableCategory)->fetchAll() as $category)
        {
            $categories[$category->id] = $category->name;
        }
        return $categories;
    }
    
    public function getMaxCategoryId()
    {
        return $this->database->table($this->tableCategory)->max('id');
    }
    
    public function urlExist($url)
    {
        if($this->database->table($this->tableCategory)->where('url', $url)->fetch())
        {
            return true;
        }
        return false;
    }
    
}
