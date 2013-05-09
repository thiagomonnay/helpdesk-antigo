<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canManageFAQ()) die('Access Denied');
$info=array();
$qstr='';
if($faq){
    $title='Update FAQ: '.$faq->getQuestion();
    $action='update';
    $submit_text='Save Changes';
    $info=$faq->getHashtable();
    $info['id']=$faq->getId();
    $info['topics']=$faq->getHelpTopicsIds();
    $qstr='id='.$faq->getId();
}else {
    $title='Add New FAQ';
    $action='create';
    $submit_text='Add FAQ';
    if($category) {
        $qstr='cid='.$category->getId();
        $info['category_id']=$category->getId();
    }
}
//TODO: Add attachment support.
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="faq.php?<?php echo $qstr; ?>" method="post" id="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>FAQ</h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em>Informações da FAQ</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <div style="padding-top:3px;"><b>Pergunta</b>&nbsp;<span class="error">*&nbsp;<?php echo $errors['question']; ?></span></div>
                    <input type="text" size="70" name="question" value="<?php echo $info['question']; ?>">
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Lista de categorias</b>:&nbsp;<span class="faded">A pergunta pertence a categoria da FAQ.</span></div>
                <select name="category_id" style="width:350px;">
                    <option value="0">Selecione a categoria da FAQ </option>
                    <?php
                    $sql='SELECT category_id, name, ispublic FROM '.FAQ_CATEGORY_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while($row=db_fetch_array($res)) {
                            echo sprintf('<option value="%d" %s>%s (%s)</option>',
                                    $row['category_id'],
                                    (($info['category_id']==$row['category_id'])?'selected="selected"':''),
                                    $row['name'],
                                    ($info['ispublic']?'Public':'Internal'));
                        }
                    }
                   ?>
                </select>
                <span class="error">*&nbsp;<?php echo $errors['category_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Tipo de lista</b>:&nbsp;
                    <span class="faded">Perguntas publicadas estão listadas na base de conhecimento pública, se a categoria pai é pública.</span></div>
                <input type="radio" name="ispublished" value="1" <?php echo $info['ispublished']?'checked="checked"':''; ?>>Público (publicar)
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="ispublished" value="0" <?php echo !$info['ispublished']?'checked="checked"':''; ?>>Interno (privado)
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['ispublished']; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div>
                    <b>Resposta</b>&nbsp;<font class="error">*&nbsp;<?php echo $errors['answer']; ?></font></div>
                    <textarea name="answer" cols="21" rows="12" style="width:98%;" class="richtext"><?php echo $info['answer']; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Anexos</b> (opcional) <font class="error">&nbsp;<?php echo $errors['files']; ?></font></div>
                <?php
                if($faq && ($files=$faq->getAttachments())) {
                    echo '<div class="faq_attachments"><span class="faded">Uncheck to delete the attachment on submit</span><br>';
                    foreach($files as $file) {
                        $hash=$file['hash'].md5($file['id'].session_id().$file['hash']);
                        echo sprintf('<label><input type="checkbox" name="files[]" id="f%d" value="%d" checked="checked">
                                      <a href="file.php?h=%s">%s</a>&nbsp;&nbsp;</label>&nbsp;',
                                      $file['id'], $file['id'], $hash, $file['name']);
                    }
                    echo '</div><br>';
                }
                ?>
                <div class="faded">Selecione os arquivos para upload.</div>
                <div class="uploads"></div>
                <div class="file_input">
                    <input type="file" class="multifile" name="attachments[]" size="30" value="" />
                </div>
            </td>
        </tr>
        <?php
        $sql='SELECT ht.topic_id, CONCAT_WS(" / ", pht.topic, ht.topic) as name '
            .' FROM '.TOPIC_TABLE.' ht '
            .' LEFT JOIN '.TOPIC_TABLE.' pht ON(pht.topic_id=ht.topic_pid) ';
        if(($res=db_query($sql)) && db_num_rows($res)) { ?>
        <tr>
            <th colspan="2">
                <em><strong>Tópicos de ajuda</strong>: Verifique todos os tópicos de ajuda relacionados a FAQ.</em>
            </th>
        </tr>
        <tr><td>
            <?php
            while(list($topicId,$topic)=db_fetch_row($res)) {
                echo sprintf('<input type="checkbox" name="topics[]" value="%d" %s>%s<br>',
                        $topicId,
                        (($info['topics'] && in_array($topicId,$info['topics']))?'checked="checked"':''),
                        $topic);
            }
             ?>
            </td>
        </tr>
        <?php
        } ?>
        <tr>
            <th colspan="2">
                <em><strong>Notas Internas</strong>: &nbsp;</em>
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
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="faq.php?<?php echo $qstr; ?>"'>
</p>
</form>
