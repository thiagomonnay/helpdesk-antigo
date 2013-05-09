<?php
/*********************************************************************
    ajax.content.php

    AJAX interface for content fetching...allowed methods.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

if(!defined('INCLUDE_DIR')) die('!');
	    
class ContentAjaxAPI extends AjaxController {
   
    function log($id) {

        if($id && ($log=Log::lookup($id))) {
            $content=sprintf('<div style="width:500px;">&nbsp;<strong>%s</strong><br><p>%s</p>
                    <hr><strong>Log Date:</strong> <em>%s</em> <strong>Endereço de IP:</strong> <em>%s</em></div>',
                    $log->getTitle(),
                    Format::display(str_replace(',',', ',$log->getText())),
                    Format::db_daydatetime($log->getCreateDate()),
                    $log->getIP());
        }else {
            $content='<div style="width:295px;">&nbsp;<strong>Erro:</strong>ID do log inválido ou desconhecido</div>';
        }

        return $content;
    }

    function ticket_variables() {

        $content='
<div style="width:680px;">
    <h2>Variáveis do Ticket</h2>
    Por favor, note que as variáveis definidas depende do contexto de uso. Visite osTicket Wiki para documentação.
    <br/>
    <table width="100%" border="0" cellspacing=1 cellpadding=2>
        <tr><td width="55%" valign="top"><b>Variáveis base</b></td><td><b>Other Variables</b></td></tr>
        <tr>
            <td width="55%" valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td width="130">%{ticket.id}</td><td>ID do Ticket(ID interno)</td></tr>
                    <tr><td>%{ticket.number}</td><td>Número do Ticket(ID externo)</td></tr>
                    <tr><td>%{ticket.email}</td><td>Endereço de e-mail</td></tr>
                    <tr><td>%{ticket.name}</td><td>Nome completo</td></tr>
                    <tr><td>%{ticket.subject}</td><td>Assunto</td></tr>
                    <tr><td>%{ticket.phone}</td><td>Fone | Ramal</td></tr>
                    <tr><td>%{ticket.status}</td><td>Status</td></tr>
                    <tr><td>%{ticket.priority}</td><td>Prioridade</td></tr>
                    <tr><td>%{ticket.assigned}</td><td>Usuário e/ou equpe atribuída</td></tr>
                    <tr><td>%{ticket.create_date}</td><td>Data de criação</td></tr>
                    <tr><td>%{ticket.due_date}</td><td>Data de expiração</td></tr>
                    <tr><td>%{ticket.close_date}</td><td>Data de fechamento</td></tr>
                    <tr><td>%{ticket.auth_token}</td><td>Auth. token usado para auto-login</td></tr>
                    <tr><td>%{ticket.client_link}</td><td>Ligação de visualização para o ticket do cliente</td></tr>
                    <tr><td>%{ticket.staff_link}</td><td>Ligação de visualização para o ticket da equipe</td></tr>
                    <tr><td colspan="2" style="padding:5px 0 5px 0;"><em>Variáveis expansíveis (Veja o Wiki)</em></td></tr>
                    <tr><td>%{ticket.<b>topic</b>}</td><td>Tópicos de ajuda</td></tr>
                    <tr><td>%{ticket.<b>dept</b>}</td><td>Departamento</td></tr>
                    <tr><td>%{ticket.<b>staff</b>}</td><td>Usuário Atribuído/Fechado</td></tr>
                    <tr><td>%{ticket.<b>team</b>}</td><td>Equipe Atribuída/Fechada</td></tr>
                </table>
            </td>
            <td valign="top">
                <table width="100%" border="0" cellspacing=1 cellpadding=1>
                    <tr><td width="100">%{message}</td><td>Mensagem recebida</td></tr>
                    <tr><td>%{response}</td><td>Mensagem enviada</td></tr>
                    <tr><td>%{comments}</td><td>Comentários Atribuídos/Transferidos</td></tr>
                    <tr><td>%{note}</td><td>Nota interna <em>(expansível)</em></td></tr>
                    <tr><td>%{assignee}</td><td>Usuário/Equipe atribuída</td></tr>
                    <tr><td>%{assigner}</td><td>Equipe atribuída ao ticket</td></tr>
                    <tr><td>%{url}</td><td>osTicket\'s URL base (FQDN)</td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>';

        return $content;
    }
}
?>
