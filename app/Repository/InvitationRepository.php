<?php

namespace App\Repository;

use App\Models\Invitation;
use App\Repository\IinvitationRepository;
use Illuminate\Support\Facades\Date;

 class InvitationRepository implements IinvitationRepository
{
	public function inviter($ids,$idr)
	{
		Invitation::create([
            'etat'=>0,
            'date'=> new Date(),
            "inviter_id"=>$ids,
            "invitee_id"=>$idr

        ]);
	}

	public function accepter($ids,$idr)
	{
		
       $invit= Invitation::where(['inviter_id'=>$ids, 'invitee_id'=>$idr])->first();
       $invit->etat=1;
       $invit->refresh();
	}

	public function invitations($idu,$etat)
	{
		// TODO: Implement invitations() method.

       $invits= Invitation::where(['etat'=>$etat,'invitee_id'=>$idu]);
       return $invits;
	}
}


?>