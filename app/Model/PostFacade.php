<?php
namespace App\Model;

use Nette\Database\Explorer;

class PostFacade
{
    public function __construct(private Explorer $database)
    {
    }

    /** POSTS **/
    public function getAll()
    {
        return $this->database->table('posts')
            ->where('status != ?', 'ARCHIVED')
            ->order('created_at DESC')
            ->fetchAll();
    }

    public function getPostById(int $id)
    {
        return $this->database->table('posts')->get($id);
    }

    public function addPost(array $data)
    {
        return $this->database->table('posts')->insert($data);
    }

    public function updatePost(int $id, array $data)
    {
        return $this->database->table('posts')->where('id', $id)->update($data);
    }

    public function deletePost(int $id)
    {
        $this->database->table('posts')->where('id', $id)->delete();
    }

    /** CATEGORIES **/
    public function getAllCategories()
    {
        return $this->database->table('categories')->fetchAll();
    }

    public function getCategoryById(int $id)
    {
        return $this->database->table('categories')->get($id);
    }

    /** IMAGES **/
    public function getImagesByPostId(int $postId)
    {
        return $this->database->table('images')
            ->where('post_id', $postId)
            ->fetchAll();
    }

    public function addImage(int $postId, string $filename)
    {
        $this->database->table('images')->insert([
            'post_id' => $postId,
            'filename' => $filename,
            'created_at' => new \DateTime()
        ]);
    }

    public function deleteImage(int $id)
    {
        $this->database->table('images')->where('id', $id)->delete();
    }

    /** COMMENTS **/
    public function getCommentsByPostId(int $postId)
    {
        return $this->database->table('comments')
            ->where('post_id', $postId)
            ->order('created_at ASC')
            ->fetchAll();
    }

    public function addComment(int $postId, int $userId, string $text)
    {
        $this->database->table('comments')->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'text' => $text,
            'created_at' => new \DateTime()
        ]);
    }

    public function deleteComment(int $id)
    {
        $this->database->table('comments')->where('id', $id)->delete();
    }
}
