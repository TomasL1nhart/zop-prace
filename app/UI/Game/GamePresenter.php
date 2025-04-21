<?php

namespace App\UI\Game;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

class GamePresenter extends Presenter
{
    /** @var GameFacade @inject */
    public $gameFacade;

    public function renderDefault(): void
    {
        $this->template->games = $this->gameFacade->getAllGames(); 
        $this->template->genres = $this->gameFacade->getAllGenres();
    }
    

    public function renderShow(int $id): void
    {
        $game = $this->gameFacade->getGameById($id);
        if (!$game) {
            $this->error('Game not found.');
        }
    
        $genres = $this->gameFacade->getAllGenres();
    
        $this->template->game = $game;
        $this->template->genres = $genres;
    }    

    public function createComponentAddGameForm(): Form
    {
        $form = new Form;
        
        $form->addText('name', 'Game Name:')
            ->setRequired('Please enter the game name.');
        $form->addTextArea('description', 'Description:')
            ->setRequired('Please enter the description.');
        
        $genres = $this->gameFacade->getAllGenres();
        $genreOptions = [];
        foreach ($genres as $genre) {
            $genreOptions[$genre->id] = $genre->name;
        }
        $form->addSelect('genre_id', 'Genre:', $genreOptions)
            ->setRequired('Please select a genre.');
    
        $form->addSubmit('submit', 'Add Game');
        $form->onSuccess[] = [$this, 'processAddGameForm'];
    
        return $form;
    }
    
    public function processAddGameForm(Form $form, \stdClass $values): void
    {
        $this->gameFacade->addGame($values->name, $values->description, $values->genre_id);
        $this->flashMessage('Game added successfully.', 'success');
        $this->redirect('this');
    }

    public function renderEdit(int $id): void
    {
        $game = $this->gameFacade->getGameById($id);
        if (!$game) {
            $this->error('Game not found.');
        }

        $genres = $this->gameFacade->getAllGenres();
    
        $this->template->game = $game;
        $this->template->genres = $genres;
    }

    protected function createComponentEditGameForm(): Form
    {
        $form = new Form;
    
        $gameId = $this->getParameter('id');
        if (!$gameId) {
            throw new \Exception('Missing game ID');
        }
    
        $game = $this->gameFacade->getGameById($gameId);
        if (!$game) {
            throw new \Exception('Game not found');
        }
    
        $genres = $this->gameFacade->getAllGenres();
        $genreOptions = [];
        foreach ($genres as $genre) {
            $genreOptions[$genre->id] = $genre->name; 
        }
    
        $form->addText('name', 'Name:')
            ->setRequired('Please enter the game name.')
            ->setDefaultValue($game->name);
    
        $form->addTextArea('description', 'Description:')
            ->setRequired('Please enter the description.')
            ->setDefaultValue($game->description);
    
        $form->addSelect('genre_id', 'Genre:', $genreOptions)
            ->setRequired('Please select a genre')
            ->setDefaultValue($game->genre_id); 
    
        $form->addSubmit('submit', 'Update');
        $form->onSuccess[] = [$this, 'processEditGameForm'];
    
        return $form;
    }    

    public function processEditGameForm(Form $form, \stdClass $values): void
    {
        $gameId = $this->getParameter('id'); 
        if (!$gameId) {
            throw new \Exception('Missing game ID');
        }
    
        $this->gameFacade->updateGame($gameId, $values->name, $values->description, $values->genre_id);
    
        $this->flashMessage('Game updated successfully.', 'success');
        $this->redirect('Game:default');
    }

    public function handleDeleteGame(int $gameId): void
    {
        $this->gameFacade->delete($gameId);
        $this->flashMessage("Hra byla úspěšně smazána!");
        $this->redirect("Game:default");
    }

    public function renderGenre(int $id): void
    {
        $games = $this->gameFacade->getGamesByGenre($id);
        $genre = $this->gameFacade->getGenreById($id);
        $genres = $this->gameFacade->getAllGenres();
    
        if (!$genre) {
            $this->error('Genre not found.');
        }
    
        $this->template->games = $games;
        $this->template->genre = $genre;
        $this->template->genres = $genres; 
    }
}