<?php


abstract class ElementCommande
{
    protected int $id;
    protected string $nom;
    protected float $prix;

    public function __construct(int $id, string $nom, float $prix)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
    }

    // Méthode abstraite obligatoire pour les sous-classes
    abstract public function afficherDetails(): string;

    // Getters communs
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }
}


class Plat extends ElementCommande
{
    private string $type;
    private string $image;
    private array $ingredients;

    public function __construct(int $id, string $nom, float $prix, string $type, string $image, array $ingredients = [])
    {
        parent::__construct($id, $nom, $prix);
        $this->type = $type;
        $this->image = $image;
        $this->ingredients = $ingredients;
    }

    public function afficherDetails(): string
    {
        $ingredientList = !empty($this->ingredients) ? implode(', ', $this->ingredients) : 'Aucun ingrédient';
        return "{$this->nom} ({$this->type}) - {$this->prix} € : $ingredientList";
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}


class Formule extends ElementCommande
{
    private string $image;
    private array $plats;

    public function __construct(int $id, string $nom, float $prix, string $image, array $plats = [])
    {
        parent::__construct($id, $nom, $prix);
        $this->image = $image;
        $this->plats = $plats;
    }

    public function afficherDetails(): string
    {
        $platsList = !empty($this->plats) ? implode(', ', array_map(fn($plat) => $plat->getNom(), $this->plats)) : 'Aucun plat associé';
        return "{$this->nom} - {$this->prix} € : {$platsList}";
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getPlats(): array
    {
        return $this->plats;
    }

    // Méthode pour ajouter un plat à la formule
    public function ajouterPlat(Plat $plat): void
    {
        $this->plats[] = $plat;  // Ajoute un plat à la liste des plats
    }
}
