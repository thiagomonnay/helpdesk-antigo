<?php
/*********************************************************************
    banlist.php

    List of banned email addresses

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.banlist.php');

/* Get the system ban list filter */
if(!($filter=Banlist::getFilter())) 
    $warn = 'Lista de proibição do sistema está vazia.';
elseif(!$filter->isActive())
    $warn = 'Sistema de filtro de lista de proibição está <b>DESABILITADO</b> - <a href="filters.php">habilite aqui!</a>.'; 
 
$rule=null; //ban rule obj.
if($filter && $_REQUEST['id'] && !($rule=$filter->getRule($_REQUEST['id'])))
    $errors['err'] = 'Lista de proibição do sistema desconhecida ou inválida#';

if($_POST && !$errors && $filter){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$rule){
                $errors['err']='Regra de proibição inválida ou desconhecida.';
            }elseif(!$_POST['val'] || !Validator::is_email($_POST['val'])){
                $errors['err']=$errors['val']='Um e-mail válido é requerido';
            }elseif(!$errors){
                $vars=array('w'=>'email',
                            'h'=>'equal',
                            'v'=>$_POST['val'],
                            'filter_id'=>$filter->getId(),
                            'isactive'=>$_POST['isactive'],
                            'notes'=>$_POST['notes']);
                if($rule->update($vars,$errors)){
                    $msg='E-mail atualizado com sucesso';
                }elseif(!$errors['err']){
                    $errors['err']='Erro ao atualizar regra, tente novamente!';
                }
            }
            break;
        case 'add':
            if(!$filter) {
                $errors['err']='Lista de proibição desconhecida ou inválida';
            }elseif(!$_POST['val'] || !Validator::is_email($_POST['val'])) {
                $errors['err']=$errors['val']='Um e-mail válido é requerido';
            }elseif(BanList::includes($_POST['val'])) {
                $errors['err']=$errors['val']='E-mail enviado já está na lista de proibição';
            }elseif($filter->addRule('email','equal',$_POST['val'],array('isactive'=>$_POST['isactive'],'notes'=>$_POST['notes']))) {
                $msg='E-mail enviado adicionado com sucesso na lista de proibição';
                $_REQUEST['a']=null;
                //Add filter rule here.
            }elseif(!$errors['err']){
                $errors['err']='Erro ao criar a regra, tente novamente!';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um e-mail para processar.';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.FILTER_RULE_TABLE.' SET isactive=1 '
                            .' WHERE filter_id='.db_input($filter->getId())
                            .' AND id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())){
                            if($num==$count)
                                $msg = 'E-mail foi habilitado  na lista de proibição';
                            else
                                $warn = "$num of $count foram selecionados para lista de proibição";
                        } else  {
                            $errors['err'] = 'Não foi possível habilitar o e-mail na lista de proibição';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.FILTER_RULE_TABLE.' SET isactive=0 '
                            .' WHERE filter_id='.db_input($filter->getId())
                            .' AND id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'O status do e-mail foi definido como desativado';
                            else
                                $warn = "$num of $count e-mail foram definidos como desativado";
                        } else {
                            $errors['err'] = 'Impossível desativar e-mails selecionados';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($r=FilterRule::lookup($v)) && $r->getFilterId()==$filter->getId() && $r->delete())
                                $i++;
                        }
                        if($i && $i==$count)
                            $msg = 'O e-mail selecionado foi deletado com sucesso';
                        elseif($i>0)
                            $warn = "$i of $count e-mail foram deletados da lista de proibição";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Impossível deletar e-mails selecionados';
                    
                        break;
                    default:
                        $errors['err'] = 'Ação desconhecida - peça ajuda técnica';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
            break;
    }
}

$page='banlist.inc.php';
if(!$filter || ($rule || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add'))))
    $page='banrule.inc.php';

$nav->setTabActive('emails');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
