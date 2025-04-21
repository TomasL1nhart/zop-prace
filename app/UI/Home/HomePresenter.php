<?php
namespace App\UI\Home;

use Nette;
use App\Model\PostFacade;
use Nette\Application\UI\Presenter;
use Nette\Utils\Paginator;

final class HomePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {

	}
    public function renderDefault(int $page = 1): void
    {
        $title = $this->getParameter('title');
        $description = $this->getParameter('description');
        $category = $this->getParameter('category');
        $status = $this->getParameter('status');

        $query = $this->facade->getPublicArticles($this->getUser());

        if ($title) {
            $query->where('title LIKE ?', "%$title%");
        }
        if ($description) {
            $query->where('description LIKE ?', "%$description%");
        }
        if ($category) {
            $query->where('category_id = ?', $category);
        }
        if ($status) {
            $query->where('status = ?', $status);
        }

        // Počet článků celkem pro stránkování
        $articlesCount = $query->count('*');

        // Nastavení Paginatoru
        $paginator = new Paginator;
        $paginator->setItemCount($articlesCount); // Celkový počet článků
        $paginator->setItemsPerPage(4); // Počet článků na stránku
        $paginator->setPage($page); // Aktuální stránka

        // Načtení příspěvků pro aktuální stránku
        $this->template->posts = $query->limit($paginator->getLength(), $paginator->getOffset());
        $this->template->categories = $this->facade->getCategories();
        $this->template->paginator = $paginator;
    }

    public function renderCategory(int $id, int $page = 1): void
    {
        $category = $this->facade->getCategoryById($id);

        if (!$category) {
            $this->error('Kategorie nebyla nalezena'); // 404 error
        }

        $query = $this->facade->getPostsByCategory($id);
        $articlesCount = $query->count('*');

        $paginator = new Paginator;
        $paginator->setItemCount($articlesCount);
        $paginator->setItemsPerPage(10);
        $paginator->setPage($page);

        $this->template->category = $category;
        $this->template->posts = $query->limit($paginator->getLength(), $paginator->getOffset());
        $this->template->paginator = $paginator;
    }
}