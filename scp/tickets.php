<?php
/*************************************************************************
    tickets.php

    Handles all tickets related actions.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

require('staff.inc.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.dept.php');
require_once(INCLUDE_DIR.'class.filter.php');
require_once(INCLUDE_DIR.'class.canned.php');


$page='';
$ticket=null; //clean start.
//LOCKDOWN...See if the id provided is actually valid and if the user has access.
if($_REQUEST['id']) {
    if(!($ticket=Ticket::lookup($_REQUEST['id'])))
         $errors['err']='ID do ticket inválido ou desconhecido';
    elseif(!$ticket->checkStaffAccess($thisstaff)) {
        $errors['err']='Accesso negaod. Contate o administrador, se você acredita que isso é um erro';
        $ticket=null; //Clear ticket obj.
    }
}
//At this stage we know the access status. we can process the post.
if($_POST && !$errors):

    if($ticket && $ticket->getId()) {
        //More coffee please.
        $errors=array();
        $lock=$ticket->getLock(); //Ticket lock if any
        $statusKeys=array('open'=>'Aberto','Reopen'=>'Aberto','Close'=>'Fechado');
        switch(strtolower($_POST['a'])):
        case 'reply':
            if(!$thisstaff->canPostReply())
                $errors['err'] = 'Ação negada. para acessar, contate o administrador';
            else {

                if(!$_POST['response'])
                    $errors['response']='A resposta é requerida';
                //Use locks to avoid double replies
                if($lock && $lock->getStaffId()!=$thisstaff->getId())
                    $errors['err']='Ação negada. Ticket bloqueado por outro usuário!';

                //Make sure the email is not banned
                if(!$errors['err'] && TicketFilter::isBanned($ticket->getEmail()))
                    $errors['err']='E-mail está na lista de proibição. Remova da lista e tente novamente.';
            }

            $wasOpen =($ticket->isOpen());

            //If no error...do the do.
            $vars = $_POST;
            if(!$errors && $_FILES['attachments'])
                $vars['files'] = AttachmentFile::format($_FILES['attachments']);

            if(!$errors && ($response=$ticket->postReply($vars, $errors, isset($_POST['emailreply'])))) {
                $msg='Mensagem enviada com sucesso';
                $ticket->reload();
                if($ticket->isClosed() && $wasOpen)
                    $ticket=null;

            } elseif(!$errors['err']) {
                $errors['err']='Não foi  possível enviar a mensagem. Corrija os erros abaixo e tente novamente!';
            }
            break;
        case 'transfer': /** Transfer ticket **/
            //Check permission
            if(!$thisstaff->canTransferTickets())
                $errors['err']=$errors['transfer'] = 'Ação negada. Você não tem permissão para transferir tickets.';
            else {

                //Check target dept.
                if(!$_POST['deptId'])
                    $errors['deptId'] = 'Selecione departamento';
                elseif($_POST['deptId']==$ticket->getDeptId())
                    $errors['deptId'] = 'Ticket já está atribuído no departamento';
                elseif(!($dept=Dept::lookup($_POST['deptId'])))
                    $errors['deptId'] = 'Departamento inválido ou desconhecido';

                //Transfer message - required.
                if(!$_POST['transfer_comments'])
                    $errors['transfer_comments'] = 'Comentários de transferência exigido';
                elseif(strlen($_POST['transfer_comments'])<5)
                    $errors['transfer_comments'] = 'Comentários de transferência muito curto!';

                //If no errors - them attempt the transfer.
                if(!$errors && $ticket->transfer($_POST['deptId'], $_POST['transfer_comments'])) {
                    $msg = 'Ticket transferido com sucesso para '.$ticket->getDeptName();
                    //Check to make sure the staff still has access to the ticket
                    if(!$ticket->checkStaffAccess($thisstaff))
                        $ticket=null;

                } elseif(!$errors['transfer']) {
                    $errors['err'] = 'Não foi possível completar trasnferêncian do ticket';
                    $errors['transfer']='Corrija o(s) erro(s) abaixo e tente novamente!';
                }
            }
            break;
        case 'assign':

             if(!$thisstaff->canAssignTickets())
                 $errors['err']=$errors['assign'] = 'Ação negada. Você não tem permissão para atribuir/reatribuir o ticket.';
             else {

                 $id = preg_replace("/[^0-9]/", "",$_POST['assignId']);
                 $claim = (is_numeric($_POST['assignId']) && $_POST['assignId']==$thisstaff->getId());

                 if(!$_POST['assignId'] || !$id)
                     $errors['assignId'] = 'Selecione a atribuição';
                 elseif($_POST['assignId'][0]!='s' && $_POST['assignId'][0]!='t' && !$claim)
                     $errors['assignId']='ID da atribuição inválido - peça ajuada ao suporte técnico';
                 elseif($ticket->isAssigned()) {
                     if($_POST['assignId'][0]=='s' && $id==$ticket->getStaffId())
                         $errors['assignId']='Ticket já foi atribuído por outra pessoa.';
                     elseif($_POST['assignId'][0]=='t' && $id==$ticket->getTeamId())
                         $errors['assignId']='Ticket já foi atribuído por outro departamento.';
                 }

                 //Comments are not required on self-assignment (claim)
                 if($claim && !$_POST['assign_comments'])
                     $_POST['assign_comments'] = 'Ticket reinvidicado por '.$thisstaff->getName();
                 elseif(!$_POST['assign_comments'])
                     $errors['assign_comments'] = 'A atribuição do comentário é exigida';
                 elseif(strlen($_POST['assign_comments'])<5)
                         $errors['assign_comments'] = 'Comentário muito curto';

                 if(!$errors && $ticket->assign($_POST['assignId'], $_POST['assign_comments'], !$claim)) {
                     if($claim) {
                         $msg = 'O ticket fo atribuído AGORA para você!';
                     } else {
                         $msg='Ticket atribuído com sucesso para '.$ticket->getAssigned();
                         TicketLock::removeStaffLocks($thisstaff->getId(), $ticket->getId());
                         $ticket=null;
                     }
                 } elseif(!$errors['assign']) {
                     $errors['err'] = 'Não foi possível concluir a atribuição de tickets!';
                     $errors['assign'] = 'Corrija os erros abaixo e tente novamente!';
                 }
             }
            break;
        case 'postnote': /* Post Internal Note */
            //Make sure the staff can set desired state
            if($_POST['state']) {
                if($_POST['state']=='closed' && !$thisstaff->canCloseTickets())
                    $errors['state'] = "Você não tem permissão para fechar o ticket";
                elseif(in_array($_POST['state'], array('overdue', 'notdue', 'unassigned'))
                        && (!($dept=$ticket->getDept()) || !$dept->isManager($thisstaff)))
                    $errors['state'] = "Você não tem permissão para mudar o status do ticket";
            }

            $wasOpen = ($ticket->isOpen());

            $vars = $_POST;
            if($_FILES['attachments'])
                $vars['files'] = AttachmentFile::format($_FILES['attachments']);

            if(($note=$ticket->postNote($vars, $errors, $thisstaff))) {

                $msg='Nota interna postada com sucesso';
                if($wasOpen && $ticket->isClosed())
                    $ticket = null; //Going back to main listing.

            } else {

                if(!$errors['err'])
                    $errors['err'] = 'Não foi possível postar nota interna - dados inválidos ou faltando.';

                $errors['postnote'] = 'Não foi possível postar a nota. Corrigir o(s) erro(s) abaixo e tente novamente!';
            }
            break;
        case 'edit':
        case 'update':
            if(!$ticket || !$thisstaff->canEditTickets())
                $errors['err']='Permissão negada. Você não pode editar tickets';
            elseif($ticket->update($_POST,$errors)) {
                $msg='Ticket atualizado com sucesso';
                $_REQUEST['a'] = null; //Clear edit action - going back to view.
                //Check to make sure the staff STILL has access post-update (e.g dept change).
                if(!$ticket->checkStaffAccess($thisstaff))
                    $ticket=null;
            } elseif(!$errors['err']) {
                $errors['err']='Não foi possível atualizar o ticket. Corrija o(s) erro(s) abaixo e tente novamente';
            }
            break;
        case 'process':
            switch(strtolower($_POST['do'])):
                case 'close':
                    if(!$thisstaff->canCloseTickets()) {
                        $errors['err'] = 'Permissão negada. Você não pode fechar o ticket.';
                    } elseif($ticket->isClosed()) {
                        $errors['err'] = 'O ticket já está fechado!';
                    } elseif($ticket->close()) {
                        $msg='Ticket #'.$ticket->getExtId().' foi mudado para fechado';
                        //Log internal note
                        if($_POST['ticket_status_notes'])
                            $note = $_POST['ticket_status_notes'];
                        else
                            $note='Ticket fechado (sem comentários)';

                        $ticket->logNote('Ticket fechado', $note, $thisstaff);

                        //Going back to main listing.
                        TicketLock::removeStaffLocks($thisstaff->getId(), $ticket->getId());
                        $page=$ticket=null;

                    } else {
                        $errors['err']='Problemas ao fechar o ticket. Tente novamente!';
                    }
                    break;
                case 'reopen':
                    //if staff can close or create tickets ...then assume they can reopen.
                    if(!$thisstaff->canCloseTickets() && !$thisstaff->canCreateTickets()) {
                        $errors['err']='Permissão negada. Você não está permitido para reabrir o ticket.';
                    } elseif($ticket->isOpen()) {
                        $errors['err'] = 'Ticket já está aberto!';
                    } elseif($ticket->reopen()) {
                        $msg='Ticket REABERTO';

                        if($_POST['ticket_status_notes'])
                            $note = $_POST['ticket_status_notes'];
                        else
                            $note='Ticket reaberto (sem comentários)';

                        $ticket->logNote('Ticket reaberto', $note, $thisstaff);

                    } else {
                        $errors['err']='Problemas na reabertura do ticket. Tente novamente';
                    }
                    break;
                case 'release':
                    if(!$ticket->isAssigned() || !($assigned=$ticket->getAssigned())) {
                        $errors['err'] = 'Ticket não está atribuído!';
                    } elseif($ticket->release()) {
                        $msg='Ticket liberado (não atribído) para '.$assigned;
                        $ticket->logActivity('Ticket não atribuído',$msg.' por '.$thisstaff->getName());
                    } else {
                        $errors['err'] = 'Problemas ao liberar o ticket. Tente novamente';
                    }
                    break;
                case 'claim':
                    if(!$thisstaff->canAssignTickets()) {
                        $errors['err'] = 'Permissão negada. Você não tem permissão para atribuir/reivindicar bilhetes.';
                    } elseif(!$ticket->isOpen()) {
                        $errors['err'] = 'Somente bilhetes em aberto pode ser atribuído';
                    } elseif($ticket->isAssigned()) {
                        $errors['err'] = 'Ticket já está atribuído a '.$ticket->getAssigned();
                    } elseif($ticket->assignToStaff($thisstaff->getId(), ('Ticket reinvidicado por '.$thisstaff->getName()), false)) {
                        $msg = 'Ticket agora está atribuído a você!';
                    } else {
                        $errors['err'] = 'Problemas ao atribuir o ticket. Tente novamente';
                    }
                    break;
                case 'overdue':
                    $dept = $ticket->getDept();
                    if(!$dept || !$dept->isManager($thisstaff)) {
                        $errors['err']='Permissão negada. Você não está permitido para setar tickets vencidos';
                    } elseif($ticket->markOverdue()) {
                        $msg='Ticket está vencido';
                        $ticket->logActivity('Ticket colocado como vencido',($msg.' por '.$thisstaff->getName()));
                    } else {
                        $errors['err']='Ocorreu problemas ao marcar o ticket como vencido. Tente novamente';
                    }
                    break;
                case 'answered':
                    $dept = $ticket->getDept();
                    if(!$dept || !$dept->isManager($thisstaff)) {
                        $errors['err']='Permissão negada. Você não tem permissão para alterar bilhetes';
                    } elseif($ticket->markAnswered()) {
                        $msg='Ticket sinalizado como respondido';
                        $ticket->logActivity('Ticket marcado como respondido',($msg.' por '.$thisstaff->getName()));
                    } else {
                        $errors['err']='Problemas para responder o ticket. Tente novamente';
                    }
                    break;
                case 'unanswered':
                    $dept = $ticket->getDept();
                    if(!$dept || !$dept->isManager($thisstaff)) {
                        $errors['err']='Permissão negada. Você não pode permitir alterações no ticket';
                    } elseif($ticket->markUnAnswered()) {
                        $msg='Ticket marcado como respondido';
                        $ticket->logActivity('Ticket marcado como sem resposta',($msg.' por '.$thisstaff->getName()));
                    } else {
                        $errors['err']='Ocorreu probelmas ao marcar o ticket como sem reposta. Tente novamente';
                    }
                    break;
                case 'banemail':
                    if(!$thisstaff->canBanEmails()) {
                        $errors['err']='Permissão negada. Você não pode bloquear e-mails.';
                    } elseif(BanList::includes($ticket->getEmail())) {
                        $errors['err']='Email já está bloqueado';
                    } elseif(Banlist::add($ticket->getEmail(),$thisstaff->getName())) {
                        $msg='O e-mail ('.$ticket->getEmail().') foi adcionado na lista de bloqueios.';
                    } else {
                        $errors['err']='Não foi possível adicionar o e-mail na lista';
                    }
                    break;
                case 'unbanemail':
                    if(!$thisstaff->canBanEmails()) {
                        $errors['err'] = 'Permissão negada. Você não pode excluir e-mail da lista de bloqueios.';
                    } elseif(Banlist::remove($ticket->getEmail())) {
                        $msg = 'E-mail removido da lista de bloqueios com sucesso.';
                    } elseif(!BanList::includes($ticket->getEmail())) {
                        $warn = 'Não existe o e-mail na lista de bloqueios.';
                    } else {
                        $errors['err']='Não foi possível remover o e-mail da lista. Tente novamente.';
                    }
                    break;
                case 'delete': // Dude what are you trying to hide? bad customer support??
                    if(!$thisstaff->canDeleteTickets()) {
                        $errors['err']='Permissão negada. Você não tem permissão para DELETAR tickets!';
                    } elseif($ticket->delete()) {
                        $msg='Ticket #'.$ticket->getNumber().' deletado com sucesso';
                        //Log a debug note
                        $ost->logDebug('Ticket #'.$ticket->getNumber().' deletado',
                                sprintf('Ticket #%s deletado por %s',
                                    $ticket->getNumber(), $thisstaff->getName())
                                );
                        $ticket=null; //clear the object.
                    } else {
                        $errors['err']='Problemas quando o ticket estava sendo deletado. Tente novamente';
                    }
                    break;
                default:
                    $errors['err']='Você deve selecionar a ação à ser executada.';
            endswitch;
            break;
        default:
            $errors['err']='Ação desconhecida';
        endswitch;
        if($ticket && is_object($ticket))
            $ticket->reload();//Reload ticket info following post processing
    }elseif($_POST['a']) {

        switch($_POST['a']) {
            case 'mass_process':
                if(!$thisstaff->canManageTickets())
                    $errors['err']='Você não tem permissão para gerenciar vários tickets. Contate o administrador para lhe conceder o acesso!';
                elseif(!$_POST['tids'] || !is_array($_POST['tids']))
                    $errors['err']='Não há ticket selecionado. Você deve selecionar pelo menos um ticket.';
                else {
                    $count=count($_POST['tids']);
                    $i = 0;
                    switch(strtolower($_POST['do'])) {
                        case 'reopen':
                            if($thisstaff->canCloseTickets() || $thisstaff->canCreateTickets()) {
                                $note='Ticketreaberto por '.$thisstaff->getName();
                                foreach($_POST['tids'] as $k=>$v) {
                                    if(($t=Ticket::lookup($v)) && $t->isClosed() && @$t->reopen()) {
                                        $i++;
                                        $t->logNote('Ticket reaberto', $note, $thisstaff);
                                    }
                                }

                                if($i==$count)
                                    $msg = "O ticket selecionado ($i) foi reaberto com sucesso.";
                                elseif($i)
                                    $warn = "$i of $count tickets selecionados foram reabertos.";
                                else
                                    $errors['err'] = 'Não foi possível reabrir o ticket selecionado.';
                            } else {
                                $errors['err'] = 'Você não tem permissão para reabrir tickets.';
                            }
                            break;
                        case 'close':
                            if($thisstaff->canCloseTickets()) {
                                $note='Ticket fechado sem resposta por '.$thisstaff->getName();
                                foreach($_POST['tids'] as $k=>$v) {
                                    if(($t=Ticket::lookup($v)) && $t->isOpen() && @$t->close()) {
                                        $i++;
                                        $t->logNote('Ticket Closed', $note, $thisstaff);
                                    }
                                }

                                if($i==$count)
                                    $msg ="O ticket selecionado ($i) foi fechado com sucesso.";
                                elseif($i)
                                    $warn = "$i de $count tickets selecionados foram fechados com sucesso..";
                                else
                                    $errors['err'] = 'Não foi possível fechar os tickets selecionados.';
                            } else {
                                $errors['err'] = 'Você não tem permissão para fechar tickets.';
                            }
                            break;
                        case 'mark_overdue':
                            $note='Ticket marcado como vencido por '.$thisstaff->getName();
                            foreach($_POST['tids'] as $k=>$v) {
                                if(($t=Ticket::lookup($v)) && !$t->isOverdue() && $t->markOverdue()) {
                                    $i++;
                                    $t->logNote('Ticket marcado como vencido', $note, $thisstaff);
                                }
                            }

                            if($i==$count)
                                $msg = "Os tickets selecionados ($i) foram marcados como vencidos";
                            elseif($i)
                                $warn = "$i de $count tickets selecionados foram marcados como vencidos.";
                            else
                                $errors['err'] = 'Não foi possível marcar como vencido.';
                            break;
                        case 'delete':
                            if($thisstaff->canDeleteTickets()) {
                                foreach($_POST['tids'] as $k=>$v) {
                                    if(($t=Ticket::lookup($v)) && @$t->delete()) $i++;
                                }

                                //Log a warning
                                if($i) {
                                    $log = sprintf('%s (%s) apenas  %d ticket(s) excluídos.',
                                            $thisstaff->getName(), $thisstaff->getUserName(), $i);
                                    $ost->logWarning('Tickets deletados', $log, false);

                                }

                                if($i==$count)
                                    $msg = "O ticket selecionado ($i) foi deletado com sucesso.";
                                elseif($i)
                                    $warn = "$i de $count tickets selecionados foram deletados.";
                                else
                                    $errors['err'] = 'Não foi possível deletar os tickets selecionados.';
                            } else {
                                $errors['err'] = 'Você não tem permissão para deletar o ticket.';
                            }
                            break;
                        default:
                            $errors['err']='Ação desconhecida ou não suportada - obter ajuda técnica';
                    }
                }
                break;
            case 'open':
                $ticket=null;
                if(!$thisstaff || !$thisstaff->canCreateTickets()) {
                     $errors['err']='Você não tem permissão para criar tickets. Contate o administrador se você realmente precisa da permissão.';
                } else {
                    $vars = $_POST;
                    if($_FILES['attachments'])
                        $vars['files'] = AttachmentFile::format($_FILES['attachments']);

                    if(($ticket=Ticket::open($vars, $errors))) {
                        $msg='Ticket criado com sucesso.';
                        $_REQUEST['a']=null;
                        if(!$ticket->checkStaffAccess($thisstaff) || $ticket->isClosed())
                            $ticket=null;
                    } elseif(!$errors['err']) {
                        $errors['err']='Não foi possível criar o ticket. Corrija o(s) erro(s) e tente novamente';
                    }
                }
                break;
        }
    }
    if(!$errors)
        $thisstaff ->resetStats(); //We'll need to reflect any changes just made!
