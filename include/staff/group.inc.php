<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
$info=array();
$qstr='';
if($group && $_REQUEST['a']!='add'){
    $title='Update Group';
    $action='update';
    $submit_text='Save Changes';
    $info=$group->getInfo();
    $info['id']=$group->getId();
    $info['depts']=$group->getDepartments();
    $qstr.='&id='.$group->getId();
}else {
    $title='Add New Group';
    $action='create';
    $submit_text='Create Group';
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['can_create_tickets']=isset($info['can_create_tickets'])?$info['can_create_tickets']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="groups.php?<?php echo $qstr; ?>" method="post" id="save" name="group">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Grupo de Usuários</h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong>Informações do grupo</strong>:Destiva os grupos de usuários. Administradores são isentos</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
                Nome:
            </td>
            <td>
                <input type="text" size="30" name="name" value="<?php echo $info['name']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Status:
            </td>
            <td>
                <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong>Habilitar</strong>
                <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><strong>Desabilitar</strong>
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['status']; ?></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Permissões do grupo</strong>: Aplica-se a todos os membros do grupo&nbsp;</em>
            </th>
        </tr>
        <tr><td>Pode <b>Criar</b> Tickets</td>
            <td>
                <input type="radio" name="can_create_tickets"  value="1"   <?php echo $info['can_create_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_create_tickets"  value="0"   <?php echo !$info['can_create_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade de abrir tickets em nome de clientes.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Editar</b> Tickets</td>
            <td>
                <input type="radio" name="can_edit_tickets"  value="1"   <?php echo $info['can_edit_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_edit_tickets"  value="0"   <?php echo !$info['can_edit_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para editar tickets.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Reponder Mensagens</b></td>
            <td>
                <input type="radio" name="can_post_ticket_reply"  value="1"   <?php echo $info['can_post_ticket_reply']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_post_ticket_reply"  value="0"   <?php echo !$info['can_post_ticket_reply']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para responder mensagens.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Fechar</b> Tickets</td>
            <td>
                <input type="radio" name="can_close_tickets"  value="1" <?php echo $info['can_close_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_close_tickets"  value="0" <?php echo !$info['can_close_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para fechar tickets. A equipe ainda pode postar uma resposta.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Atribuir</b> Tickets</td>
            <td>
                <input type="radio" name="can_assign_tickets"  value="1" <?php echo $info['can_assign_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_assign_tickets"  value="0" <?php echo !$info['can_assign_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para atribuir tickets para menbros das equipes.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Transferir</b> Tickets</td>
            <td>
                <input type="radio" name="can_transfer_tickets"  value="1" <?php echo $info['can_transfer_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_transfer_tickets"  value="0" <?php echo !$info['can_transfer_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade de transferência de tickets entre os departamentos.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Deletar</b> Tickets</td>
            <td>
                <input type="radio" name="can_delete_tickets"  value="1"   <?php echo $info['can_delete_tickets']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_delete_tickets"  value="0"   <?php echo !$info['can_delete_tickets']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade de apagar tickets (tickets excluídos não podem ser recuperados!)</i>
            </td>
        </tr>
        <tr><td>Pode <b>Banir</b> Emails</td>
            <td>
                <input type="radio" name="can_ban_emails"  value="1" <?php echo $info['can_ban_emails']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_ban_emails"  value="0" <?php echo !$info['can_ban_emails']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade de adicionar / remover e-mails da banlist via interface do ticket.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Gerenciar</b> Premade</td>
            <td>
                <input type="radio" name="can_manage_premade"  value="1" <?php echo $info['can_manage_premade']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_manage_premade"  value="0" <?php echo !$info['can_manage_premade']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para adic/atua/desab/deletar respostas e anexos pré-determindos.</i>
            </td>
        </tr>
        <tr><td>Pode <b>Gerenciar</b> FAQ</td>
            <td>
                <input type="radio" name="can_manage_faq"  value="1" <?php echo $info['can_manage_faq']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_manage_faq"  value="0" <?php echo !$info['can_manage_faq']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para adic/atua/desab/deletar categorias e FAQs da base de conhecimento (KB).</i>
            </td>
        </tr>
        <tr><td>Pode <b>Visulaizar</b> Estatísticas do Usuário.</td>
            <td>
                <input type="radio" name="can_view_staff_stats"  value="1" <?php echo $info['can_view_staff_stats']?'checked="checked"':''; ?> />Sim
                &nbsp;&nbsp;
                <input type="radio" name="can_view_staff_stats"  value="0" <?php echo !$info['can_view_staff_stats']?'checked="checked"':''; ?> />Não
                &nbsp;&nbsp;<i>Capacidade para ver as estatísticas de outros usuários permitidos no departamento.</i>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Acessar Departamentos</strong>: Verifica todos os membros do departamentos e grupo que tem permissão para acessar.&nbsp;&nbsp;&nbsp;<a id="selectAll" href="#deptckb">Marcar tudo</a>&nbsp;&nbsp;<a id="selectNone" href="#deptckb">Desmarcar</a>&nbsp;&nbsp;</em>
            </th>
        </tr>
        <?php
         $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name';
         if(($res=db_query($sql)) && db_num_rows($res)){
            while(list($id,$name) = db_fetch_row($res)){
                $ck=($info['depts'] && in_array($id,$info['depts']))?'checked="checked"':'';
                echo sprintf('<tr><td colspan=2>&nbsp;&nbsp;<input type="checkbox" class="deptckb" name="depts[]" value="%d" %s>%s</td></tr>',$id,$ck,$name);
            }
         }
        ?>
        <tr>
            <th colspan="2">
                <em><strong>Notas Adminstrativas</strong>: Notas internas podem ser visualizadas por todos os administradores.&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea name="notes" cols="21" rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  value="Resetar">
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="groups.php"'>
</p>
</form>
