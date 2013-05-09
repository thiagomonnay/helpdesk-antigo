<?php
/*********************************************************************
    slas.php

    SLA - Service Level Agreements

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.sla.php');

$sla=null;
if($_REQUEST['id'] && !($sla=SLA::lookup($_REQUEST['id'])))
    $errors['err']='API key ID inválida ou desconhecida.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$sla){
                $errors['err']='Plano ce SLA inválida ou desconhecida.';
            }elseif($sla->update($_POST,$errors)){
                $msg='Plano ce SLA atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar o plano de SLA. Tente novamente!';
            }
            break;
        case 'add':
            if(($id=SLA::create($_POST,$errors))){
                $msg='Plano de SLA adicionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não é possível adicionar plano de SLA. Corrigir erro (s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um plano de SLA.';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.SLA_TABLE.' SET isactive=1 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                    
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Plano de SLA selecionado habilitado';
                            else
                                $warn = "$num de $count Plano de SLA selecionado habilitado";
                        } else {
                            $errors['err'] = 'Não foi possível habilitar o plano de SLA selecionado.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.SLA_TABLE.' SET isactive=0 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Plano de SLA selecionado desabilitado';
                            else
                                $warn = "$num de $count Plano de SLA selecionado desabilitado";
                        } else {
                            $errors['err'] = 'Não foi possível desabilitar o plano de SLA selecionado';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($p=SLA::lookup($v)) && $p->delete())
                                $i++;
                        }

                        if($i && $i==$count)
                            $msg = 'Plano de SLA selecionado deletado com sucesso';
                        elseif($i>0)
                            $warn = "$i de $count plano de SLA selecionado deletado";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível detelar o plano de SLA selecionado';
                        break;
                    default:
                        $errors['err']='Ação desconhecida. Peça ajuda técnica';
                }
            }
            break;
        default:
            $errors['err']='Ação/Comando desconhecido';
            break;
    }
}

$page='slaplans.inc.php';
if($sla || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='slaplan.inc.php';

$nav->setTabActive('manage');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
