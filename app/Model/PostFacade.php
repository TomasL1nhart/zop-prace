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

    public function addComment(int $postId, int $userId, string $text): void
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'text' => $text,
            'created_at' => new \DateTime()
        ]);
    }
    
    
    public function getComments(int $postId): array
    {
        return $this->database->table('comments')
            ->where('post_id', $postId)
            ->order('created_at ASC')
            ->fetchAll();
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
    
    
}