endif;

/*... Quick stats ...*/
$stats= $thisstaff->getTicketsStats();

//Navigation
$nav->setTabActive('tickets');
if($cfg->showAnsweredTickets()) {
    $nav->addSubMenu(array('desc'=>'Tickets Abertos ('.number_format($stats['open']+$stats['answered']).')',
                            'title'=>'Tickets Abertos',
                            'href'=>'tickets.php',
                            'iconclass'=>'Ticket'),
                        (!$_REQUEST['status'] || $_REQUEST['status']=='open'));
} else {

    if($stats) {
        $nav->addSubMenu(array('desc'=>'Tickets Abertos ('.number_format($stats['open']).')',
                               'title'=>'Tickets Abertos',
                               'href'=>'tickets.php',
                               'iconclass'=>'Ticket'),
                            (!$_REQUEST['status'] || $_REQUEST['status']=='open'));
    }

    if($stats['answered']) {
        $nav->addSubMenu(array('desc'=>'Respondido ('.number_format($stats['answered']).')',
                               'title'=>'Tickets Respondidos',
                               'href'=>'tickets.php?status=answered',
                               'iconclass'=>'answeredTickets'),
                            ($_REQUEST['status']=='answered'));
    }
}

if($stats['assigned']) {
    if(!$ost->getWarning() && $stats['assigned']>10)
        $ost->setWarning($stats['assigned'].' tickets respondidos por você!');

    $nav->addSubMenu(array('desc'=>'Meus Tickets ('.number_format($stats['assigned']).')',
                           'title'=>'Tickets Atribuídos',
                           'href'=>'tickets.php?status=assigned',
                           'iconclass'=>'assignedTickets'),
                        ($_REQUEST['status']=='assigned'));
}

