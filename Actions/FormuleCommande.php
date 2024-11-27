<?php

abstract class AbstractFormule
{
    public $id;
    public $nom;
    public $type;
    public $isComplet;

    public function __construct($id, $nom, $type, $isComplet)
    {
        $this->isComplet = $isComplet;
        $this->id = $id;
        $this->nom = $nom;
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'type' => $this->type,
            'uniqueNum' => $this->isComplet
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIsComplet()
    {
        return $this->isComplet;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getType()
    {
        return $this->type;
    }
}

class FormuleCommande extends AbstractFormule
{
    public $plat;
    public $dessert;
    public $boisson;
    public $choixIngredients;

    public function __construct($id, $nom, $type, $isComplet, $plat = null, $dessert = null, $boisson = null, $choixIngredients = [])
    {
        parent::__construct($id, $nom, $type, $isComplet);
        $this->plat = $plat;
        $this->dessert = $dessert;
        $this->boisson = $boisson;
        $this->choixIngredients = $choixIngredients;
    }

    public function toArray()
    {
        $base = parent::toArray();
        $base['plat'] = $this->plat;
        $base['dessert'] = $this->dessert;
        $base['boisson'] = $this->boisson;
        $base['choixIngredients'] = $this->choixIngredients;
        return $base;
    }

    public function addIngredient($ingredient)
    {
        $this->choixIngredients[] = $ingredient;
    }

    public function removeIngredient($ingredient)
    {
        $index = array_search($ingredient, $this->choixIngredients);
        if ($index !== false) {
            unset($this->choixIngredients[$index]);
        }
    }

    // Getters supplémentaires
    public function getPlat()
    {
        return $this->plat;
    }

    public function getDessert()
    {
        return $this->dessert;
    }

    public function getBoisson()
    {
        return $this->boisson;
    }

    public function getChoixIngredients()
    {
        return $this->choixIngredients;
    }

    // Méthode pour ajouter un plat, dessert, ou boisson
    public function setPlat($plat)
    {
        $this->plat = $plat;
    }

    public function setDessert($dessert)
    {
        $this->dessert = $dessert;
    }

    public function setBoisson($boisson)
    {
        $this->boisson = $boisson;
    }
}
