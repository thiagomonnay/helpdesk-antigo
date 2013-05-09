<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');

$gmtime = Misc::gmtime();
?>
<h2>Configurações e preferências - <span>osTicket (v<?php echo $cfg->getVersion(); ?>)</span></h2>
<form action="settings.php?t=system" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="system" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Configurações do sistema & preferências</h4>
                <em><b>Configurações gerais</b>: O modo offline irá desativar interface do cliente e só permitir que os administradores façam o login para Painel de Controle Pessoal</em>
            </th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td width="220" class="required">Helpdesk Status:</td>
            <td>
                <input type="radio" name="isonline"  value="1"   <?php echo $config['isonline']?'checked="checked"':''; ?> /><b>Online</b> (Ativo)
                <input type="radio" name="isonline"  value="0"   <?php echo !$config['isonline']?'checked="checked"':''; ?> /><b>Offline</b> (Inativo)
                &nbsp;<font class="error">&nbsp;<?php echo $config['isoffline']?'osTicket offline':''; ?></font>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Helpdesk URL:</td>
            <td>
                <input type="text" size="40" name="helpdesk_url" value="<?php echo $config['helpdesk_url']; ?>">
                &nbsp;<font class="error">*&nbsp;<?php echo $errors['helpdesk_url']; ?></font></td>
        </tr>
        <tr>
            <td width="220" class="required">Helpdesk Nome/Título:</td>
            <td><input type="text" size="40" name="helpdesk_title" value="<?php echo $config['helpdesk_title']; ?>">
                &nbsp;<font class="error">*&nbsp;<?php echo $errors['helpdesk_title']; ?></font></td>
        </tr>
        <tr>
            <td width="220" class="required">Departamento Padrão:</td>
            <td>
                <select name="default_dept_id">
                    <option value="">&mdash; Selecione o Departamento Padrão &mdash;</option>
                    <?php
                    $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' WHERE ispublic=1';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id, $name) = db_fetch_row($res)){
                            $selected = ($config['default_dept_id']==$id)?'selected="selected"':''; ?>
                            <option value="<?php echo $id; ?>"<?php echo $selected; ?>> Departamento - <?php echo $name; ?></option>
                        <?php
                        }
                    } ?>
                </select>&nbsp;<font class="error">*&nbsp;<?php echo $errors['default_dept_id']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="220" class="required">Templates de E-mail Padrão:</td>
            <td>
                <select name="default_template_id">
                    <option value="">&mdash; Selecione o Template Padrão &mdash;</option>
                    <?php
                    $sql='SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_TABLE.' WHERE isactive=1 AND cfg_id='.db_input($cfg->getId()).' ORDER BY name';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while (list($id, $name) = db_fetch_row($res)){
                            $selected = ($config['default_template_id']==$id)?'selected="selected"':''; ?>
                            <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                        <?php
                        }
                    } ?>
                </select>&nbsp;<font class="error">*&nbsp;<?php echo $errors['default_template_id']; ?></font>
            </td>
        </tr>

        <tr><td>Tamanho Padrão da Página:</td>
            <td>
                <select name="max_page_size">
                    <?php
                     $pagelimit=$config['max_page_size'];
                    for ($i = 5; $i <= 50; $i += 5) {
                        ?>
                        <option <?php echo $config['max_page_size']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Nível de Logs Padrão:</td>
            <td>
                <select name="log_level">
                    <option value=0 <?php echo $config['log_level'] == 0 ? 'selected="selected"':''; ?>>Nenhum (Logs Desabilitados)</option>
                    <option value=3 <?php echo $config['log_level'] == 3 ? 'selected="selected"':''; ?>> DEBUG</option>
                    <option value=2 <?php echo $config['log_level'] == 2 ? 'selected="selected"':''; ?>> ALERTAS</option>
                    <option value=1 <?php echo $config['log_level'] == 1 ? 'selected="selected"':''; ?>> ERROS</option>
                </select>
                <font class="error">&nbsp;<?php echo $errors['log_level']; ?></font>
            </td>
        </tr>
        <tr>
            <td>Logs de Depuração:</td>
            <td>        
                <select name="log_graceperiod">
                    <option value=0 selected>Nunca guarde Logs de Depuração</option>
                    <?php
                    for ($i = 1; $i <=12; $i++) {
                        ?>
                        <option <?php echo $config['log_graceperiod']==$i?'selected="selected"':''; ?> value="<?php echo $i; ?>">
                            Depois&nbsp;<?php echo $i; ?>&nbsp;<?php echo ($i>1)?'Meses':'Mês'; ?></option>
                        <?php
                    } ?>
                </select>
            </td>
        </tr>
        <tr><td>Resetar Senha de Política:</th>
            <td>
                <select name="passwd_reset_period">
                   <option value="0"> &mdash; Nunca &mdash;</option>
                  <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo sprintf('<option value="%d" %s>%s%s</option>',
                                $i,(($config['passwd_reset_period']==$i)?'selected="selected"':''), $i>1?"A cada $i ":'', $i>1?' Meses':'Mensal');
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['passwd_reset_period']; ?></font>
            </td>
        </tr>
        <tr><td>Vincular o IP a Sessão Atual:</td>
            <td>
              <input type="checkbox" name="staff_ip_binding" <?php echo $config['staff_ip_binding']?'checked="checked"':''; ?>>
              <em>(Vincular o IP originário pessoal após o login)</em>
            </td>
        </tr>
        <tr><td>Logins Excessivos - Equipe:</td>
            <td>
                <select name="staff_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_max_logins']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select> Falha na tentaiva de login, não permitir antes de
                <select name="staff_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['staff_login_timeout']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select> minutos.
            </td>
        </tr>
        <tr><td>Tempo para Expiração de Sessão - Equipe:</td>
            <td>
              <input type="text" name="staff_session_timeout" size=6 value="<?php echo $config['staff_session_timeout']; ?>">
                O tempo máximo de inatividade em minutos, antes de um membro da equipe fazer login novamente (digite 0 para desabilitar).
            </td>
        </tr>
        <tr><td>Tentativas de Login Excedidas - Usuário:</td>
            <td>
                <select name="client_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_max_logins']==$i)?'selected="selected"':''), $i);
                    }

                    ?>
                </select> Falha na tentativa de login, não permitir depois de
                <select name="client_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>', $i,(($config['client_login_timeout']==$i)?'selected="selected"':''), $i);
                    }
                    ?>
                </select> minutos. 
            </td>
        </tr>

        <tr><td>Tempo para Expiração de Sessão- Usuário:</td>
            <td>
              <input type="text" name="client_session_timeout" size=6 value="<?php echo $config['client_session_timeout']; ?>">
                &nbsp;O tempo máximo de inatividade em minutos, antes de um usuário fazer login novamente (digite 0 para desabilitar).
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><b>Opções para data e tempo</b>: Por favor tome por referência <a href="http://php.net/date" target="_blank">o Manual PHP</a> para suporte dos parâmetros.</em>
            </th>
        </tr>
        <tr><td width="220" class="required">Formato de Tempo:</td>
            <td>
                <input type="text" name="time_format" value="<?php echo $config['time_format']; ?>">
                    &nbsp;<font class="error">*&nbsp;<?php echo $errors['time_format']; ?></font>
                    <em><?php echo Format::date($config['time_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em></td>
        </tr>
        <tr><td width="220" class="required">Formato de Data::</td>
            <td><input type="text" name="date_format" value="<?php echo $config['date_format']; ?>">
                        &nbsp;<font class="error">*&nbsp;<?php echo $errors['date_format']; ?></font>
                        <em><?php echo Format::date($config['date_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
            </td>
        </tr>
        <tr><td width="220" class="required">Formato com Data &amp; Tempo:</td>
            <td><input type="text" name="datetime_format" value="<?php echo $config['datetime_format']; ?>">
                        &nbsp;<font class="error">*&nbsp;<?php echo $errors['datetime_format']; ?></font>
                        <em><?php echo Format::date($config['datetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
            </td>
        </tr>
        <tr><td width="220" class="required">Formato com Dia, Data &amp; Tempo:</td>
            <td><input type="text" name="daydatetime_format" value="<?php echo $config['daydatetime_format']; ?>">
                        &nbsp;<font class="error">*&nbsp;<?php echo $errors['daydatetime_format']; ?></font>
                        <em><?php echo Format::date($config['daydatetime_format'], $gmtime, $config['tz_offset'], $config['enable_daylight_saving']); ?></em>
            </td>
        </tr>
        <tr><td width="220" class="required">Zona Padrão do Tempo:</td>
            <td>
                <select name="default_timezone_id">
                    <option value="">&mdash; Selecione a Zona Padrão &mdash;</option>
                    <?php
                    $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id, $offset, $tz)=db_fetch_row($res)){
                            $sel=($config['default_timezone_id']==$id)?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>GMT %s - %s</option>', $id, $sel, $offset, $tz);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">*&nbsp;<?php echo $errors['default_timezone_id']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="220">Horário de Verão :</td>
            <td>
                <input type="checkbox" name="enable_daylight_saving" <?php echo $config['enable_daylight_saving'] ? 'checked="checked"': ''; ?>>Observar horário de verão
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:250px;">
    <input class="button" type="submit" name="submit" value="Salvar Alterações">
    <input class="button" type="reset" name="reset" value="Resetar Alterações">
</p>
</form>
