<?php

namespace App\Infrastructure\Search;

interface IndexerInterface
{
    /**
     * Indexe un contenu dans le système de recherche.
     *
     * @param array $data {id: string, title: string, content: string, created_at: int, category: string[], pict: string}
     */
    public function index(array $data): void;

    /**
     * ajoute un contenu dans le système de recherche.
     *
     * @param array $data {id: string, title: string, content: string, created_at: int, category: string[], pict: string}
     */
    public function indexOne(array $data): void;

    /**
     * Supprime un contenu de l'index.
     */
    public function remove(string $id): void;

    /**
     * Vide l'index de toute données.
     */
    public function clean(): void;
}