if($stats['overdue']) {
    $nav->addSubMenu(array('desc'=>'Vencidos ('.number_format($stats['overdue']).')',
                           'title'=>'Tickets Vencidos',
                           'href'=>'tickets.php?status=overdue',
                           'iconclass'=>'overdueTickets'),
                        ($_REQUEST['status']=='overdue'));

    if(!$sysnotice && $stats['overdue']>10)
        $sysnotice=$stats['overdue'] .' tickets vencidos!';
}

if($thisstaff->showAssignedOnly() && $stats['closed']) {
    $nav->addSubMenu(array('desc'=>'Meus Tickets Fechados ('.number_format($stats['closed']).')',
                           'title'=>'Meus Tickets Fechados',
                           'href'=>'tickets.php?status=closed',
                           'iconclass'=>'closedTickets'),
                        ($_REQUEST['status']=='closed'));
} else {

    $nav->addSubMenu(array('desc'=>'Tickets Fechados ('.number_format($stats['closed']).')',
                           'title'=>'Tickets Fechados',
                           'href'=>'tickets.php?status=closed',
                           'iconclass'=>'closedTickets'),
                        ($_REQUEST['status']=='closed'));
}

if($thisstaff->canCreateTickets()) {
    $nav->addSubMenu(array('desc'=>'Ticket Novo',
                           'href'=>'tickets.php?a=open',
                           'iconclass'=>'newTicket'),
                        ($_REQUEST['a']=='open'));
}


