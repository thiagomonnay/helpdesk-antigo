<?php
/*********************************************************************
    emailtest.php

    Email Diagnostic 

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('admin.inc.php');
include_once(INCLUDE_DIR.'class.email.php');
include_once(INCLUDE_DIR.'class.csrf.php');
$info=array();
$info['subj']='Teste de e-mail OS Ticket';

if($_POST){
    $errors=array();
    $email=null;
    if(!$_POST['email_id'] || !($email=Email::lookup($_POST['email_id'])))
        $errors['email_id']='E-mail selecionado';

    if(!$_POST['email'] || !Validator::is_email($_POST['email']))
        $errors['email']='E-mail do destinatário - necessário';

    if(!$_POST['subj'])
        $errors['subj']='Assunto - necessário';

    if(!$_POST['message'])
        $errors['message']='Mensagem - necessária';

    if(!$errors && $email){
        if($email->send($_POST['email'],$_POST['subj'],$_POST['message']))
            $msg='E-mail de teste enviado com sucesso para '.Format::htmlchars($_POST['email']);
        else
            $errors['err']='Erro ao enviar o e-mail - tente novamente.';
    }elseif($errors['err']){
        $errors['err']='Erro ao enviar o e-mail - tente novamente.';
    }
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
$nav->setTabActive('emails');
require(STAFFINC_DIR.'header.inc.php');
?>
<form action="emailtest.php" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <h2>E-mail de teste</h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <em>O envio  de e-mails depende das configurações do servidor (php.ini) e / ou configuração de e-mail SMTP.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="120" class="required">
                De:
            </td>
            <td>
                <select name="email_id">
                    <option value="0">&mdash; E-mail selecionado &mdash;</option>
                    <?php
                    $sql='SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE.' email ORDER by name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$email,$name,$smtp)=db_fetch_row($res)){
                            $selected=($info['email_id'] && $id==$info['email_id'])?'selected="selected"':'';
                            if($name)
                                $email=Format::htmlchars("$name <$email>");
                            if($smtp)
                                $email.=' (SMTP)';

                            echo sprintf('<option value="%d" %s>%s</option>',$id,$selected,$email);
                        }
                    }
                    ?>
                </select>
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['email_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="120" class="required">
                Para:
            </td>
            <td>
                <input type="text" size="60" name="email" value="<?php echo $info['email']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['email']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="120" class="required">
                Assunto:
            </td>
            <td>
                <input type="text" size="60" name="subj" value="<?php echo $info['subj']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['subj']; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <em><strong>Mensagem</strong>: Mensdagem para ser enviada no e-mail.</em>&nbsp;<span class="error">*&nbsp;<?php echo $errors['message']; ?></span><br>
                <textarea name="message" cols="21" rows="10" style="width: 90%;"><?php echo $info['message']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="Enviar Mensagem">
    <input type="reset"  name="reset"  value="Limpar">
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="emails.php"'>
</p>
</form>
<?php
include(STAFFINC_DIR.'footer.inc.php');
?>
