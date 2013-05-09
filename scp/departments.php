<?php
/*********************************************************************
    departments.php

    Departments

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
$dept=null;
if($_REQUEST['id'] && !($dept=Dept::lookup($_REQUEST['id'])))
    $errors['err']='ID do departamento inválido ou desconhecido.';

if($_POST){
    switch(strtolower($_POST['do'])){
        case 'update':
            if(!$dept){
                $errors['err']='Departamento inválido ou desconhecido.';
            }elseif($dept->update($_POST,$errors)){
                $msg='Departamento atualizado com sucesso';
            }elseif(!$errors['err']){
                $errors['err']='Erro ao atualizar o departamento. Tente novamente!';
            }
            break;
        case 'create':
            if(($id=Dept::create($_POST,$errors))){
                $msg=Format::htmlchars($_POST['name']).' adicionado com sucesso';
                $_REQUEST['a']=null;
            }elseif(!$errors['err']){
                $errors['err']='Não foi possível adicionar o departamento. Coriija os erros e tente novamente.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err'] = 'Selecione pelo menos um departamento';
            }elseif(in_array($cfg->getDefaultDeptId(),$_POST['ids'])) {
                $errors['err'] = 'Você não pode deletar ou atualizar um departamento padrão. Remova o departemanto de padrão e tente novamente.';
            }else{
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'make_public':
                        $sql='UPDATE '.DEPT_TABLE.' SET ispublic=1 '
                            .' WHERE dept_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        if(db_query($sql) && ($num=db_affected_rows())){
                            if($num==$count)
                                $msg='Os departamentos selecionados, foram transformados em públicos';
                            else
                                $warn="$num of $count departamentos, foram transformados em públicos";
                        } else {
                            $errors['err']='Não foi possível transformar o departaento selecionado em público.';
                        }
                        break;
                    case 'make_private':
                        $sql='UPDATE '.DEPT_TABLE.' SET ispublic=0  '
                            .' WHERE dept_id IN ('.implode(',', db_input($_POST['ids'])).') '
                            .' AND dept_id!='.db_input($cfg->getDefaultDeptId());
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Departamento selecionado, transformado em privado';
                            else
                                $warn = "$num of $count departamentos foram transformados em privado";
                        } else {
                            $errors['err'] = 'Não foi possível transformar os departamentos selecionado em privado. Possivelmente ele já seja privado!';
                        }
                        break;
                    case 'delete':
                        //Deny all deletes if one of the selections has members in it.
                        $sql='SELECT count(staff_id) FROM '.STAFF_TABLE
                            .' WHERE dept_id IN ('.implode(',', db_input($_POST['ids'])).')';
                        list($members)=db_fetch_row(db_query($sql));
                        if($members)
                            $errors['err']='Departamentos com usuários não podem ser excluídos.';
                        else {
                            $i=0;
                            foreach($_POST['ids'] as $k=>$v) {
                                if($v!=$cfg->getDefaultDeptId() && ($d=Dept::lookup($v)) && $d->delete())
                                    $i++;
                            }
                            if($i && $i==$count)
                                $msg = 'Departamentos selecioandos foram deletados co sucesso';
                            elseif($i>0)
                                $warn = "$i of $count foram deletados";
                            elseif(!$errors['err'])
                                $errors['err'] = 'Não foi possível deletar os departamentos selecionados.';
                        }
                        break;
                    default:
                        $errors['err']='Ação desconhecida, peça ajuda técnica';
                }
            }
            break;
        default:
            $errors['err']='Ação/comando desconhecido';
            break;
    }
}

$page='departments.inc.php';
if($dept || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
    $page='department.inc.php';

$nav->setTabActive('staff');
require(STAFFINC_DIR.'header.inc.php');
require(STAFFINC_DIR.$page);
include(STAFFINC_DIR.'footer.inc.php');
?>
