<?php
/*********************************************************************
    helptopics.php

    Help Topics.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.topic.php');

$topic=null;
if($_REQUEST['id'] && !($topic=Topic::lookup($_REQUEST['id'])))
    $errors['err']='ID Tópico de ajuda desconhecido ou inválido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$topic){
                $errors['err']='Tópico de ajuda desconhecido ou inválido.';
            }elseif($topic->update($_POST,$errors)){
                $msg='Tópico de ajuda atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar o tópico. Tente novamente!';
            }
            break;
        case 'create':
            if(($id=Topic::create($_POST,$errors))){
                $msg='Tópico de ajuda atualizado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não é possível adicionar tópico de ajuda. Corrigir erro (s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos um tópico';
            } else {
                $count=count($_POST['ids']);

                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.TOPIC_TABLE.' SET isactive=1 '
                            .' WHERE topic_id IN ('.implode(',', db_input($_POST['ids'])).')';
                    
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Tópicos de ajuda selecionados habilitado';
                            else
                                $warn = "$num de $count tópicos de ajuda selecionados habilitado";
                        } else {
                            $errors['err'] = 'Não foi possível selecionar o tópico.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.TOPIC_TABLE.' SET isactive=0 '
                            .' WHERE topic_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Tópicos de ajuda selecionados deabilitado';
                            else
                                $warn = "$num de $count tópico de ajuda selecionados deabilitado";
                        } else {
                            $errors['err'] ='Não foi possível desabilitar os tópicos selecionados';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=Topic::lookup($v)) && $t->delete())
                                $i++;
                        }

                        if($i && $i==$count)
                            $msg = 'Tópico de ajuda selecionado deletado com seucesso';
                        elseif($i>0)
                            $warn = "$i de $count tópicos de ajuda selecionados deletados com seucesso";
                        elseif(!$errors['err'])
                            $errors['err']  = 'Não foi possível deletar o tópico de ajuda selecioando';

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

$page='helptopics.inc.php';
if($topic || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='helptopic.inc.php';

$nav->setTabActive('manage');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
