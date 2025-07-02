<?php

use App\Models\Utilisateur;
use App\Repository\IUtilisateurRepository;

class UtilisateurRepository implements IUtilisateurRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Utilisateur();
    }

    public function getAllUtilisateurs()
    {
        return $this->model->all();
    }

    public function getUtilisateurById($id)
    {
        return $this->model->find($id);
    }

    public function createUtilisateur(array $data)
    {
        return $this->model->create($data);
    }

    public function updateUtilisateur($id, array $data)
    {
        $utilisateur = $this->getUtilisateurById($id);
        if ($utilisateur) {
            $utilisateur->update($data);
            return $utilisateur;
        }
        return null;
    }
}
?>