<?php
/*********************************************************************
    filters.php

    Email Filters

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.filter.php');
require_once(INCLUDE_DIR.'class.canned.php');
$filter=null;
if($_REQUEST['id'] && !($filter=Filter::lookup($_REQUEST['id'])))
    $errors['err']='Unknown or invalid filter.';

/* NOTE: Banlist has its own interface*/
if($filter && $filter->isSystemBanlist())
    header('Location: banlist.php');

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$filter){
                $errors['err']='Filtro desconhecido ou inválido.';
            }elseif($filter->update($_POST,$errors)){
                $msg='Filtro atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro na atualzação do filtro. Tente novamente!';
            }
            break;
        case 'add':
            if((Filter::create($_POST,$errors))){
                $msg='Filtro adicionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível adicionar o filtro. Corrija o(s) erro(s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um filtro para processar.';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.FILTER_TABLE.' SET isactive=1 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Filtros selecionados habilitado';
                            else
                                $warn = "$num de $count filtros selecionados habilitado";
                        } else {
                            $errors['err'] = 'Não foi possível permitir os filtros selecionados';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.FILTER_TABLE.' SET isactive=0 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Filtros selecionados desativado';
                            else
                                $warn = "$num de $count filtros selecionados desativado";
                        } else {
                            $errors['err'] = 'Não foiu possível desativar os filtros selecionados';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($f=Filter::lookup($v)) && !$f->isSystemBanlist() && $f->delete())
                                $i++;
                        }
                        
                        if($i && $i==$count)
                            $msg = 'Filtros selecionados excluído com sucesso';
                        elseif($i>0)
                            $warn = "$i de $count filtros selecionados excluídos";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não é possível excluir filtros selecionados';
                        break;
                    default:
                        $errors['err']='Ação deconhecida - peça ajuda ao suporte técnico';
                }
            }
            break;
        default:
            $errors['err']='Ação/comando desconhecido.';
            break;
    }
}

$page='filters.inc.php';
if($filter || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='filter.inc.php';

$nav->setTabActive('manage');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
