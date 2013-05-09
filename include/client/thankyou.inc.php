<?php
if(!defined('OSTCLIENTINC') || !is_object($ticket)) die('Kwaheri rafiki!');
//Please customize the message below to fit your organization speak!
?>
<div style="margin:5px 100px 100px 0;">
    <?php echo Format::htmlchars($ticket->getName()); ?>,<br>
    <p>
     Obrigado por nos contatar.<br>
     Um pedido de ticket de suporte foi criado e um representante estará repondendo à você em breve, se necessário.</p>
          
    <?php if($cfg->autoRespONNewTicket()){ ?>
    <p>Um e-mail com o número do ticket foi enviado para <b><?php echo $ticket->getEmail(); ?></b>.
        Você precisa ter o número do ticket, juntamente com seu e-mail para ver status online do seu ticket.
    </p>
    <p>
     Se você deseja enviar comentários ou informações adicionais sobre mesmo assunto, por favor, siga as instruções no e-mail.
    </p>
    <?php } ?>
    <p>Equipe de Suporte </p>
</div>
