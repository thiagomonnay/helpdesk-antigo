<?php
/*********************************************************************
    teams.php

    Evertything about teams

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$team=null;
if($_REQUEST['id'] && !($team=Team::lookup($_REQUEST['id'])))
    $errors['err']='ID da equipe inválido ou desconhecido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$team){
                $errors['err']='Equié inválida ou desconhecida.';
            }elseif($team->update($_POST,$errors)){
                $msg='Equipe atualizada com sucesso.';
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível atualizar a equipe. Corrija o(s) erro(s) abaixo e tente novamente!';
            }
            break;
        case 'create':
            if(($id=Team::create($_POST,$errors))){
                $msg=Format::htmlchars($_POST['team']).' adicionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível adicionar a equipe. Corrija o(s) erro(s) abaixo e tente novamente!';            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err']='Você deve selecionar pelo menos uma equipe.';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'enable':
                        $sql='UPDATE '.TEAM_TABLE.' SET isenabled=1 '
                            .' WHERE team_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Equipe selecionada ativada.';
                            else
                                $warn = "$num de $count selecinada ativada.";
                        } else {
                            $errors['err'] = 'Não foi possível ativar a equipe selecionada.';
                        }
                        break;
                    case 'disable':
                        $sql='UPDATE '.TEAM_TABLE.' SET isenabled=0 '
                            .' WHERE team_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Equipe selecionada desativada';
                            else
                                $warn = "$num de $count equipes selecionadas desativadas";
                        } else {
                            $errors['err'] = 'Não foi possível desabilitar a equipe selecionada';
                        }
                        break;
                    case 'delete':
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($t=Team::lookup($v)) && $t->delete())
                                $i++;
                        }
                        if($i && $i==$count)
                            $msg = 'Equipes selecionadas deletadas com sucesso';
                        elseif($i>0)
                            $warn = "$i de $count equipes selecionadas deletadas";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Não foi possível deletar a equipe selecionada';
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

$page='teams.inc.php';
if($team || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='team.inc.php';

$nav->setTabActive('staff');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
