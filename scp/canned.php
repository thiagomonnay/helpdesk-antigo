<?php
/*********************************************************************
    canned.php

    Canned Responses aka Premade Responses.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
include_once(INCLUDE_DIR.'class.canned.php');
/* check permission */
if(!$thisstaff || !$thisstaff->canManageCannedResponses()) {
    header('Location: kb.php');
    exit;
}

//TODO: Support attachments!

$canned=null;
if($_REQUEST['id'] && !($canned=Canned::lookup($_REQUEST['id'])))
    $errors['err']='ID da mensagem pré-definida desconhecido.';

if($_POST && $thisstaff->canManageCannedResponses()) {
    switch(strtolower($_POST['do'])) {
        case 'update':
            if(!$canned) {
                $errors['err']='Resposta pré-definida inválida ou desconhecida.';
            } elseif($canned->update($_POST, $errors)) {
                $msg='Resposta pré-definida atualizada com sucesso.';
                //Delete removed attachments.
                //XXX: files[] shouldn't be changed under any circumstances.
                $keepers = $_POST['files']?$_POST['files']:array();
                $attachments = $canned->getAttachments(); //current list of attachments.
                foreach($attachments as $k=>$file) {
                    if($file['id'] && !in_array($file['id'], $keepers)) {
                        $canned->deleteAttachment($file['id']);
                    }
                }
                //Upload NEW attachments IF ANY - TODO: validate attachment types??
                if($_FILES['attachments'] && ($files=AttachmentFile::format($_FILES['attachments'])))
                    $canned->uploadAttachments($files);

                $canned->reload();

            } elseif(!$errors['err']) {
                $errors['err']='Erro ao atualizar a respsta pré-definida. Tente novamente!';
            }
            break;
        case 'create':
            if(($id=Canned::create($_POST, $errors))) {
                $msg='Resposta pré-definida atualizada com sucesso';
                $_REQUEST['a']=null;
                //Upload attachments
                if($_FILES['attachments'] && ($c=Canned::lookup($id)) && ($files=AttachmentFile::format($_FILES['attachments'])))
                    $c->uploadAttachments($files);

            } elseif(!$errors['err']) {
                $errors['err']='Não foi possível atualizar a reposta pré-definida. Corrija o(s) erro(s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err']='Você deve selecionar pelo menos uma resposta pré-definida';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.CANNED_TABLE.' SET isenabled=1 '
                            .' WHERE canned_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'As respostas pré-definidas selecionadas, foram habilitadas ';
                            else
                                $warn = "$num de $count respostas pré-definidas selecionadas habilitadas";
                        } else {
                            $errors['err'] = 'Não foi possível habilitar as respostas selecionadas.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.CANNED_TABLE.' SET isenabled=0 '
                            .' WHERE canned_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'As respostas pré-definidas selecionadas, foram desabilitadas';
                            else
                                $warn = "$num de $count respostas pré-definidas selecionadas desabilitadas";
                        } else {
                            $errors['err'] = 'Não foi possível desabilitar as respostas selecionadas';
                        }
                        break;
                    case 'delete':

                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($c=Canned::lookup($v)) && $c->delete())
                                $i++;
                        }

                        if($i==$count)
                            $msg = 'As respostas pré-definidas selecionadas, foram deletadas com sucesso';
                        elseif($i>0)
                            $warn="$i de $count repostas pré-definidas foram deletadas";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível deletar as tarefas pré-definidas selecionadas';
                        break;
                    default:
                        $errors['err']='Comando desconhecido';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
            break;
    }
}

$page='cannedresponses.inc.php';
if($canned || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='cannedresponse.inc.php';

$nav->setTabActive('kbase');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
