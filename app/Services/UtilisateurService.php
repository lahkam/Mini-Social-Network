<?php

namespace App\Services;

use App\Repository\IinvitationRepository;
use App\Repository\IUtilisateurRepository;
use App\Services\IUtilisateurService;

class UtilisateurService implements IUtilisateurService
{
    protected $utilisateurRepository;
    protected $invitationRepository;

    public function __construct(IUtilisateurRepository $utilisateurRepository, IinvitationRepository $invitationRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->invitationRepository=$invitationRepository;
    }

    public function getAllUtilisateurs()
    {
        return $this->utilisateurRepository->getAllUtilisateurs();
    }

    public function getUtilisateurById($id)
    {
        return $this->utilisateurRepository->getUtilisateurById($id);
    }

    public function createUtilisateur(array $data)
    {
        // You can add validation logic here if needed

        
        return $this->utilisateurRepository->createUtilisateur($data);
    }

    public function updateUtilisateur($id, array $data)
    {
        return $this->utilisateurRepository->updateUtilisateur($id, $data);
    }

    public function inviter($ids,$idr)
	{
        //verifier est ce que il y a deja une invitation entre les 2
            $this->invitationRepository->inviter($ids,$idr);
    }
    public function accepter($ids,$idr)
	{}

    public function invitations($idu,$etat)
	{}
}
?>