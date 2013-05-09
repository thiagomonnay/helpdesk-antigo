<?php
/*********************************************************************
    groups.php

    User Groups.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$group=null;
if($_REQUEST['id'] && !($group=Group::lookup($_REQUEST['id'])))
    $errors['err']='ID do grupo desconhecido ou inválido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$group){
                $errors['err']='Grupo desconhecido ou inválido.';
            }elseif($group->update($_POST,$errors)){
                $msg='Grupo atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível atualizar o grupo. Corrigir qualquer erro (s) abaixo e tente novamente!';
            }
            break;
        case 'create':
            if(($id=Group::create($_POST,$errors))){
                $msg=Format::htmlchars($_POST['name']).' adicionada com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não é possível adicionar grupo. Corrigir erro (s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um grupo.';
            } elseif(in_array($thisstaff->getGroupId(), $_POST['ids'])) {
                $errors['err'] = "Como administrador, você não pode desativar / excluir um grupo a que pertence - que você pode bloqueio de todos os administradores! ";
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.GROUP_TABLE.' SET group_enabled=1, updated=NOW() '
                            .' WHERE group_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())){
                            if($num==$count)
                                $msg = 'Os grupos selecionados ativado';
                            else
                                $warn = "$num de $count grupos selecionados ativado";
                        } else {
                            $errors['err'] = 'Não é possível ativar grupos selecionados';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.GROUP_TABLE.' SET group_enabled=0, updated=NOW() '
                            .' WHERE group_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Os grupos selecionados desativado';
                            else
                                $warn = "$num de $count grupos selecionados desativado";
                        } else {
                            $errors['err'] = 'Não foi possível desativar grupos selecionados';
                        }
                        break;
                    case 'delete':
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($g=Group::lookup($v)) && $g->delete())
                                $i++;
                        }   

                        if($i && $i==$count)
                            $msg = 'Grupos selecionados excluído com sucesso';
                        elseif($i>0)
                            $warn = "$i de $count grupos selecionados excluído com sucesso";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não é possível excluir os grupos selecionados';
                        break;
                    default:
                        $errors['err']  = 'Ação desconhecida. Obter ajuda técnica!';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
            break;
    }
}

$page='groups.inc.php';
if($group || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='group.inc.php';

$nav->setTabActive('staff');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
