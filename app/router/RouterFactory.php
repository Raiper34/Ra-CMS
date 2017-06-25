<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{
	use Nette\StaticClass;

        
        private $tableArticle = 'article';
        private $tableCategory = 'category';
        
	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter(Nette\Database\Context $database)
	{
		$router = new RouteList;
                //Articles/Pages
                foreach($database->table('article')->fetchAll() as $article)
                {
                    if($article->url != '')
                    {
                        $router[] = new Route($article->url, ['presenter' => 'Page', 'action' => 'article', 'article_id' => $article->id]);
                    }
                }
                //Categories/Pages
                foreach($database->table('category')->fetchAll() as $category)
                {
                    if($category->url != '')
                    {
                        $router[] = new Route($category->url, ['presenter' => 'Page', 'action' => 'category', 'category_id' => $category->id]);
                    }
                }
                $router[] = new Route('sitemap', ['presenter' => 'Page', 'action' => 'sitemap']);
                //Default page + administration
		$router[] = new Route('<presenter>/<action>', 'Page:default');
		return $router;
	}

}
