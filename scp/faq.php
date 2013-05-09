<?php
/*********************************************************************
    faq.php

    FAQs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
require_once(INCLUDE_DIR.'class.faq.php');

$faq=$category=null;
if($_REQUEST['id'] && !($faq=FAQ::lookup($_REQUEST['id'])))
   $errors['err']='FAQ desconhcida ou inválida';

if($_REQUEST['cid'] && !$faq && !($category=Category::lookup($_REQUEST['cid'])))
    $errors['err']='Categoria da FAQ desconhecida ou inváilida';

if($_POST):
    $errors=array();
    switch(strtolower($_POST['do'])) {
        case 'create':
        case 'add':
            if(($faq=FAQ::add($_POST,$errors)))
                $msg='FAQ adicionada com sucesso';
            elseif(!$errors['err'])
                $errors['err'] = 'Não foi possível adicionar a FAQ. Tente novamente!';
        break;
        case 'update':
        case 'edit';
            if(!$faq)
                $errors['err'] = 'FAQ inválida ou desconhecida';
            elseif($faq->update($_POST,$errors)) {
                $msg='FAQ atualizada com sucesso';
                $_REQUEST['a']=null; //Go back to view
                $faq->reload();
            } elseif(!$errors['err'])
                $errors['err'] = 'Não foi possível atualizar a FAQ. Tente novamente!';     
            break;
        case 'manage-faq':
            if(!$faq) {
                $errors['err']='FAQ inválida ou dsconhecida';
            } else {
                switch(strtolower($_POST['a'])) {
                    case 'edit':
                        $_GET['a']='edit';
                        break;
                    case 'publish';
                        if($faq->publish())
                            $msg='FAQ publicada com sucesso';
                        else
                            $errors['err']='Não foi possível publicar a FAQ. Tente editar novamente.';
                        break;
                    case 'unpublish';
                        if($faq->unpublish())
                            $msg='FAQ retirada com sucesso';
                        else
                            $errors['err']='Não foi possível retirar a FAQ. Tente editá-la novamente.';
                        break;
                    case 'delete':
                        $category = $faq->getCategory();
                        if($faq->delete()) {
                            $msg='FAQ deletada com sucesso';
                            $faq=null;
                        } else {
                            $errors['err']='Não foi possível deletar a FAQ. Tente novamente';
                        }
                        break;
                    default:
                        $errors['err']='Ação inválida';
                }
            }
            break;
        default:
            $errors['err']='Ação desconhecida';
    
    }
endif;


$inc='faq-categories.inc.php'; //FAQs landing page.
if($faq) {
    $inc='faq-view.inc.php';
    if($_REQUEST['a']=='edit' && $thisstaff->canManageFAQ())
        $inc='faq.inc.php';
}elseif($_REQUEST['a']=='add' && $thisstaff->canManageFAQ()) {
    $inc='faq.inc.php';
} elseif($category && $_REQUEST['a']!='search') {
    $inc='faq-category.inc.php';
}
$nav->setTabActive('kbase');
require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');
?>
