<?php

namespace App\Repository;

 interface IinvitationRepository
{
    public function inviter($ids, $idr);
    public function accepter($ids, $idr);
    public function invitations($idu, $etat);


}


?>