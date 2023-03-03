<?php

namespace App\Security\Voter;

use App\Entity\Tramite;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TramiteVoter extends Voter
{
    private $security;
    const LOT_EDIT = 'LOT_EDIT';
    const LOT_NEW = 'LOT_NEW';
    const LOT_RENEW = 'LOT_RENEW';
    const LOT_APROV = 'LOT_APROV';
    const LOT_PRINT = 'LOT_PRINT';
    const LOT_ENTREGA = 'LOT_ENTREGA';
    const LOT_DUPLICADO = 'LOT_DUPLICADO';
    const LOT_SUSPEND = 'LOT_SUSPEND';
    const LOT_FINSUSPEND = 'LOT_FINSUSPEND';
    const LOT_CANCEL = 'LOT_CANCEL';
    const LOT_DELETE = 'LOT_DELETE';
    const PJ_EDIT = 'PJ_EDIT';
    const PJ_ADD = 'PJ_ADD';
    const PJ_DELETE = 'PJ_DELETE';
    const BASIF_ADD = 'BASIF_ADD';
    const BASIF_EDIT = 'BASIF_EDIT';
    const BASIF_NEW = 'BASIF_NEW';
    const BASIF_EDITMEDIOS = 'BASIF_EDITMEDIOS';
    const MEDIO_ADD = 'MEDIO_ADD';
    const MEDIO_EDIT = 'MEDIO_EDIT';
    const MEDIO_DELETE = 'MEDIO_DELETE';
    const MEDIO_ADDCOMP = 'MEDIO_ADDCOMP';
    const MEDIO_DELCOMP ='MEDIO_DELCOMP';
    const COMP_ADD = 'COMP_ADD';
    const COMP_EDIT = 'COMP_EDIT';
    const COMP_DELETE = 'COMP_DELETE';
    const COMP_PRINT = 'COMP_PRINT';
    const INST_ADD = 'INST_ADD';
    const INST_EDIT = 'INST_EDIT';
    const INST_DELETE = 'INST_DELETE';
    const INST_ADDCOMP = 'INST_ADDCOMP';
    const INST_REMOVECOMP = 'INST_REMOVECOMP';


    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports($attribute, $subject)
    {

        return in_array($attribute, [
                self::LOT_EDIT,
                self::LOT_NEW,
                self::LOT_RENEW,
                self::LOT_APROV,
                self::LOT_PRINT,
                self::LOT_ENTREGA,
                self::LOT_DUPLICADO,
                self::LOT_SUSPEND,
                self::LOT_FINSUSPEND,
                self::LOT_CANCEL,
                self::LOT_DELETE,
                self::PJ_EDIT,
                self::PJ_ADD,
                self::PJ_DELETE,
                self::BASIF_ADD,
                self::BASIF_EDIT,
                self::BASIF_NEW,
                self::BASIF_EDITMEDIOS,
                self::MEDIO_ADD,
                self::MEDIO_EDIT,
                self::MEDIO_DELETE,
                self::MEDIO_ADDCOMP,
                self::MEDIO_DELCOMP,
                self::COMP_ADD,
                self::COMP_DELETE,
                self::COMP_EDIT,
                self::COMP_PRINT,
                self::INST_ADD,
                self::INST_EDIT,
                self::INST_DELETE,
                self::INST_ADDCOMP,
                self::INST_REMOVECOMP,
            ])
            && $subject instanceof Tramite;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        $tr = $subject;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::LOT_EDIT :
                return $this->LOT_EDIT($tr,$user);
                break;
            case  self::LOT_NEW :
                return $this->LOT_NEW($tr,$user);
                break;
            case self::LOT_RENEW :
                return $this->LOT_NEW($tr,$user);
                break;
            case self::LOT_APROV :
                return $this->LOT_APROV($tr,$user);
                break;
            case self::LOT_PRINT :
                return $this->LOT_PRINT($tr,$user);
                break;
            case self::LOT_ENTREGA :
                return $this->LOT_PRINT($tr,$user);
                break;
            case self::LOT_DUPLICADO :
                return $this->LOT_PRINT($tr,$user);
                break;
            case self::LOT_DELETE :
                return $this->LOT_DELETE($tr,$user);
                break;

            case self::PJ_EDIT :
                return $this->PJ_EDIT($tr,$user);
                break;
            case self::PJ_DELETE :
                return $this->LOT_DELETE($tr,$user);
                break;
        }

        return false;
    }
    private function LOT_EDIT(Tramite $tr, User $user)
    {
       if($tr->getLot()->getIdentidad()->getIdmunicipio()->getProvinciaId()->getId()!== $user->getMunicipio()->getProvinciaId()->getId())
        return false;
       return true;
    }
    private function LOT_NEW(Tramite $tr,User $user){
        if($tr->getLot()->getIdentidad()->getIdmunicipio()->getProvinciaId()->getId()!== $user->getMunicipio()->getProvinciaId()->getId())
            return false;
        return true;
    }
    private function LOT_APROV(Tramite $tr,User $user){
        if($tr->getLot()){
            if($tr->getLot()->getIdaprueba()){
                if($tr->getLot()->getIdaprueba()->getId()== $user->getId())
                    return true;
            }
        }
        return false;
    }
    private function LOT_PRINT(Tramite $tr, User $user){
        if($tr->getLot()->getIdentidad()->getIdmunicipio()->getProvinciaId()->getId()!== $user->getMunicipio()->getProvinciaId()->getId())
            return false;
        return true;
    }
    private function PJ_EDIT(Tramite $tr, User $user){
        if($tr->getPj()->getIdmunicipio()->getProvinciaId()->getId()!== $user->getMunicipio()->getProvinciaId()->getId())
            return false;
        return true;
    }

    private function LOT_DELETE(Tramite $tr, User $user){
        if($tr->getLot()->getIdentidad()->getIdmunicipio()->getProvinciaId()->getId()!== $user->getMunicipio()->getProvinciaId()->getId() )
            return false;
        return true;
    }

}
