<?php
/*********************************************************************
    staff.php

    Evertything about staff members.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$staff=null;
if($_REQUEST['id'] && !($staff=Staff::lookup($_REQUEST['id'])))
    $errors['err']='ID do funcionário desconhecido ou inválido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$staff){
                $errors['err']='Funcionário inválido o desconhecido.';
            }elseif($staff->update($_POST,$errors)){
                $msg='Funcionãrio atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível atualizar o funcionário. Corrija o(s) erro(s) abaixo e tente novamnente!';
            }
            break;
        case 'create':
            if(($id=Staff::create($_POST,$errors))){
                $msg=Format::htmlchars($_POST['name']).' atualizado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível atualizar o funcionário. Corrija o(s) erro(s) abaixo e tente novamnente!';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'You must select at least one staff member.';
            } elseif(in_array($thisstaff->getId(),$_POST['ids'])) {
                $errors['err'] = 'Você não pode desativar/excluir-se - você pode ser o único administrador!';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.STAFF_TABLE.' SET isactive=1 '
                            .' WHERE staff_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Funcionário ativado selecionado';
                            else
                                $warn = "$num de $count funcionárioa ativados selecionados";
                        } else {
                            $errors['err'] = 'Não foi possível atualizar o funcionário selecionado';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.STAFF_TABLE.' SET isactive=0 '
                            .' WHERE staff_id IN ('.implode(',', db_input($_POST['ids'])).') AND staff_id!='.db_input($thisstaff->getId());

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Funcionário selecionado desativado.';
                            else
                                $warn = "$num de $count funcionários selecionados desativados.";
                        } else {
                            $errors['err'] = 'Não foi possível desabilitar o funcionário selecionado.';
                        }
                        break;
                    case 'delete':
                        foreach($_POST['ids'] as $k=>$v) {
                            if($v!=$thisstaff->getId() && ($s=Staff::lookup($v)) && $s->delete())
                                $i++;
                        }

                        if($i && $i==$count)
                            $msg = 'Funcionário selecionado desativado com sucesso.';
                        elseif($i>0)
                            $warn = "$i de $count funcionários selecionados desativados.";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível deletar o funcionário selecionado.';
                        break;
                    default:
                        $errors['err'] = 'Ação desconhecida. Peça ajuda técnica!';
                }
                    
            }
            break;
        default:
            $errors['err']='Ação/Comando desconhecido';
            break;
    }
}

$page='staffmembers.inc.php';
if($staff || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='staff.inc.php';

$nav->setTabActive('staff');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
