<?php
/*********************************************************************
    emails.php

    Emails

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.email.php');

$email=null;
if($_REQUEST['id'] && !($email=Email::lookup($_REQUEST['id'])))
    $errors['err']='ID do e-mail inválido ou desconhecido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$email){
                $errors['err']='E-mail inválido ou desconhecido.';
            }elseif($email->update($_POST,$errors)){
                $msg='Email updated successfully';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar e-mail. Tente novamente!';
            }
            break;
        case 'create':
            if(($id=Email::create($_POST,$errors))){
                $msg='E-mail adixcionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível adicionar o e-mail. Corrija o(s) erro(s) abaixo e tente novamente';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um e-mail';
            } else {
                $count=count($_POST['ids']);

                $sql='SELECT count(dept_id) FROM '.DEPT_TABLE.' dept '
                    .' WHERE email_id IN ('.implode(',', db_input($_POST['ids'])).') '
                    .' OR autoresp_email_id IN ('.implode(',', db_input($_POST['ids'])).')';

                list($depts)=db_fetch_row(db_query($sql));
                if($depts>0) {
                    $errors['err'] = 'Um ou mais e-mails selecionados já estão em uso. Remova a associação primeiro!';
                } elseif(!strcasecmp($_POST['a'], 'delete')) {
                    $i=0;
                    foreach($_POST['ids'] as $k=>$v) {
                        if($v!=$cfg->getDefaultEmailId() && ($e=Email::lookup($v)) && $e->delete())
                            $i++;
                    }

                    if($i && $i==$count)
                        $msg = 'Os e-mails selecionados foram deletados com sucesso';
                    elseif($i>0)
                        $warn = "$i de $count e-mails selecionados, forma deletados";
                    elseif(!$errors['err'])
                        $errors['err'] = 'Não foi possível deletetar os e-mails selecionados';
                    
                } else {
                    $errors['err'] = 'Ação desconhecido - peça ajuda técnica';
                }
            }
            break;
        default:
            $errors['err'] = 'Ação/comando desconhecido';
            break;
    }
}

$page='emails.inc.php';
if($email || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='email.inc.php';

$nav->setTabActive('emails');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
