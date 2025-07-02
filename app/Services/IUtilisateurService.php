<?php
namespace App\Services;


interface IUtilisateurService
{
    public function getAllUtilisateurs();
    public function getUtilisateurById($id);
    public function createUtilisateur(array $data);
    public function updateUtilisateur($id, array $data);


     public function inviter($ids, $idr);
    public function accepter($ids, $idr);
    public function invitations($idu, $etat);
    
}

?>