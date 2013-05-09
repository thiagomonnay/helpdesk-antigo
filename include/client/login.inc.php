<?php
if(!defined('OSTCLIENTINC')) die('Acesso Negado');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);
?>
<h1>Verificar status do ticket</h1>
<p>Para visualizar o status de um ticket, digite os seguintes campos com os detalhes de login abaixo.</p>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
    <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
    <br>
    <div>
        <label for="email">Endereço de E-Mail:</label>
        <input id="email" type="text" name="lemail" size="30" value="<?php echo $email; ?>">
    </div>
    <div>
        <label for="ticketno">Ticket ID:</label>
        <input id="ticketno" type="text" name="lticket" size="16" value="<?php echo $ticketid; ?>"></td>
    </div>
    <p>
        <input class="btn" type="submit" value="Verificar Status">
    </p>
</form>
<br>
<p>
Se este é o seu primeiro contato ou você perdeu o ID do ticket, por favor <a href="open.php">abra um novo ticket</a>.    
</p>
