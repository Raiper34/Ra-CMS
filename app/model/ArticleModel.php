<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

class ArticleModel extends BaseModel
{
    
    private $tableArticle = 'article';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) 
    {
        parent::__construct($database, $user);
    }
    
    public function createNewArticle($values)
    {
        $values->user_id = $this->user->id;
        if(!$values->date)
        {
            $values->date = date("Y-m-d");
        }
        if(!$values->time)
        {
            $values->time = date("H:i:s");
        }
        $values->date = "{$values->date} {$values->time}:00";
        $values->category_id = ($values->category_id == '')?  NULL : $values->category_id;
        unset($values->time);
        return $this->database->table($this->tableArticle)->insert($values);
    }
    
    public function editArticle($article_id, $values)
    {
        if(isset($values->category_id))
        {
            $values->category_id = ($values->category_id == '') ? NULL : $values->category_id;
        }
        if(!$values->date)
        {
            $values->date = date("Y-m-d");
        }
        if(!$values->time)
        {
            $values->time = date("H:i:s");
        }
        $values->date = "{$values->date} {$values->time}:00";
        unset($values->time);
        $this->database->table($this->tableArticle)->where('id', $article_id)->update($values);
    }

    public function publishToggleArticle($article_id)
    {
        $this->database->table($this->tableArticle)->where('id', $article_id)->update(array('published' => !$this->getArticle($article_id)->published));
    }
    
    public function deleteArticle($article_id)
    {
        $this->database->table($this->tableArticle)->where('id', $article_id)->delete();
    }
    
    public function getArticles()
    {
        return $this->database->table($this->tableArticle)->where('user_id', $this->user->id)->fetchAll();
    }
    
    public function getArticlesOnPage($articlesPerPage, $page)
    {
        return $this->database->table($this->tableArticle)->where('user_id', $this->user->id)->limit($articlesPerPage, $articlesPerPage * $page)->fetchAll();
    }
    
    public function getArticlesCount()
    {
        return $this->database->table($this->tableArticle)->count('*');
    }
    
    public function getArticle($article_id)
    {
        return $this->database->table($this->tableArticle)->where('id', $article_id)->fetch();
    }
    
    public function getArticlesForSelect()
    {
        $articles = array();
        foreach($this->database->table($this->tableArticle)->fetchAll() as $article)
        {
            $articles[$article->id] = $article->title;
        }
        return $articles;
    }
    
    public function getCategoryArticles($category_id, $page = 0, $itemsPerPage = 10)
    {
        return $this->database->table($this->tableArticle)
            ->where('category_id', $category_id)
            ->where('published', true)
            ->where('date < NOW()')
            ->limit($itemsPerPage, $itemsPerPage * $page)
            ->fetchAll();
    }

    public function getPublishedArticles()
    {
        return $this->database->table($this->tableArticle)
            ->where('published', true)
            ->where('date < NOW()')
            ->fetchAll();
    }
    
    public function getcategoryArticlesCount($category_id)
    {
        return $this->database->table($this->tableArticle)->where('category_id', $category_id)->count();
    }
    
    public function getMaxArticleId()
    {
        return $this->database->table($this->tableArticle)->max('id');
    }
    
    public function urlExist($url)
    {
        if($this->database->table($this->tableArticle)->where('url', $url)->fetch())
        {
            return true;
        }
        return false;
    }
}
