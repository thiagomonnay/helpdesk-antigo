<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
if(!($maxfileuploads=ini_get('max_file_uploads')))
    $maxfileuploads=DEFAULT_MAX_FILE_UPLOADS;
?>
<h2>Opções e Configurações dos Tickets</h2>
<form action="settings.php?t=tickets" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="tickets" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Configurações dos Tickets</h4>
                <em>Opções e Configurações Globais dos Tickets.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td width="220" class="required">IDs dos Tickets:</td>
            <td>
                <input type="radio" name="random_ticket_ids"  value="0" <?php echo !$config['random_ticket_ids']?'checked="checked"':''; ?> />
                Sequencial
                <input type="radio" name="random_ticket_ids"  value="1" <?php echo $config['random_ticket_ids']?'checked="checked"':''; ?> />
                Aletatório  <em>(Altamente recomendável)</em>
            </td>
        </tr>

        <tr>
            <td width="180" class="required">
                SLA Padrão:
            </td>
            <td>
                <select name="default_sla_id">
                    <option value="0">&mdash; Nunca &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id => $name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id,
                                    ($config['default_sla_id'] && $id==$config['default_sla_id'])?'selected="selected"':'',
                                    $name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['default_sla_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">Prioridade Padrão:</td>
            <td>
                <select name="default_priority_id">
                    <?php
                    $priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
                    while (list($id,$tag) = db_fetch_row($priorities)){ ?>
                        <option value="<?php echo $id; ?>"<?php echo ($config['default_priority_id']==$id)?'selected':''; ?>><?php echo $tag; ?></option>
                    <?php
                    } ?>
                </select>
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['default_priority_id']; ?></span>
             </td>
        </tr>
        <tr>
            <td>Máximo Tickets <b>Abertos</b>:</td>
            <td>
                <input type="text" name="max_open_tickets" size=4 value="<?php echo $config['max_open_tickets']; ?>">
                por email/usuário. <em>(Ajuda com controle de spam e e-mail - digite 0 para ilimitado)</em>
            </td>
        </tr>
        <tr>
            <td>Tempo Automático para Fechamento dos Tickets:</td>
            <td>
                <input type="text" name="autolock_minutes" size=4 value="<?php echo $config['autolock_minutes']; ?>">
                <font class="error"><?php echo $errors['autolock_minutes']; ?></font>
                <em>(Minutos para bloquear um ticket ativo - digite 0 para desabiltar)</em>
            </td>
        </tr>
        <tr>
                    <td width="180">Prioridade dos Tickets da Web:</td>
                    <td>
                        <input type="checkbox" name="allow_priority_change" value="1" <?php echo $config['allow_priority_change'] ?'checked="checked"':''; ?>>
                        <em>(Permitir usuários setar/editar prioridades)</em>
                    </td>
                </tr>
                <tr>
                    <td width="180">Prioridade dos Tickets Enviados por E-mail:</td>
                    <td>
                        <input type="checkbox" name="use_email_priority" value="1" <?php echo $config['use_email_priority'] ?'checked="checked"':''; ?> >
                        <em>(Use a prioridade de e-mail quando disponível)</em>
            </td>
        </tr>
        <tr>
            <td width="180">Visualizar Tickets Relacionados:</td>
            <td>
                <input type="checkbox" name="show_related_tickets" value="1" <?php echo $config['show_related_tickets'] ?'checked="checked"':''; ?> >
                <em>(Visualiza todos os tickets relacionados de um usário - caso contrário o acesso é restrito por sessão)</em>
            </td>
        </tr>
        <tr>
            <td width="180">Visualizar Notas Internas:</td>
            <td>
                <input type="checkbox" name="show_notes_inline" value="1" <?php echo $config['show_notes_inline'] ?'checked="checked"':''; ?> >
                <em>(Visualiza as notas internas)</em>
              </td>
        </tr>
        <tr><td>Clickable URLs:</td>
            <td>
              <input type="checkbox" name="clickable_urls" <?php echo $config['clickable_urls']?'checked="checked"':''; ?>>
               <em>(Converte URLs em links clicáveis)</em>
            </td>
        </tr>
        <tr>
            <td>Verificação Anti-Robô:</td>
            <td>
                <input type="checkbox" name="enable_captcha" <?php echo $config['enable_captcha']?'checked="checked"':''; ?>>
                Habilita o CAPTCHA para os tickets web.<em>(é necessário a GDLib)</em> &nbsp;<font class="error">&nbsp;<?php echo $errors['enable_captcha']; ?></font><br/>
            </td>
        </tr>
        <tr>
            <td>Tickets Reabertos:</td>
            <td>
                <input type="checkbox" name="auto_assign_reopened_tickets" <?php echo $config['auto_assign_reopened_tickets']?'checked="checked"':''; ?>>
                Atribuir automaticamente tickets reabertos através de uma fila.
            </td>
        </tr>
        <tr>
            <td>Tickets Atribuídos:</td>
            <td>
                <input type="checkbox" name="show_assigned_tickets" <?php echo $config['show_assigned_tickets']?'checked="checked"':''; ?>>
                Visualizar tickets atribuídos.
            </td>
        </tr>
        <tr>
            <td>Tickets Respondidos:</td>
            <td>
                <input type="checkbox" name="show_answered_tickets" <?php echo $config['show_answered_tickets']?'checked="checked"':''; ?>>
                Visualizar tickets respondidos.
            </td>
        </tr>
        <tr>
            <td>Log de Atividades dos Tickets:</td>
            <td>
                <input type="checkbox" name="log_ticket_activity" <?php echo $config['log_ticket_activity']?'checked="checked"':''; ?>>
                Gardar atividades dos tickets como notas internas.
            </td>
        </tr>
        <tr>
            <td>Mascarando Identiade Pessoal:</td>
            <td>
                <input type="checkbox" name="hide_staff_name" <?php echo $config['hide_staff_name']?'checked="checked"':''; ?>>
                Ocultar o nome das equipes na respota
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Anexos</b>:  Tamanho máximo. Imagens de configuração se aplicam principalmente aos bilhetes web.</em>
            </th>
        </tr>
        <tr>
            <td width="180">Permitir Anexos:</td>
            <td>
              <input type="checkbox" name="allow_attachments" <?php echo $config['allow_attachments']?'checked="checked"':''; ?>><b>Permitir anexos</b>
                &nbsp; <em>(Configurações globais)</em>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['allow_attachments']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Anexos de Emais/API:</td>
            <td>
                <input type="checkbox" name="allow_email_attachments" <?php echo $config['allow_email_attachments']?'checked="checked"':''; ?>> Permitir anexos de email/API.
                    &nbsp;<font class="error">&nbsp;<?php echo $errors['allow_email_attachments']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Online/Anexos Web:</td>
            <td>
                <input type="checkbox" name="allow_online_attachments" <?php echo $config['allow_online_attachments']?'checked="checked"':''; ?> >
                   Permitir upload web &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="allow_online_attachments_onlogin" <?php echo $config['allow_online_attachments_onlogin'] ?'checked="checked"':''; ?> >
                    Limitar somente à usuários autenticados. <em>(Só usuários logados podem fazer upload)</em>
                    <font class="error">&nbsp;<?php echo $errors['allow_online_attachments']; ?></font>
            </td>
        </tr>
        <tr>
            <td>Quantidade Máxima de Arquivos po Usuário:</td>
            <td>
                <select name="max_user_file_uploads">
                    <?php
                    for($i = 1; $i <=$maxfileuploads; $i++) {
                        ?>
                        <option <?php echo $config['max_user_file_uploads']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            <?php echo $i; ?>&nbsp;<?php echo ($i>1)?'files':'file'; ?></option>
                        <?php
                    } ?>
                </select>
                <em>(Número de arquivos que o usuário tem permissão para enviar simultaneamente)</em>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['max_user_file_uploads']; ?></font>
            </td>
        </tr>
        <tr>
            <td>Quantidade Máxima de Arquivos por Equipe:</td>
            <td>
                <select name="max_staff_file_uploads">
                    <?php
                    for($i = 1; $i <=$maxfileuploads; $i++) {
                        ?>
                        <option <?php echo $config['max_staff_file_uploads']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            <?php echo $i; ?>&nbsp;<?php echo ($i>1)?'files':'file'; ?></option>
                        <?php
                    } ?>
                </select>
                <em>(Número de arquivos que a equipe tem permissão para fazer upload simultaneamente)</em>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['max_staff_file_uploads']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Tamanho Máximo do Arquivo:</td>
            <td>
                <input type="text" name="max_file_size" value="<?php echo $config['max_file_size']; ?>"> em bytes.
                    <em>(Tamanho máximo do sistema. <?php echo Format::file_size(ini_get('upload_max_filesize')); ?>)</em>
                    <font class="error">&nbsp;<?php echo $errors['max_file_size']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Arquivo de Resposta dos Tickets:</td>
            <td>
                <input type="checkbox" name="email_attachments" <?php echo $config['email_attachments']?'checked="checked"':''; ?> >Enviar e-mail anexados ao usuário
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Aceitar os Seguintes Tipos de Arquivos</strong>: Limitar os tipos de arquivos que os usuários estão autorizados a enviar.
                <font class="error">&nbsp;<?php echo $errors['allowed_filetypes']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <em>Digite as extensões dos arquivos permitidos  e separadas por uma vírgula. Ex.: doc,. pdf. Para aceitar todos os arquivos digitar caractere curinga<b><i>.*</i></b>&nbsp; (Não Recomendável).</em><br>
                <textarea name="allowed_filetypes" cols="21" rows="4" style="width: 65%;" wrap="hard" ><?php echo $config['allowed_filetypes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:250px;">
    <input class="button" type="submit" name="submit" value="Salvar Alterações">
    <input class="button" type="reset" name="reset" value="Resetar Alterações">
</p>
</form>

