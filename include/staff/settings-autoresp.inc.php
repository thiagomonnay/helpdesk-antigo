<h2>Configurações de Auto-Respostas</h2>
<form action="settings.php?t=autoresp" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="autoresp" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Configurações de Auto-Respostas</h4>
                <em>Configurações Globais - pode ser desativada em nível de departamento ou e-mail.</em>
            </th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td width="160">Novo Ticket:</td>
            <td>
                <input type="radio" name="ticket_autoresponder"  value="1"   <?php echo $config['ticket_autoresponder']?'checked="checked"':''; ?> /><b>Habilitado</b>
                <input type="radio" name="ticket_autoresponder"  value="0"   <?php echo !$config['ticket_autoresponder']?'checked="checked"':''; ?> />Desabilitado
                &nbsp;&nbsp;&nbsp;
                <em>(Autoresponse inclui a identificação do bilhete necessário para verificar o status do bilhete)</em>
            </td>
        </tr>
        <tr>
            <td width="160">Novo Ticket Pela Equipe:</td>
            <td>
                <input type="radio" name="ticket_notice_active"  value="1"   <?php echo $config['ticket_notice_active']?'checked="checked"':''; ?> /><b>Habilitado</b>
                <input type="radio" name="ticket_notice_active"  value="0"   <?php echo !$config['ticket_notice_active']?'checked="checked"':''; ?> />Desabilitado
                 &nbsp;&nbsp;&nbsp;
                 <em>(Aviso enviado quando a equipe cria um bilhete em nome do usuário (funcionários podem substituir))</em>
            </td>
        </tr>
        <tr>
            <td width="160">Nova Mensagem:</td>
            <td>
                <input type="radio" name="message_autoresponder"  value="1"   <?php echo $config['message_autoresponder']?'checked="checked"':''; ?> /><b>Habilitado</b>
                <input type="radio" name="message_autoresponder"  value="0"   <?php echo !$config['message_autoresponder']?'checked="checked"':''; ?> />Desabilitado
                &nbsp;&nbsp;&nbsp;
                <em>(Aviso de confirmação enviado quando uma nova mensagem é anexado a um ticket existente)</em>
            </td>
        </tr>
        <tr>
            <td width="160">Aviso Sobre Limite:</td>
            <td>
                <input type="radio" name="overlimit_notice_active"  value="1"   <?php echo $config['overlimit_notice_active']?'checked="checked"':''; ?> /><b>Habilitado</b>
                <input type="radio" name="overlimit_notice_active"  value="0"   <?php echo !$config['overlimit_notice_active']?'checked="checked"':''; ?> />Desabilitado
                &nbsp;&nbsp;&nbsp;
                <em>(Negado a notificação do ticket enviada ao usuário em violação do limite. Administrador recebe alertas em TODOS desmentidos por padrão)</em>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:200px;">
    <input class="button" type="submit" name="submit" value="Salvar Alterações">
    <input class="button" type="reset" name="reset" value="Resetar Alterações">
</p>
</form>
