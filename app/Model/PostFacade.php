<?php

namespace App\Model;

use Nette;

final class PostFacade
{
    public function __construct(
        private Nette\Database\Explorer $database
    ) {}

    public function getPublicArticles(?Nette\Security\User $user = null)
    {
        $query = $this->database
            ->table('posts')
            ->where('created_at < ', new \DateTime)
            ->order('created_at DESC');
    
        if (!$user || !$user->isLoggedIn()) {
            $query->where('status != ?', 'ARCHIVED');
        }
    
        return $query;
    }

    public function getPostById(int $postId, ?Nette\Security\User $user = null)
    {
        $post = $this->database
            ->table('posts')
            ->get($postId);
    
        if (!$post) {
            return null;
        }
    
        if ($post->status === 'ARCHIVED' && (!$user || !$user->isLoggedIn())) {
            return null; 
        }
    
        return $post;
    }    

    public function getComments(int $postId)
    {
        return $this->database
            ->table('comments')
            ->where('post_id', $postId)
            ->order('created_at ASC');
    }

    public function addView(int $postId): void
    {
    $this->database->table('posts')
        ->where('id', $postId)
        ->update([
            'views_count' => new Nette\Database\SqlLiteral('views_count + 1')
        ]);
    }

    public function addComment(int $postId, string $name, ?string $email, string $content): void
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'name' => $name,
            'email' => $email,
            'content' => $content,
            'created_at' => new \DateTime()
        ]);
    }

    public function createPost(array $data)
    {
        if (!isset($data['category_id'])) {
            throw new \InvalidArgumentException('Kategorie je povinná.');
        }
        return $this->database->table('posts')->insert($data);
    }

    public function updatePost(int $postId, array $data)
    {
        isset($data['category_id']);
        $this->database->table("posts")->where("id", $postId)->update($data);
        return $postId;
    }

    public function getCommentById(int $commentId): ?array
    {
        return $this->database->table('comments')
            ->get($commentId)
            ?->toArray();
    }

    public function deleteComment(int $commentId): void
    {
        $this->database->table('comments')
            ->where('id', $commentId)
            ->delete();
    }

    public function getCategories()
    {
        return $this->database->table('categories')->fetchAll();
    }

    public function getCategoryById(int $categoryId)
    {
        return $this->database->table('categories')->get($categoryId);
    }

    public function getPostsByCategory(int $categoryId)
    {
        return $this->database->table('posts')
            ->where('category_id', $categoryId)
            ->order('created_at DESC');
    }
    public function findPublishedPosts(int $limit, int $offset)
    {
        return $this->database->table('posts')
            ->where('created_at < ?', new \DateTime)
            ->order('created_at DESC')
            ->limit($limit, $offset);
    }

    public function getPublishedPostsCount(): int
    {
        return $this->database->table('posts')
            ->where('created_at < ?', new \DateTime)
            ->count('*');
    }
}
