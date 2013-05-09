<?php
/*********************************************************************
    profile.php

    Staff's profile handle

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

require_once('staff.inc.php');
$msg='';
$staff=Staff::lookup($thisstaff->getId());
if($_POST && $_POST['id']!=$thisstaff->getId()) { //Check dummy ID used on the form.
 $errors['err']='Erro interno, acesso negado';
} elseif(!$errors && $_POST) { //Handle post

    if(!$staff)
        $errors['err']='Usuário desconhecido ou inválido';
    elseif($staff->updateProfile($_POST,$errors)){
        $msg='Perfil atualizado com sucesso';
        $thisstaff->reload();
        $staff->reload();
        $_SESSION['TZ_OFFSET']=$thisstaff->getTZoffset();
        $_SESSION['TZ_DST']=$thisstaff->observeDaylight();
    }elseif(!$errors['err'])
        $errors['err']='Erro ao atualizar o perfil. Verifique os erros e tente novamente!';
}

//Forced password Change.
if($thisstaff->forcePasswdChange() && !$errors['err'])
    $errors['err']=sprintf('<b>Hi %s</b> - Você deve alterar sua senha para poder continuar!',$thisstaff->getFirstName());
elseif($thisstaff->onVacation() && !$warn)
    $warn=sprintf('<b>Welcome back %s</b>! Você está listado no sistema como \'de férias\' Por favor informe ao seu gerente a sua volta.',$thisstaff->getFirstName());

$inc='profile.inc.php';
$nav->setTabActive('dashboard');
require_once(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');
?>
