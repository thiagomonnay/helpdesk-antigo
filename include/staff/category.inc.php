<?php
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->canManageFAQ()) die('Access Denied');
$info=array();
$qstr='';
if($category && $_REQUEST['a']!='add'){
    $title='Update Category :'.$category->getName();
    $action='update';
    $submit_text='Save Changes';
    $info=$category->getHashtable();
    $info['id']=$category->getId();
    $qstr.='&id='.$category->getId();
}else {
    $title='Add New Category';
    $action='create';
    $submit_text='Add';
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<form action="categories.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Categoria da FAQ</h2>
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
                <em>Informações Categoria: Categorias Públicas são publicadas, se existirem artigos da FAQ.</em>
            </th>
        </tr>
        <tr>
            <td width="180" class="required">Tipo de categoria:</td>
            <td>
                <input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']?'checked="checked"':''; ?>><b>Pública</b> (publicar)
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>>Privada (interna)
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['ispublic']; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div style="padding-top:3px;"><b>Nome da categoria</b>:&nbsp;<span class="faded">Nome do descritivo curto.</span></div>
                    <input type="text" size="70" name="name" value="<?php echo $info['name']; ?>">
                    &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span>
                <br>
                <div style="padding-top:5px;">
                    <b>Descrição da categoria</b>:&nbsp;<span class="faded">Resumo da categoria.</span>
                    &nbsp;
                    <font class="error">*&nbsp;<?php echo $errors['description']; ?></font></div>
                    <textarea class="richtext" name="description" cols="21" rows="12" style="width:98%;"><?php echo $info['description']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em>Notas internas&nbsp;</em>
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
    <input type="button" name="cancel" value="Cancelar" onclick='window.location.href="categories.php"'>
</p>
</form>
