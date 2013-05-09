    </div>
    <div id="footer">
        Copyright &copy; 2006-<?php echo date('Y'); ?>&nbsp;osTicket.com. &nbsp;Todos os direitos reservados. :: Versão Adaptada AETC-JP ::
    </div>
<?php
if(is_object($thisstaff) && $thisstaff->isStaff()) { ?>
    <div>
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        <img src="autocron.php" alt="" width="1" height="1" border="0" />
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
    </div>
<?php
} ?>
</div>
<div id="overlay"></div>
<div id="loading">
    <h4>Por favor aguarde!</h4>
    <p>Por favor aguarde... pode demorar alguns segundos!</p>
</div>
</body>
</html>
