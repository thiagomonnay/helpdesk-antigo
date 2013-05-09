<?php
/*********************************************************************
    apikeys.php

    API keys.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.api.php');

$api=null;
if($_REQUEST['id'] && !($api=API::lookup($_REQUEST['id'])))
    $errors['err']='Unknown or invalid API key ID.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$api){
                $errors['err']='Chave da API desconhecida ou inválida.';
            }elseif($api->update($_POST,$errors)){
                $msg='Chave API atualizada com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro na atualização da chave de API, tente novamente!';
            }
            break;
        case 'add':
            if(($id=API::add($_POST,$errors))){
                $msg='Chave API adicionada com sucesso!';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não é possível adicionar uma chave API. Corrija o(s) erro(s) abaixo e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Você deve selecionar pelo menos uma chave de API';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.API_KEY_TABLE.' SET isactive=1 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Chaves API selecionados habilitada';
                            else
                                $warn = "$num of $count Selecione habilitar na chave API";
                        } else {
                            $errors['err'] = 'Não foi possível permitir chaves API selecionadas.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.API_KEY_TABLE.' SET isactive=0 '
                            .' WHERE id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Seleciona desabilitar na chave API';
                            else
                                $warn = "$num of $count seleciona na chave API, desabilitar";
                        } else {
                            $errors['err']='Não foi possível permitir chaves API selecionadas';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=API::lookup($v)) && $t->delete())
                                $i++;
                        }
                        if($i && $i==$count)
                            $msg = 'Chave API selecionada, foi desabilitada com sucesso';
                        elseif($i>0)
                            $warn = "$i of $count selected API keys deleted";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível excluir chaves API selecionados';
                        break;
                    default:
                        $errors['err']='Ação desconhecida - obter ajuda técnica';
                }
            }
            break;
        default:
            $errors['err']='Ação/Comando desconecido';
            break;
    }
}

$page='apikeys.inc.php';
if($api || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='apikey.inc.php';

$nav->setTabActive('manage');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
