<?php
/*********************************************************************
    templates.php

    Email Templates

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.template.php');
$template=null;
if($_REQUEST['id'] && !($template=Template::lookup($_REQUEST['id'])))
    $errors['err']='Unknown or invalid template ID.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'updatetpl':
            if(!$template){
                $errors['err']='Template inválido ou desconhecido';
            }elseif($template->updateMsgTemplate($_POST,$errors)){
                $template->reload();
                $msg='Template atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar o template. Tente novamente!';
            }
            break;
        case 'update':
            if(!$template){
                $errors['err']='Template inválido ou desconhecido';
            }elseif($template->update($_POST,$errors)){
                $msg='Template atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar o template. Tente novamente!';
            }
            break;
        case 'add':
            if((Template::create($_POST,$errors))){
                $msg='Template adicionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível adicionar o template. Corrija o(s) erro(s) abaixo e tente novamente!';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err']='Você deve selecionar pelo menos um template.';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.EMAIL_TEMPLATE_TABLE.' SET isactive=1 '
                            .' WHERE tpl_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())){
                            if($num==$count)
                                $msg = 'Template selecionado habilitado';
                            else
                                $warn = "$num de $count templates selecionados habilitados";
                        } else {
                            $errors['err'] = 'Não foi possível habilitar o template selecioando';
                        }
                        break;
                    case 'disable':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=Template::lookup($v)) && !$t->isInUse() && $t->disable())
                                $i++;
                        }
                        if($i && $i==$count)
                            $msg = 'Templates selecionados desabilitados';
                        elseif($i)
                            $warn = "$i de $count templates selecionados desativada (templates em uso não pode ser desativado)";
                        else
                            $errors['err'] = "Não foi possível desativar templates selecionados (em uso ou template padrão não pode ser desativado)";
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=Template::lookup($v)) && !$t->isInUse() && $t->delete())
                                $i++;
                        }

                        if($i && $i==$count)
                            $msg = 'Templates selecionados atualizados com sucesso';
                        elseif($i>0)
                            $warn = "$i de$count templates selecionados deletados";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível deletarv os templates selecionados';
                        break;
                    default:
                        $errors['err']='Ação desconhecida';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
            break;
    }
}

$page='templates.inc.php';
if($template && !strcasecmp($_REQUEST['a'],'manage')){
    $page='tpl.inc.php';
}elseif($template || !strcasecmp($_REQUEST['a'],'add')){
    $page='template.inc.php';
}

$nav->setTabActive('emails');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
