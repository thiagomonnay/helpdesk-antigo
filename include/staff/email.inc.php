<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($email && $_REQUEST['a']!='add'){
    $title='Update Email';
    $action='update';
    $submit_text='Save Changes';
    $info=$email->getInfo();
    $info['id']=$email->getId();
    if($info['mail_delete'])
        $info['postfetch']='delete';
    elseif($info['mail_archivefolder'])
        $info['postfetch']='archive';
    else
        $info['postfetch']=''; //nothing.
    if($info['userpass'])
        $passwdtxt='To change password enter new password above.';

    $qstr.='&id='.$email->getId();
}else {
    $title='Add New Email';
    $action='create';
    $submit_text='Submit';
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<h2>Endereço de e-mail</h2>
<form action="emails.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong>Informação do e-mail</strong>: Detalhes de login são opcionais, mas necessários quando IMAP / POP ou SMTP estão habilitados.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
                Endereço de e-mail
            </td>
            <td>
                <input type="text" size="35" name="email" value="<?php echo $info['email']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['email']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Nome do e-mail
            </td>
            <td>
                <input type="text" size="35" name="name" value="<?php echo $info['name']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?>&nbsp;</span>
            </td>
        </tr>
        <tr>
            <td width="180">
                Nome do usuário
            </td>
            <td>
                <input type="text" size="35" name="userid" value="<?php echo $info['userid']; ?>">
                &nbsp;<span class="error">&nbsp;<?php echo $errors['userid']; ?>&nbsp;</span>
            </td>
        </tr>
        <tr>
            <td width="180">
                Senha do usuário
            </td>
            <td>
                <input type="password" size="35" name="passwd" value="<?php echo $info['passwd']; ?>">
                &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd']; ?>&nbsp;</span>
                <br><em><?php echo $passwdtxt; ?></em>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Conta de e-mnail</strong>: Configuração opcional para a recuperação de e-mails recebidos. Procurar e-mail deve estar habilitado com autocron ativa ou configuração cron externo. &nbsp;<font class="error">&nbsp;<?php echo $errors['mail']; ?></font></em>
            </th>
        </tr>
        <tr><td>Status</td>
            <td>
                <label><input type="radio" name="mail_active"  value="1"   <?php echo $info['mail_active']?'checked="checked"':''; ?> /><strong>Enable</strong></label>
                &nbsp;&nbsp;
                <label><input type="radio" name="mail_active"  value="0"   <?php echo !$info['mail_active']?'checked="checked"':''; ?> />Disable</label>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_active']; ?></font>
            </td>
        </tr>
        <tr><td>Host</td>
            <td><input type="text" name="mail_host" size=35 value="<?php echo $info['mail_host']; ?>">
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_host']; ?></font>
            </td>
        </tr>
        <tr><td>Porta</td>
            <td><input type="text" name="mail_port" size=6 value="<?php echo $info['mail_port']?$info['mail_port']:''; ?>">
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_port']; ?></font>
            </td>
        </tr>
        <tr><td>Protocolo</td>
            <td>
                <select name="mail_protocol">
                    <option value='POP'>&mdash; Selecione o protocolo do e-mail &mdash;</option>
                    <option value='POP' <?php echo ($info['mail_protocol']=='POP')?'selected="selected"':''; ?> >POP</option>
                    <option value='IMAP' <?php echo ($info['mail_protocol']=='IMAP')?'selected="selected"':''; ?> >IMAP</option>
                </select>
                <font class="error">&nbsp;<?php echo $errors['mail_protocol']; ?></font>
            </td>
        </tr>

        <tr><td>Encriptação</td>
            <td>
                <select name="mail_encryption">
                    <option value='NONE'>Nenhuma</option>
                    <option value='SSL' <?php echo ($info['mail_encryption']=='SSL')?'selected="selected"':''; ?> >SSL</option>
                </select>
                <font class="error">&nbsp;<?php echo $errors['mail_encryption']; ?></font>
            </td>
        </tr>
        <tr><td>Frequência</td>
            <td>
                <input type="text" name="mail_fetchfreq" size=4 value="<?php echo $info['mail_fetchfreq']?$info['mail_fetchfreq']:''; ?>"> Atraso em intervalos de minutos
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_fetchfreq']; ?></font>
            </td>
        </tr>
        <tr><td>Emails para busca</td>
            <td>
                <input type="text" name="mail_fetchmax" size=4 value="<?php echo $info['mail_fetchmax']?$info['mail_fetchmax']:''; ?>"> Máximo de e-mails para processar
                &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_fetchmax']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">
                Nova prioridade do ticket:
            </td>
            <td>
                <select name="priority_id">
                    <option value="">&mdash; Selecione a prioridade &mdash;</option>
                    <?php
                    $sql='SELECT priority_id,priority_desc FROM '.PRIORITY_TABLE.' pri ORDER by priority_urgency DESC';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['priority_id'] && $id==$info['priority_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<span class="error"><?php echo $errors['priority_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180">
                Novo ticket para o departamento.
            </td>
            <td>
                <select name="dept_id">
                    <option value="">&mdash; Selecione o departamento &mdash;</option>
                    <?php
                    $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' dept ORDER by dept_name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$name)=db_fetch_row($res)){
                            $selected=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<span class="error"><?php echo $errors['dept_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180">
                Auto-resposta
            </td>
            <td>
                <input type="checkbox" name="noautoresp" value="1" <?php echo $info['noautoresp']?'checked="checked"':''; ?> >
                <strong>Desabilitado</strong> novo ticket de auto-resposta para este e-mail. Substitua as configurações globais do dept.
            </td>
        </tr>
        <tr><td valign="top">E-mails pesquisados</td>
             <td>
                <input type="radio" name="postfetch" value="archive" <?php echo ($info['postfetch']=='archive')? 'checked="checked"': ''; ?> >
                 Mover para: <input type="text" name="mail_archivefolder" size="20" value="<?php echo $info['mail_archivefolder']; ?>"/> pasta.
                    &nbsp;<font class="error">&nbsp;<?php echo $errors['mail_folder']; ?></font>
                <input type="radio" name="postfetch" value="delete" <?php echo ($info['postfetch']=='delete')? 'checked="checked"': ''; ?> >
                Deletar e-mail pesquisado
                <input type="radio" name="postfetch" value="" <?php echo (isset($info['postfetch']) && !$info['postfetch'])? 'checked="checked"': ''; ?> >
                Não fazer nada (não recomendado)
              <br><em>Mover e-mails pesquisados para uma pasta de backup é altamente recomendado.</em> &nbsp;<font class="error"><?php echo $errors['postfetch']; ?></font>
            </td>
        </tr>

        <tr>
            <th colspan="2">
                <em><strong>Configurações SMTP</strong>: Quando ativada a <b>conta de e-mail</b> vai usar o servidor SMTP em vez de PHP mail interno (função para e-mails enviados). &nbsp;<font class="error">&nbsp;<?php echo $errors['smtp']; ?></font></em>
            </th>
        </tr>
        <tr><td>Status</td>
            <td>
                <label><input type="radio" name="smtp_active"  value="1"   <?php echo $info['smtp_active']?'checked':''; ?> />Habilitado</label>
                <label><input type="radio" name="smtp_active"  value="0"   <?php echo !$info['smtp_active']?'checked':''; ?> />Desablitado</label>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['smtp_active']; ?></font>
            </td>
        </tr>
        <tr><td>Host SMTP</td>
            <td><input type="text" name="smtp_host" size=35 value="<?php echo $info['smtp_host']; ?>">
                &nbsp;<font class="error">&nbsp;<?php echo $errors['smtp_host']; ?></font>
            </td>
        </tr>
        <tr><td>Porta SMTP</td>
            <td><input type="text" name="smtp_port" size=6 value="<?php echo $info['smtp_port']?$info['smtp_port']:''; ?>">
                &nbsp;<font class="error">&nbsp;<?php echo $errors['smtp_port']; ?></font>
            </td>
        </tr>
        <tr><td>Autenticação?</td>
            <td>

                 <label><input type="radio" name="smtp_auth"  value="1"
                    <?php echo $info['smtp_auth']?'checked':''; ?> />Sim</label>
                 <label><input type="radio" name="smtp_auth"  value="0"
                    <?php echo !$info['smtp_auth']?'checked':''; ?> />Não</label>
                <font class="error">&nbsp;<?php echo $errors['smtp_auth']; ?></font>
            </td>
        </tr>
        <tr>
            <td>Permitir cabeçalho?</td>
            <td>
                <input type="checkbox" name="smtp_spoofing" value="1" <?php echo $info['smtp_spoofing'] ?'checked="checked"':''; ?>>
                Permiti cabeçalho de email <em>(só se aplica aos e-mails que estão sendo enviados através desta conta)</em>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Notas internas</strong>: Notas admnistrativas. &nbsp;<span class="error">&nbsp;<?php echo $errors['notes']; ?></span></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea name="notes" cols="21" rows="5" style="width: 60%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  value="Resetar">
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="emails.php"'>
</p>
</form>
