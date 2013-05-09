<?php
/*********************************************************************
    class.nav.php

    Navigation helper classes. Pointless BUT helps keep navigation clean and free from errors.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class StaffNav {
    var $tabs=array();
    var $submenus=array();

    var $activetab;
    var $activemenu;
    var $panel;

    var $staff;

    function StaffNav($staff, $panel='staff'){
        $this->staff=$staff;
        $this->panel=strtolower($panel);
        $this->tabs=$this->getTabs();
        $this->submenus=$this->getSubMenus();
    }

    function getPanel(){
        return $this->panel;
    }

    function isAdminPanel(){
        return (!strcasecmp($this->getPanel(),'admin'));
    }

    function isStaffPanel() {
        return (!$this->isAdminPanel());
    }

    function setTabActive($tab, $menu=''){

        if($this->tabs[$tab]){
            $this->tabs[$tab]['active']=true;
            if($this->activetab && $this->activetab!=$tab && $this->tabs[$this->activetab])
                 $this->tabs[$this->activetab]['active']=false;

            $this->activetab=$tab;
            if($menu) $this->setActiveSubMenu($menu, $tab);

            return true;
        }

        return false;
    }

    function setActiveTab($tab, $menu=''){
        return $this->setTabActive($tab, $menu);
    }

    function getActiveTab(){
        return $this->activetab;
    }

    function setActiveSubMenu($mid, $tab='') {
        if(is_numeric($mid))
            $this->activeMenu = $mid;
        elseif($mid && $tab && ($subNav=$this->getSubNav($tab))) {
            foreach($subNav as $k => $menu) {
                if(strcasecmp($mid, $menu['href'])) continue;

                $this->activeMenu = $k+1;
                break;
            }
        }
    }

    function getActiveMenu() {
        return $this->activeMenu;
    }

    function addSubMenu($item,$active=false){

        $this->submenus[$this->getPanel().'.'.$this->activetab][]=$item;
        if($active)
            $this->activeMenu=sizeof($this->submenus[$this->getPanel().'.'.$this->activetab]);
    }


    function getTabs(){

        if(!$this->tabs) {
            $this->tabs=array();
            $this->tabs['dashboard']=array('desc'=>'Dashboard','href'=>'dashboard.php','title'=>'Staff Dashboard');
            $this->tabs['tickets']=array('desc'=>'Tickets','href'=>'tickets.php','title'=>'Ticket Queue');
            $this->tabs['kbase']=array('desc'=>'KB-Informações','href'=>'kb.php','title'=>'Knowledgebase');
        }

        return $this->tabs;
    }

    function getSubMenus(){ //Private.

        $staff = $this->staff;
        $submenus=array();
        foreach($this->getTabs() as $k=>$tab){
            $subnav=array();
            switch(strtolower($k)){
                case 'tickets':
                    $subnav[]=array('desc'=>'Tickets','href'=>'tickets.php','iconclass'=>'Ticket', 'droponly'=>true);
                    if($staff) {
                        if(($assigned=$staff->getNumAssignedTickets()))
                            $subnav[]=array('desc'=>"Meus&nbsp;Tickets ($assigned)",
                                            'href'=>'tickets.php?status=assigned',
                                            'iconclass'=>'assignedTickets',
                                            'droponly'=>true);

                        if($staff->canCreateTickets())
                            $subnav[]=array('desc'=>'Novo&nbsp;Ticket',
                                            'href'=>'tickets.php?a=open',
                                            'iconclass'=>'newTicket',
                                            'droponly'=>true);
                    }
                    break;
                case 'dashboard':
                    $subnav[]=array('desc'=>'Dashboard','href'=>'dashboard.php','iconclass'=>'logs');
                    $subnav[]=array('desc'=>'Diretório de&nbsp;Usuários','href'=>'directory.php','iconclass'=>'teams');
                    $subnav[]=array('desc'=>'Meu&nbsp;Perfil','href'=>'profile.php','iconclass'=>'users');
                    break;
                case 'kbase':
                    $subnav[]=array('desc'=>'FAQs','href'=>'kb.php', 'urls'=>array('faq.php'), 'iconclass'=>'kb');
                    if($staff) {
                        if($staff->canManageFAQ())
                            $subnav[]=array('desc'=>'Categorias','href'=>'categories.php','iconclass'=>'faq-categories');
                        if($staff->canManageCannedResponses())
                            $subnav[]=array('desc'=>'Respostas&nbsp;Pré-Estabelecidas','href'=>'canned.php','iconclass'=>'canned');
                    }
                   break;
            }
            if($subnav)
                $submenus[$this->getPanel().'.'.strtolower($k)]=$subnav;
        }

        return $submenus;
    }

    function getSubMenu($tab=null){
        $tab=$tab?$tab:$this->activetab;
        return $this->submenus[$this->getPanel().'.'.$tab];
    }

    function getSubNav($tab=null){
        return $this->getSubMenu($tab);
    }

}

class AdminNav extends StaffNav{

    function AdminNav($staff){
        parent::StaffNav($staff, 'admin');
    }

    function getTabs(){


        if(!$this->tabs){

            $tabs=array();
            $tabs['dashboard']=array('desc'=>'Dashboard','href'=>'logs.php','title'=>'Admin Dashboard');
            $tabs['settings']=array('desc'=>'Configurações','href'=>'settings.php','title'=>'System Settings');
            $tabs['manage']=array('desc'=>'Gerenciar','href'=>'helptopics.php','title'=>'Manage Options');
            $tabs['emails']=array('desc'=>'E-mails','href'=>'emails.php','title'=>'Email Settings');
            $tabs['staff']=array('desc'=>'Usuários','href'=>'staff.php','title'=>'Manage Staff');
            $this->tabs=$tabs;
        }

        return $this->tabs;
    }

    function getSubMenus(){

        $submenus=array();
        foreach($this->getTabs() as $k=>$tab){
            $subnav=array();
            switch(strtolower($k)){
                case 'dashboard':
                    $subnav[]=array('desc'=>'System&nbsp;Logs','href'=>'logs.php','iconclass'=>'logs');
                    break;
                case 'settings':
                    $subnav[]=array('desc'=>'Preferências&nbsp;do Sistema','href'=>'settings.php?t=system','iconclass'=>'preferences');
                    $subnav[]=array('desc'=>'Tickets','href'=>'settings.php?t=tickets','iconclass'=>'ticket-settings');
                    $subnav[]=array('desc'=>'E-mails','href'=>'settings.php?t=emails','iconclass'=>'email-settings');
                    $subnav[]=array('desc'=>'KB-Informações','href'=>'settings.php?t=kb','iconclass'=>'kb-settings');
                    $subnav[]=array('desc'=>'Auto-Respostas','href'=>'settings.php?t=autoresp','iconclass'=>'email-autoresponders');
                    $subnav[]=array('desc'=>'Alertas&nbsp;&amp;&nbsp;Notícias','href'=>'settings.php?t=alerts','iconclass'=>'alert-settings');
                    break;
                case 'manage':
                    $subnav[]=array('desc'=>'Tópicos de&nbsp;Ajuda','href'=>'helptopics.php','iconclass'=>'helpTopics');
                    $subnav[]=array('desc'=>'Filtros&nbsp;do Ticket','href'=>'filters.php',
                                        'title'=>'Ticket&nbsp;Filters','iconclass'=>'ticketFilters');
                    $subnav[]=array('desc'=>'Planos de&nbsp;SLA','href'=>'slas.php','iconclass'=>'sla');
                    $subnav[]=array('desc'=>'Chaves de&nbsp;API','href'=>'apikeys.php','iconclass'=>'api');
                    break;
                case 'emails':
                    $subnav[]=array('desc'=>'E-mails','href'=>'emails.php', 'title'=>'Email Addresses', 'iconclass'=>'emailSettings');
                    $subnav[]=array('desc'=>'Lista de Banição','href'=>'banlist.php',
                                        'title'=>'Banned&nbsp;Emails','iconclass'=>'emailDiagnostic');
                    $subnav[]=array('desc'=>'Templates','href'=>'templates.php','title'=>'Email Templates','iconclass'=>'emailTemplates');
                    $subnav[]=array('desc'=>'Diagnósticos','href'=>'emailtest.php', 'title'=>'Email Diagnostic', 'iconclass'=>'emailDiagnostic');
                    break;
                case 'staff':
                    $subnav[]=array('desc'=>'Membros das&nbsp;Equipes','href'=>'staff.php','iconclass'=>'users');
                    $subnav[]=array('desc'=>'Equipes','href'=>'teams.php','iconclass'=>'teams');
                    $subnav[]=array('desc'=>'Grupos','href'=>'groups.php','iconclass'=>'groups');
                    $subnav[]=array('desc'=>'Departamentos','href'=>'departments.php','iconclass'=>'departments');
                    break;
            }
            if($subnav)
                $submenus[$this->getPanel().'.'.strtolower($k)]=$subnav;
        }

        return $submenus;
    }
}

class UserNav {

    var $navs=array();
    var $activenav;

    var $user;

    function UserNav($user=null, $active=''){

        $this->user=$user;
        $this->navs=$this->getNavs();
        if($active)
            $this->setActiveNav($active);
    }

    function setActiveNav($nav){

        if($nav && $this->navs[$nav]){
            $this->navs[$nav]['active']=true;
            if($this->activenav && $this->activenav!=$nav && $this->navs[$this->activenav])
                 $this->navs[$this->activenav]['active']=false;

            $this->activenav=$nav;

            return true;
        }

        return false;
    }

    function getNavLinks(){
        global $cfg;

        //Paths are based on the root dir.
        if(!$this->navs){

            $navs = array();
            $user = $this->user;
            $navs['home']=array('desc'=>'Início&nbsp;da Página&nbsp;osTicket','href'=>'index.php','title'=>'');
            if($cfg && $cfg->isKnowledgebaseEnabled())
                $navs['kb']=array('desc'=>'KB-Informações','href'=>'kb/index.php','title'=>'');

            $navs['new']=array('desc'=>'Abrir&nbsp;Novo&nbsp;Ticket','href'=>'open.php','title'=>'');
            if($user && $user->isValid()) {
                if($cfg && $cfg->showRelatedTickets()) {
                    $navs['tickets']=array('desc'=>sprintf('Meus&nbsp;Tickets&nbsp;(%d)',$user->getNumTickets()),
                                           'href'=>'tickets.php',
                                            'title'=>'Show all tickets');
                } else {
                    $navs['tickets']=array('desc'=>'Acompanhar&nbsp;Ticket',
                                           'href'=>sprintf('tickets.php?id=%d',$user->getTicketID()),
                                           'title'=>'View ticket status');
                }
            } else {
                $navs['status']=array('desc'=>'Verificar Status do Ticket','href'=>'view.php','title'=>'');
            }
            $this->navs=$navs;
        }

        return $this->navs;
    }

    function getNavs(){
        return $this->getNavLinks();
    }

}

?>
