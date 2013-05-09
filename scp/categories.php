<?php
/*********************************************************************
    categories.php

    FAQ categories

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
include_once(INCLUDE_DIR.'class.category.php');

/* check permission */
if(!$thisstaff || !$thisstaff->canManageFAQ()) {
    header('Location: kb.php');
    exit;
}


$category=null;
if($_REQUEST['id'] && !($category=Category::lookup($_REQUEST['id'])))
    $errors['err']='ID da categoria inválido ou desconhecido';

if($_POST){
    switch(strtolower($_POST['do'])) {
        case 'update':
            if(!$category) {
                $errors['err']='Categoria inválida ou desconhecida.';
            } elseif($category->update($_POST,$errors)) {
                $msg='Categoria atualizada com sucesso';
            } elseif(!$errors['err']) {
                $errors['err']='Erro ao atualizar a categoria. Tente novamente!';
            }
            break;
        case 'create':
            if(($id=Category::create($_POST,$errors))) {
                $msg='Categoria adicionada com sucesso';
                $_REQUEST['a']=null;
            } elseif(!$errors['err']) {
                $errors['err']='Não foi possível adicionar a categoria. Corrija os erros e tente novamente';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err']='Você deve selecionar pelo menos uma categoria';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'make_public':
                        $sql='UPDATE '.FAQ_CATEGORY_TABLE.' SET ispublic=1 '
                            .' WHERE category_id IN ('.implode(',', db_input($_POST['ids'])).')';
                    
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Categorias selecionadas, foram transformadas em pública';
                            else
                                $warn = "$num of $count selecionadas foram transformadas em pública";
                        } else {
                            $errors['err'] = 'Não foi possível habilitar as categorias selecionadas para pública.';
                        }
                        break;
                    case 'make_private':
                        $sql='UPDATE '.FAQ_CATEGORY_TABLE.' SET ispublic=0 '
                            .' WHERE category_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Categorias selecionadas, foram transformadas em privada';
                            else
                                $warn = "$num of $count selecionadas foram transformadas em privada";
                        } else {
                            $errors['err'] = 'Não foi possível habilitar as categorias selecionadas para privada.';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($c=Category::lookup($v)) && $c->delete())
                                $i++;
                        }

                        if($i==$count)
                            $msg = 'Categorias selecionadas deletadas com sucesso';
                        elseif($i>0)
                            $warn = "$i of $count categorias foram deletadas";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível deletar as categorias selecionadas';
                        break;
                    default:
                        $errors['err']='Ação/comando desconhecido';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
            break;
    }
}

$page='categories.inc.php';
if($category || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='category.inc.php';

$nav->setTabActive('kbase');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
