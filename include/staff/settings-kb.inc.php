<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin() || !$config) die('Access Denied');
?>
<h2>Configurações e Opções da Base de Conhecimentos</h2>
<form action="settings.php?t=kb" method="post" id="save">
<?php csrf_token(); ?>
<input type="hidden" name="t" value="kb" >
<table class="form_table settings_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Configurações da Base de Conhecimentos</h4>
                <em>Desabilitar a base de conhecimentos para interface dos clientes.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180">Status da KB:</td>
            <td>
              <input type="checkbox" name="enable_kb" value="1" <?php echo $config['enable_kb']?'checked="checked"':''; ?>>
              Habilitar&nbsp;<em>(Interface do Cliente)</em>
              &nbsp;<font class="error">&nbsp;<?php echo $errors['enable_kb']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="180">Respostas predeterminadas:</td>
            <td>
                <input type="checkbox" name="enable_premade" value="1" <?php echo $config['enable_premade']?'checked="checked"':''; ?> >
                Habilitar respostas predeterminadas&nbsp;<em>(Disponível nas respostas dos bilhetes)</em>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['enable_premade']; ?></font>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:210px;">
    <input class="button" type="submit" name="submit" value="Salvar Alterações">
    <input class="button" type="reset" name="reset" value="Resetar Alteracções">
</p>
</form>
