<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canCreateTickets()) die('Access Denied');
$info=array();
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="tickets.php?a=open" method="post" id="save"  enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="create">
 <input type="hidden" name="a" value="open">
 <h2>Abrir Novo Ticket</h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Novo ticket</h4>
                <em><strong>Informações do usuário</strong></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="160" class="required">
                Endereço de e-mail:
            </td>
            <td>

                <input type="text" size="50" name="email" id="email" class="typeahead" value="<?php echo $info['email']; ?>"
                    autocomplete="off" autocorrect="off" autocapitalize="off">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['email']; ?></span>
            <?php 
            if($cfg->notifyONNewStaffTicket()) { ?>
               &nbsp;&nbsp;&nbsp;
               <input type="checkbox" name="alertuser" <?php echo (!$errors || $info['alertuser'])? 'checked="checked"': ''; ?>>Enviar alerta para o usuário.
            <?php 
             } ?>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                Nome Completo:
            </td>
            <td>
                <input type="text" size="50" name="name" id="name" value="<?php echo $info['name']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="160">
                Fone:
            </td>
            <td>
                <input type="text" size="20" name="phone" id="phone" value="<?php echo $info['phone']; ?>">
                &nbsp;<span class="error">&nbsp;<?php echo $errors['phone']; ?></span>
                Ramal <input type="text" size="6" name="phone_ext" id="phone_ext" value="<?php echo $info['phone_ext']; ?>">
                &nbsp;<span class="error">&nbsp;<?php echo $errors['phone_ext']; ?></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Informações do ticket &amp; Opções</strong>:</em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                Origem do ticket:
            </td>
            <td>
                <select name="source">
                    <option value="" selected >&mdash; Selecione a origem &mdash;</option>
                    <option value="Phone" <?php echo ($info['source']=='Phone')?'selected="selected"':''; ?>>Fone</option>
                    <option value="Email" <?php echo ($info['source']=='Email')?'selected="selected"':''; ?>>E-mail</option>
                    <option value="Other" <?php echo ($info['source']=='Other')?'selected="selected"':''; ?>>Outros</option>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?php echo $errors['source']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                Departamento:
            </td>
            <td>
                <select name="deptId">
                    <option value="" selected >&mdash; Selecione o departamento &mdash;</option>
                    <?php
                    if($depts=Dept::getDepartments()) {
                        foreach($depts as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?php echo $errors['deptId']; ?></font>
            </td>
        </tr>

        <tr>
            <td width="160" class="required">
                Tópico de ajuda:
            </td>
            <td>
                <select name="topicId">
                    <option value="" selected >&mdash; Selecione o tópico de ajuda &mdash;</option>
                    <?php
                    if($topics=Topic::getHelpTopics()) {
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['topicId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?php echo $errors['topicId']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160">
                Prioridade:
            </td>
            <td>
                <select name="priorityId">
                    <option value="0" selected >&mdash; Padrão do sistema &mdash;</option>
                    <?php
                    if($priorities=Priority::getPriorities()) {
                        foreach($priorities as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['priorityId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['priorityId']; ?></font>
            </td>
         </tr>
         <tr>
            <td width="160">
                Plano SLA:
            </td>
            <td>
                <select name="slaId">
                    <option value="0" selected="selected" >&mdash; Padrão do sistema &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['slaId']; ?></font>
            </td>
         </tr>

         <tr>
            <td width="160">
                Data de expiração:
            </td>
            <td>
                <input class="dp" id="duedate" name="duedate" value="<?php echo Format::htmlchars($info['duedate']); ?>" size="12" autocomplete=OFF>
                &nbsp;&nbsp;
                <?php
                $min=$hr=null;
                if($info['time'])
                    list($hr, $min)=explode(':', $info['time']);

                echo Misc::timeDropdown($hr, $min, 'time');
                ?>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['duedate']; ?> &nbsp; <?php echo $errors['time']; ?></font>
                <em>O tempo é baseado na zona (GMT <?php echo $thisstaff->getTZoffset(); ?>)</em>
            </td>
        </tr>

        <?php
        if($thisstaff->canAssignTickets()) { ?>
        <tr>
            <td width="160">Atribuído para:</td>
            <td>
                <select id="assignId" name="assignId">
                    <option value="0" selected="selected">&mdash; Selecione um usuário ou uma equipe &mdash;</option>
                    <?php
                    if(($users=Staff::getAvailableStaffMembers())) {
                        echo '<OPTGROUP label="Staff Members ('.count($users).')">';
                        foreach($users as $id => $name) {
                            $k="s$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }

                    if(($teams=Team::getActiveTeams())) {
                        echo '<OPTGROUP label="Teams ('.count($teams).')">';
                        foreach($teams as $id => $name) {
                            $k="t$id";
                            echo sprintf('<option value="%s" %s>%s</option>',
                                        $k,(($info['assignId']==$k)?'selected="selected"':''),$name);
                        }
                        echo '</OPTGROUP>';
                    }
                    ?>
                </select>&nbsp;<span class='error'>&nbsp;<?php echo $errors['assignId']; ?></span>
            </td>
        </tr>
        <?php
        } ?>
        <tr>
            <th colspan="2">
                <em><strong>Questão</strong>: O usuário será capaz de ver o resumo da questão abaixo e todas as respostas associadas.</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <div>
                    <em><strong>Assunto</strong>: Resumo da edição</em> &nbsp;<font class="error">*&nbsp;<?php echo $errors['subject']; ?></font><br>
                    <input type="text" name="subject" size="60" value="<?php echo $info['subject']; ?>">
                </div>
                <div><em><strong>Questão</strong>: Detalhes sobre o motivo(s) para a abertura do ticket.</em> <font class="error">*&nbsp;<?php echo $errors['issue']; ?></font></div>
                <textarea name="issue" cols="21" rows="8" style="width:80%;"><?php echo $info['issue']; ?></textarea>
            </td>
        </tr>
        <?php
        //is the user allowed to post replies??
        if($thisstaff->canPostReply()) {
            ?>
        <tr>
            <th colspan="2">
                <em><strong>Resposta</strong>: A resposta é opcional para a questão acima.</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
            <?php
            if(($cannedResponses=Canned::getCannedResponses())) {
                ?>
                <div>
                    Repostas pré-definidas:&nbsp;
                    <select id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected">&mdash; Selecione uma reposta pré-definida &mdash;</option>
                        <?php
                        foreach($cannedResponses as $id =>$title) {
                            echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;
                    <label><input type='checkbox' value='1' name="append" id="append" checked="checked">Anexar</label>
                </div>
            <?php
            } ?>
                <textarea name="response" id="response" cols="21" rows="8" style="width:80%;"><?php echo $info['response']; ?></textarea>
                <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <?php
                if($cfg->allowAttachments()) { ?>
                    <tr><td width="100" valign="top">Anexos:</td>
                        <td>
                            <div class="canned_attachments">
                            <?php
                            if($info['cannedattachments']) {
                                foreach($info['cannedattachments'] as $k=>$id) {
                                    if(!($file=AttachmentFile::lookup($id))) continue;
                                    $hash=$file->getHash().md5($file->getId().session_id().$file->getHash());
                                    echo sprintf('<label><input type="checkbox" name="cannedattachments[]" 
                                            id="f%d" value="%d" checked="checked" 
                                            <a href="file.php?h=%s">%s</a>&nbsp;&nbsp;</label>&nbsp;',
                                            $file->getId(), $file->getId() , $hash, $file->getName());
                                }
                            }
                            ?>
                            </div>
                            <div class="uploads"></div>
                            <div class="file_input">
                                <input type="file" class="multifile" name="attachments[]" size="30" value="" />
                            </div>
                        </td>
                    </tr>
                <?php
                } ?>

            <?php
            if($thisstaff->canCloseTickets()) { ?>
                <tr>
                    <td width="100">Status do ticket:</td>
                    <td>
                        <input type="checkbox" name="ticket_state" value="closed" <?php echo $info['ticket_state']?'checked="checked"':''; ?>>
                        <b>Fechar uma resposta</b>&nbsp;<em>(Aplicável apenas se a resposta for introduzida)</em>
                    </td>
                </tr>
            <?php
            } ?>
             <tr>
                <td width="100">Assinatura:</td>
                <td>
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label><input type="radio" name="signature" value="none" checked="checked"> Nenhum</label>
                    <?php
                    if($thisstaff->getSignature()) { ?>
                        <label><input type="radio" name="signature" value="mine"
                            <?php echo ($info['signature']=='mine')?'checked="checked"':''; ?>> Minha assinatura</label>
                    <?php
                    } ?>
                    <label><input type="radio" name="signature" value="dept"
                        <?php echo ($info['signature']=='dept')?'checked="checked"':''; ?>> Assinaturea do departamento (se setado)</label>
                </td>
             </tr>
            </table>
            </td>
        </tr>
        <?php
        } //end canPostReply
        ?>
        <tr>
            <th colspan="2">
                <em><strong>Notas internas</strong>: As notas internas são opcionais (recomendado a atribuição) <font class="error">&nbsp;<?php echo $errors['note']; ?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea name="note" cols="21" rows="6" style="width:80%;"><?php echo $info['note']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:250px;">
    <input type="submit" name="submit" value="Abrir">
    <input type="reset"  name="reset"  value="Resetar">
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="tickets.php"'>
</p>
</form>
