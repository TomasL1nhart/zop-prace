<?php

namespace App\Model;

use Nette;
use Nette\Utils\Paginator;

final class PostFacade
{
    public function __construct(
        private Nette\Database\Explorer $database
    ) {}

    public function getPublicArticles(?Nette\Security\User $user = null)
    {
        $query = $this->database
            ->table('posts')
            ->where('created_at < ?', new \DateTime)
            ->order('created_at DESC');
    
        if (!$user || !$user->isLoggedIn()) {
            $query->where('status != ?', 'ARCHIVED');
        }
    
        return $query;
    }
    
    public function getPostById(int $postId, ?Nette\Security\User $user = null)
    {
        $post = $this->database->table('posts')->get($postId);

        if (!$post) {
            return null;
        }

        if ($post->status === 'ARCHIVED' && (!$user || !$user->isLoggedIn())) {
            return null;
        }

        return $post;
    }

    public function findAll(): array
    {
    return $this->database->table('posts')->order('created_at DESC')->fetchAll();
    }

    public function addComment(int $postId, int $userId, string $text): void
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'text' => $text,
            'created_at' => new \DateTime()
        ]);
    }
    
    public function addCategory(string $name): void
    {
    $this->database->table('categories')->insert([
        'name' => $name
    ]);
    }

    public function findAllCategories(): array
    {
    return $this->getCategories()->fetchAll();
    }

    public function getComments(int $postId): array
    {
        return $this->database->table('comments')
            ->where('post_id', $postId)
            ->order('created_at ASC')
            ->fetchAll();
    }
    
    public function getPaginatedArticles(
        ?Nette\Security\User $user,
        int $page,
        int $itemsPerPage,
        ?int $categoryId = null
    ): array {
        $paginator = new Paginator;
        $paginator->setPage(max($page, 1));
        $paginator->setItemsPerPage($itemsPerPage);
    
        $query = $this->database->table('posts');
    
        if (!$user || !$user->isLoggedIn()) {
            $query->where('status != ?', 'ARCHIVED');
        }
        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }
    
        $paginator->setItemCount($query->count('*'));
    
        $posts = $query
            ->order('created_at DESC')
            ->limit($paginator->getLength(), $paginator->getOffset())
            ->fetchAll();
    
        return [
            'paginator' => $paginator,
            'posts' => $posts,
        ];
    }    
    
    public function createPost(array $data)
    {
        return $this->database->table('posts')->insert($data);
    }

    public function updatePost(int $postId, array $data)
    {
        return $this->database->table('posts')
            ->where('id', $postId)
            ->update($data);
    }

        public function deletePost(int $postId): void
    {
        $post = $this->database->table('posts')->get($postId);
        if ($post) {
            $post->delete();
        }
    }

    public function deleteComment(int $commentId): void
    {
        $this->database->table('comments')->where('id', $commentId)->delete();
    }

    public function getCommentById(int $commentId): ?array
    {
        return $this->database->table('comments')->get($commentId)?->toArray();
    }

    public function getCategories(): \Nette\Database\Table\Selection
    {
        return $this->database->table('categories');
    }

    public function deleteCategory(int $categoryId): void
    {
    $category = $this->database->table('categories')->get($categoryId);
    if ($category) {
        $category->delete();
    }
    } 
    
}