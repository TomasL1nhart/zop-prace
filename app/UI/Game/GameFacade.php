<?php

namespace App\UI\Game; 

use Nette\Database\Explorer;

class GameFacade
{
    /** @var Explorer */
    private $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function getGameById(int $id)
    {
        return $this->database->table('games')
            ->where('id', $id)
            ->fetch();
    }

    public function getAllGames()
    {
        return $this->database->query('
            SELECT games.*, genres.name AS genre_name
            FROM games
            LEFT JOIN genres ON games.genre_id = genres.id
        ')->fetchAll();
    }
    
    public function addGame(string $name, string $description, int $genre_id): void
    {
        $this->database->table('games')->insert([
            'name' => $name,
            'description' => $description,
            'genre_id' => $genre_id,  // Použití cizího klíče
        ]);
    }

    public function updateGame(int $id, string $name, string $description, int $genre_id): void
    {
        $this->database->table('games')
            ->where('id', $id)
            ->update([
                'name' => $name,
                'description' => $description,
                'genre_id' => $genre_id, 
            ]);
    }

    public function delete(int $id): void
    {
        $this->database->table('games')->get($id)->delete();
    }

    public function getAllGenres()
    {
        return $this->database->table('genres')->fetchAll();
    }

    public function getGamesByGenre(int $genreId)
    {
        return $this->database->table('games')
            ->where('genre_id', $genreId)
            ->fetchAll();
    }

    public function getGenreById(int $id)
    {
        return $this->database->table('genres')
            ->where('id', $id)
            ->fetch();
    }
}
