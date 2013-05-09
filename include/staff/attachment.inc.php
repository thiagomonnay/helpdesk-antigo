<?php
if(!defined('OSTADMININC') || !$thisstaff->isAdmin()) die('Access Denied');
//Get the config info.
$config=($errors && $_POST)?Format::input($_POST):$cfg->getConfigInfo();
?>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
    <form action="admin.php?t=attach" method="post">
    <input type="hidden" name="t" value="attach">
    <tr>
      <td>
        <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
          <tr class="header">
            <td colspan=2>&nbsp;Configurações de Anexos</td>
          </tr>
          <tr class="subheader">
            <td colspan=2">
                Antes de ativar anexos certifique-se de compreender as configurações de segurança e questões relacionadas com o arquivo uploads.</td>
          </tr>
          <tr>
            <th width="165">Permitir anexos:</th>
            <td>
              <input type="checkbox" name="allow_attachments" <?php echo $config['allow_attachments'] ?'checked':''; ?>><b>Permitir Anexos</b>
                &nbsp; (<i>Configurações Globais</i>)
                &nbsp;<font class="error">&nbsp;<?php echo $errors['allow_attachments']; ?></font>
            </td>
          </tr>
          <tr>
            <th>Anexos enviados por email:</th>
            <td>
                <input type="checkbox" name="allow_email_attachments" <?php echo $config['allow_email_attachments'] ? 'checked':''; ?> > Aceitar arquivos enviados por email
                    &nbsp;<font class="warn">&nbsp;<?php echo $warn['allow_email_attachments']; ?></font>
            </td>
          </tr>
         <tr>
            <th>Anexos online:</th>
            <td>
                <input type="checkbox" name="allow_online_attachments" <?php echo $config['allow_online_attachments'] ?'checked':''; ?> >
                   Permitir o envio de anexos on-line<br/>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="allow_online_attachments_onlogin" <?php echo $config['allow_online_attachments_onlogin'] ?'checked':''; ?> >
                    Apenas usuários autenticados. (<i>O usuário deve estar logado para fazer upload de arquivos</i>)
                    <font class="warn">&nbsp;<?php echo $warn['allow_online_attachments']; ?></font>
            </td>
          </tr>
          <tr>
            <th>Arquivos de resposta do funcionário:</th>
            <td>
                <input type="checkbox" name="email_attachments" <?php echo $config['email_attachments']?'checked':''; ?> >Enviar anexo por e-mail ao usuário
            </td>
          </tr>
          <tr>
            <th nowrap>Tamanho máximo de arquivo:</th>
            <td>
              <input type="text" name="max_file_size" value="<?php echo $config['max_file_size']; ?>"> <i>bytes</i>
                <font class="error">&nbsp;<?php echo $errors['max_file_size']; ?></font>
            </td>
          </tr>
          <tr>
            <th>Pasta de anexos:</th>
            <td>
                Usuário da Web (por exemplo, o Apache) deve ter acesso de gravação para a pasta. &nbsp;<font class="error">&nbsp;<?php echo $errors['upload_dir']; ?></font><br>
              <input type="text" size=60 name="upload_dir" value="<?php echo $config['upload_dir']; ?>"> 
              <font color=red>
              <?php echo $attwarn; ?>
              </font>
            </td>
          </tr>
          <tr>
            <th valign="top"><br/>Tipo de arquivos aceitos:</th>
            <td>
                Digite as extensões de arquivos permitidos separadas por uma vírgula. por exemplo: <i>.doc, .pdf, </i> <br>
                Para aceitar todos os arquivos digite<b><i>.*</i></b>&nbsp;&nbsp;(não recomendado).
                <textarea name="allowed_filetypes" cols="21" rows="4" style="width: 65%;" wrap=HARD ><?php echo $config['allowed_filetypes']; ?></textarea>
            </td>
          </tr>
        </table>
    </td></tr>
    <tr><td style="padding:10px 0 10px 200px">
        <input class="button" type="submit" name="submit" value="Salvar Alterações">
        <input class="button" type="reset" name="reset" value="Resetar Alterações">
    </td></tr>
  </form>
</table>