$inc = 'tickets.inc.php';
if($ticket) {
    $ost->setPageTitle('Ticket #'.$ticket->getNumber());
    $nav->setActiveSubMenu(-1);
    $inc = 'ticket-view.inc.php';
    if($_REQUEST['a']=='edit' && $thisstaff->canEditTickets())
        $inc = 'ticket-edit.inc.php';
    elseif($_REQUEST['a'] == 'print' && !$ticket->pdfExport($_REQUEST['psize'], $_REQUEST['notes']))
        $errors['err'] = 'Erro! Não foi possível exportar o ticket para PDF ou imprimí-lo.';
} else {
    $inc = 'tickets.inc.php';
    if($_REQUEST['a']=='open' && $thisstaff->canCreateTickets())
        $inc = 'ticket-open.inc.php';
    elseif($_REQUEST['a'] == 'export') {
        require_once(INCLUDE_DIR.'class.export.php');
        $ts = strftime('%Y%m%d');
        if (!($token=$_REQUEST['h']))
            $errors['err'] = 'Token de consulta é necessário.';
        elseif (!($query=$_SESSION['search_'.$token]))
            $errors['err'] = 'Token de consulta não encontrado';
        elseif (!Export::saveTickets($query, "tickets-$ts.csv", 'csv'))
            $errors['err'] = 'Erro! Não foi possível obter resultado(s) na pesquisa.';
    }

    //Clear active submenu on search with no status
    if($_REQUEST['a']=='search' && !$_REQUEST['status'])
        $nav->setActiveSubMenu(-1);

    //set refresh rate if the user has it configured
    if(!$_POST && !$_REQUEST['a'] && ($min=$thisstaff->getRefreshRate()))
        $ost->addExtraHeader('<meta http-equiv="refresh" content="'.($min*60).'" />');
}

require_once(STAFFINC_DIR.'header.inc.php');
require_once(STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');
?>
