<?php
/*********************************************************************
    settings.php

    Handles all admin settings.
    
    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$errors=array();
$settingOptions=array(
                'system' => 'Configurações do sistema',
                'tickets' => 'Opções e configurações dos tickets',
                'emails' => 'Configurações de e-mail',
                'kb' => 'Configurações da base de conhecimento',
                'autoresp' => 'Configurações de auto-resposta',
                'alerts' => 'Configurações de alertas e informações',);
//Handle a POST.
if($_POST && !$errors) {
    if($cfg && $cfg->updateSettings($_POST,$errors)) {
        $msg=Format::htmlchars($settingOptions[$_POST['t']]).'Atualizado com sucesso';
        $cfg->reload();
    } elseif(!$errors['err']) {
        $errors['err']='Não foi possível atualizar as configurações - corrija o(s) erro(s) abaixo e tente novamente';
    }
}

$target=($_REQUEST['t'] && $settingOptions[$_REQUEST['t']])?$_REQUEST['t']:'system';
$config=($errors && $_POST)?Format::input($_POST):Format::htmlchars($cfg->getConfigInfo());

$nav->setTabActive('settings', ('settings.php?t='.$target));
require_once(STAFFINC_DIR.'header.inc.php');
include_once(STAFFINC_DIR."settings-$target.inc.php");
include_once(STAFFINC_DIR.'footer.inc.php');
?>
