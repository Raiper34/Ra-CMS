<?php

namespace App\Model;

use Nette;

class PageModel extends BaseModel
{
    
    private $tablePage = 'page';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) 
    {
        parent::__construct($database, $user);
    }

    
    public function getPages()
    {
        return $this->database->table($this->tablePage)->fetchAll();
    }
    
    public function getPagesForMenu()
    {
        return $this->database->query('SELECT page.id, page.name, article.url as url, page.page_order as page_order '
                . 'FROM page JOIN article ON page.article_id = article.id '
                . 'UNION  '
                . 'SELECT page.id, page.name, category.url as url, page.page_order as page_order '
                . 'FROM page JOIN category ON page.category_id = category.id '
                . 'ORDER BY page_order')->fetchAll();
    }
    
    
    public function createPage($values)
    {
        $values->page_order = $this->getMaxOrder() + 1;
        $this->database->table($this->tablePage)->insert($values);
    }
    
    public function editPage($page_id, $values)
    {
        $this->database->table($this->tablePage)->where('id', $page_id)->update($values);
    }
    
    public function getPage($page_id)
    {
        return $this->database->table($this->tablePage)->where('id', $page_id)->fetch();
    }

    
    public function deletePage($page_id)
    {
        $this->database->table($this->tablePage)->where('id', $page_id)->delete();
    }
    
    private function getMaxOrder()
    {
        return $this->database->table($this->tablePage)->max('page_order');
    }
}
