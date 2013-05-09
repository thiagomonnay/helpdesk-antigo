<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Configurações e Opções de E-mails</h2>
<form action="settings.php?t=emails" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="emails" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Configurações dos E-mails</h4>
                <em>Observe que algumas das configurações globais pode ser substituídas no departamento / nível email.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">E-mail Padrão do Sistema:</td>
            <td>
                <select name="default_email_id">
                    <option value=0 disabled>Selecione Um</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id,$email,$name) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                 &nbsp;<font class="error">*&nbsp;<?php echo $errors['default_email_id']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">E-mail de Alerta Padrão:</td>
            <td>
                <select name="alert_email_id">
                    <option value="0" selected="selected">Use o E-mail Padrão do Sistema (acima)</option>
                    <?php
                    $sql='SELECT email_id,email,name FROM '.EMAIL_TABLE.' WHERE email_id != '.db_input($config['default_email_id']);
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id,$email,$name) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['alert_email_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>
                 &nbsp;<font class="error">*&nbsp;<?php echo $errors['alert_email_id']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">E-mail do Administrador:</td>
            <td>
                <input type="text" size=40 name="admin_email" value="<?php echo $config['admin_email']; ?>">
                    &nbsp;<font class="error">*&nbsp;<?php echo $errors['admin_email']; ?></font>
                &nbsp;&nbsp;<em>(E-mail do administrador do sistema)</em> 
            </td>
        </tr>
        <tr><th colspan=2><em><strong>E-mails recebidos</strong>: Para a busca automática de e-mails funcionar, você deve definir um trabalho cron externo ou habilitar a checagem automática cron</em></th>
        <tr>
            <td width="180">Tipo do Recebimento de E-mails :</td>
            <td><input type="checkbox" name="enable_mail_polling" value=1 <?php echo $config['enable_mail_polling']? 'checked="checked"': ''; ?>  > Habilitar POP/IMAP
                 &nbsp;&nbsp;
                 <input type="checkbox" name="enable_auto_cron" <?php echo $config['enable_auto_cron']?'checked="checked"':''; ?>>
                 Habilitar auto-cron <em>(Poll baseia-se na atividade pessoal - não recomendável)</em>
            </td>
        </tr>
        <tr>
            <td width="180">Retirar o Citado ao Responder:</td>
            <td>
                <input type="checkbox" name="strip_quoted_reply" <?php echo $config['strip_quoted_reply'] ? 'checked="checked"':''; ?>>
                <em>(depende do separador set tag da resposta abaixo)</em>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['strip_quoted_reply']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Tag Separadora para Reposta:</td>
            <td><input type="text" name="reply_separator" value="<?php echo $config['reply_separator']; ?>">
                &nbsp;<font class="error">&nbsp;<?php echo $errors['reply_separator']; ?></font>
            </td>
        </tr>
        <tr><th colspan=2><em><strong>E-mails Enviados</strong>: E-mail padrão só se aplica aos e-mails enviados sem configuração SMTP.</em></th></tr>
        <tr><td width="180">E-mail Padrão para Envios:</td>
            <td>
                <select name="default_smtp_id">
                    <option value=0 selected="selected">None: Use PHP mail function</option>
                    <?php
                    $sql='SELECT email_id,email,name,smtp_host FROM '.EMAIL_TABLE.' WHERE smtp_active=1';

                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while (list($id,$email,$name,$host) = db_fetch_row($res)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            ?>
                            <option value="<?php echo $id; ?>"<?php echo ($config['default_smtp_id']==$id)?'selected="selected"':''; ?>><?php echo $email; ?></option>
                        <?php
                        }
                    } ?>
                 </select>&nbsp;&nbsp;<font class="error">&nbsp;<?php echo $errors['default_smtp_id']; ?></font>
           </td>
       </tr>
    </tbody>
</table>
<p style="padding-left:250px;">
    <input class="button" type="submit" name="submit" value="Salvar Alterações">
    <input class="button" type="reset" name="reset" value="Resetar Alterações">
</p>
</form>
