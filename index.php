<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');
$section = 'home';
require(CLIENTINC_DIR.'header.inc.php');
?>

<div id="landing_page">
    <h1>Bem vindo ao helpdesk - AETC/JP</h1>
    <p>
        A fim de agilizar o atendimento às requisições de suporte e melhor atendê-lo, nós utilizamos um sistema de tickets. A cada pedido de suporte é atribuído um número único que você pode usar para monitorar o progresso e as respostas online. Para sua referência, nós fornecemos arquivos completos e histórico de todos os seus pedidos de suporte. Um endereço de e-mail válido é necessário.
    </p>

    <div id="new_ticket">
        <h3>Abrir novo ticket</h3>
        <br>
        <div>Favor informar o máximo de detalhes possível para que possamos melhor atendê-lo. Para atualizar um ticket anterior, entre com usuário e senha..</div>
        <p>
            <a href="open.php" class="green button">Abrir Ticket !</a>
        </p>
    </div>

    <div id="check_status">
        <h3>Verificar status do seu ticket</h3>
        <br>
        <div>Nós possuímos o arquivo e histórico de todas as suas requisições de suporte, juntamente com suas respostas.</div>
        <p>
            <a href="view.php" class="blue button">Verificar Ticket !</a>
        </p>
    </div>
</div>
<div class="clear"></div>
<?php
if($cfg && $cfg->isKnowledgebaseEnabled()){
    //FIXME: provide ability to feature or select random FAQs ??
?>
<p>Não se esqueça de consultar nossas <a href="kb/index.php">Perguntas mais frequentes (FAQs)</a>, antes de abrir um ticket.</p>
</div>
<?php
} ?>
<?php require(CLIENTINC_DIR.'footer.inc.php'); ?>
