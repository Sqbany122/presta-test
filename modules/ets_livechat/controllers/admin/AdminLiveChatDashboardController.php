<?php
/**
 * 2007-2019 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2019 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;
class AdminLiveChatDashboardController extends ModuleAdminController
{
    public $errors= array();
    public function __construct()
    {
       parent::__construct();
       $this->bootstrap = true;
       $this->context = Context::getContext();
       if($this->context->employee->id_profile!=1)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminLiveChatTickets'));
    }
    public function initContent()
    {
        $data_conversations=array();
        $data_receive_messages=array();
        $data_replied_messages=array();
        $data_tickets=array();
        $label_datas=array();
        $data_open_tickets=array();
        $data_close_tickets=array();
        $submit=false;
        $recently_customers= array();
        $count_recently_customers=0;
        $year_min_conversation = Db::getInstance()->getValue('SELECT MIN(YEAR(datetime_added)) FROM '._DB_PREFIX_.'ets_livechat_conversation');
        $year_min_ticket= Db::getInstance()->getValue('SELECT MIN(YEAR(date_add)) FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message');
        $year_min = $year_min_conversation < $year_min_ticket ? $year_min_conversation : $year_min_ticket;
        if(Tools::isSubmit('submitDateWeek') || Tools::getValue('actionSubmitChart')=='submitDateWeek')
        {
            $date =date('Y-m-d');
            $dayofweek = date('w', strtotime($date));
            if($dayofweek==0)
               $dayofweek=6;
            else
                $dayofweek--; 
            $days = array($this->l('Mon'), $this->l('Tue'), $this->l('Wed'),$this->l('Thu'),$this->l('Fri'), $this->l('Sat'),$this->l('Sun'));        
            for($day=0; $day < 7; $day++)
            {
                $label_datas[]=$days[$day];
                $datetime = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date)));
                $data_tickets[]= $this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00"');
                $data_open_tickets[]=$this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00" AND status="open"');
                $data_close_tickets[]=$this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00" AND status="close"');
                $data_conversations[]=$this->module->getCountConversation(' AND datetime_added <="'.pSQL($datetime).' 23:59:59" AND datetime_added >="'.pSQL($datetime).' 00:00:00"');
                $data_receive_messages[] = $this->module->getCountMessage(' AND m.id_employee=0 AND m.datetime_added <="'.pSQL($datetime).' 23:59:59" AND m.datetime_added >="'.pSQL($datetime).' 00:00:00"');
                $data_replied_messages[]= $this->module->getCountMessage(' AND m.id_employee!=0 AND m.datetime_added <="'.pSQL($datetime).' 23:59:59" AND m.datetime_added >="'.pSQL($datetime).' 00:00:00"');
            }
            $recently_customers = $this->getCustomerLoginSocial(false,' AND s.date_login <="'.pSQL(date('Y-m-d', strtotime((6 - (int)$dayofweek).' day', strtotime($date)))).' 23:59:59" AND s.date_login >="'.pSQL(date('Y-m-d', strtotime((0 - (int)$dayofweek).' day', strtotime($date)))).' 00:00:00"',0,5);
            $count_recently_customers = $this->getCustomerLoginSocial(true,' AND s.date_login <="'.pSQL(date('Y-m-d', strtotime((6 - (int)$dayofweek).' day', strtotime($date)))).' 23:59:59" AND s.date_login >="'.pSQL(date('Y-m-d', strtotime((0 - (int)$dayofweek).' day', strtotime($date)))).' 00:00:00"');
            $submit=true;
        }
        elseif(Tools::isSubmit('submitDateMonth') || Tools::getValue('actionSubmitChart')=='submitDateMonth')
        {
            $month = date('m');
            $year= date('Y');
            $days = function_exists('cal_days_in_month') ? cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year) : (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year));
            if($days)
            {
                for($day=1; $day<=$days;$day++)
                {
                    $label_datas[]=$day;
                    $data_conversations[]=$this->module->getCountConversation(' AND YEAR(datetime_added)="'.(int)$year.'" AND MONTH(datetime_added)="'.(int)$month.'" AND DAY(datetime_added)="'.(int)$day.'"');
                    $data_receive_messages[]= $this->module->getCountMessage(' AND m.id_employee=0 AND YEAR(m.datetime_added)="'.(int)$year.'" AND MONTH(m.datetime_added)="'.(int)$month.'" AND DAY(m.datetime_added)="'.(int)$day.'"');
                    $data_replied_messages[]= $this->module->getCountMessage(' AND m.id_employee!=0 AND YEAR(m.datetime_added)="'.(int)$year.'" AND MONTH(m.datetime_added)="'.(int)$month.'" AND DAY(m.datetime_added)="'.(int)$day.'"');
                    $data_tickets[]=$this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$month.'" AND DAY(date_add)="'.(int)$day.'"');
                    $data_open_tickets[]=$this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$month.'" AND DAY(date_add)="'.(int)$day.'" AND status="open"');
                    $data_close_tickets[]=$this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$month.'" AND DAY(date_add)="'.(int)$day.'" AND status="close"');
                }
            }
            $recently_customers = $this->getCustomerLoginSocial(false,' AND YEAR(s.date_login)="'.(int)$year.'" AND MONTH(s.date_login) = "'.(int)$month.'"',0,5);
            $count_recently_customers = $this->getCustomerLoginSocial(true,' AND YEAR(s.date_login)="'.(int)$year.'" AND MONTH(s.date_login) = "'.(int)$month.'"');
            $submit=true;
        }
        elseif(Tools::isSubmit('submitDateYear') || Tools::getValue('actionSubmitChart')=='submitDateYear' || ((Tools::isSubmit('submitDateAll') || Tools::getValue('actionSubmitChart')=='submitDateAll') && $year_min == date('Y')) )
        {
            $year= date('Y');
            $months=Tools::dateMonths();
            foreach($months as $key=>$month)
            {
                $label_datas[]=$key;
                $data_conversations[]=$this->module->getCountConversation(' AND YEAR(datetime_added)="'.(int)$year.'" AND MONTH(datetime_added)="'.(int)$key.'"');
                $data_receive_messages[] =$this->module->getCountMessage(' AND m.id_employee=0 AND YEAR(m.datetime_added)="'.(int)$year.'" AND MONTH(m.datetime_added)="'.(int)$key.'"');
                $data_replied_messages[] =$this->module->getCountMessage(' AND m.id_employee!=0 AND YEAR(m.datetime_added)="'.(int)$year.'" AND MONTH(m.datetime_added)="'.(int)$key.'"');
                $data_tickets[]=$this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$key.'"');
                $data_open_tickets= $this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$key.'" AND status="open"');
                $data_close_tickets = $this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND MONTH(date_add)="'.(int)$key.'" AND status="close"');
            }
            $recently_customers = $this->getCustomerLoginSocial(false,' AND YEAR(s.date_login)="'.(int)$year.'"',0,5);
            $count_recently_customers = $this->getCustomerLoginSocial(true,' AND YEAR(s.date_login)="'.(int)$year.'"');
            $submit=true;
        }
        elseif(Tools::isSubmit('submitDateAll') || Tools::getValue('actionSubmitChart')=='submitDateAll')
        {
            for($year = $year_min; $year<=date('Y');$year++)
            {
                $label_datas[]=$year;
                $data_conversations[]=$this->module->getCountConversation(' AND YEAR(datetime_added)="'.(int)$year.'"');
                $data_receive_messages[] =$this->module->getCountMessage(' AND m.id_employee=0 AND YEAR(m.datetime_added)="'.(int)$year.'"');
                $data_replied_messages[] =$this->module->getCountMessage(' AND m.id_employee!=0 AND YEAR(m.datetime_added)="'.(int)$year.'"');
                $data_tickets[]=$this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'"');
                $data_open_tickets= $this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND status="open"');
                $data_close_tickets = $this->getCountTicket(' AND YEAR(date_add)="'.(int)$year.'" AND status="close"');
            }
            $recently_customers = $this->getCustomerLoginSocial(false,' AND YEAR(s.date_login)="'.(int)$year.'"',0,5);
            $count_recently_customers = $this->getCustomerLoginSocial(true,' AND YEAR(s.date_login)="'.(int)$year.'"');
            $submit=true;
        }
        if($submit && Tools::getValue('ajax'))
        {
            die(
                Tools::jsonEncode(
                    array(
                        'data_conversations' =>array($data_conversations,$data_receive_messages,$data_replied_messages),
                        'data_tickets'=>array($data_tickets,$data_open_tickets,$data_close_tickets),
                        'label_datas' => $label_datas,
                        'recently_customer' => $this->module->displayRecentlyCustomer($count_recently_customers,$recently_customers),
                    )
                )
            );
        }
        parent::initContent();
    }
    public function renderList() 
    {
        $data_conversations=array();
        $data_receive_messages=array();
        $data_replied_messages=array();
        $data_tickets=array();
        $data_open_tickets=array();
        $data_close_tickets=array();
        $chart_labels = array();
        $date =date('Y-m-d');
        $dayofweek = date('w', strtotime($date));
        if($dayofweek==0)
           $dayofweek=6;
        else
            $dayofweek--; 
        $days = array($this->l('Mon'), $this->l('Tue'), $this->l('Wed'),$this->l('Thu'),$this->l('Fri'), $this->l('Sat'),$this->l('Sun'));        
        for($day=0; $day<7; $day++)
        {
            $chart_labels[]= $days[$day];
            $datetime = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date)));
            $data_tickets[]=$this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00"');
            $data_open_tickets[]=$this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00" AND status="open"');
            $data_close_tickets[] = $this->getCountTicket(' AND date_add <= "'.pSQL($datetime).' 23:59:59" AND date_add >="'.pSQL($datetime).' 00:00:00" AND status="close"');
            $data_conversations[]= $this->module->getCountConversation(' AND datetime_added <="'.pSQL($datetime).' 23:59:59" AND datetime_added >="'.pSQL($datetime).' 00:00:00"');
            $data_receive_messages[]=  $this->module->getCountMessage(' AND m.id_employee=0 AND m.datetime_added <= "'.pSQL($datetime).' 23:59:59" AND m.datetime_added >= "'.pSQL($datetime).' 00:00:00"');
            $data_replied_messages[]= $this->module->getCountMessage(' AND m.id_employee!=0 AND m.datetime_added <= "'.pSQL($datetime).' 23:59:59" AND m.datetime_added >= "'.pSQL($datetime).' 00:00:00"');
        }
        $login_customers= $this->getCustomerLoginSocial(false,' AND s.date_login <="'.pSQL(date('Y-m-d', strtotime((6 - (int)$dayofweek).' day', strtotime($date)))).' 23:59:59" AND s.date_login >="'.pSQL(date('Y-m-d', strtotime((0 - (int)$dayofweek).' day', strtotime($date)))).' 00:00:00"',0,5);
        $conversation_datasets=array(
            array(
                'label'=> $this->l('Conversations'),
                'data' =>$data_conversations,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                //'lineTension'=> 1
            ),
            array(
                'label'=> $this->l('Received messages'),
                'data' =>$data_receive_messages,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                //'lineTension'=> 1
            ),
            array(
                'label'=> $this->l('Replied messages'),
                'data' =>$data_replied_messages,
                'backgroundColor'=>'rgba(139,195,72,0.3)',
                'borderColor'=>'rgba(139,195,72,1)',
                'borderWidth'=>1,
                'pointRadius' => 2,
                //'lineTension'=> 1
            )
        );
        $ticket_datasets =array(
            array(
                'label'=> $this->l('Received tickets'),
                'data' =>$data_tickets,
                'backgroundColor'=>'rgba(163,225,212,0.3)',
                'borderColor'=>'rgba(163,225,212,1)',
                'fill'=>true,
                'borderWidth'=>1,
                'pointRadius' => 2,
                //'lineTension'=> 1
            ),
            array(
                'label'=> $this->l('Open tickets'),
                'data' =>$data_open_tickets,
                'backgroundColor'=>'rgba(253,193,7,0.3)',
                'borderColor'=>'rgba(253,193,7,1)',
                //'lineTension'=> 1,
                'borderWidth'=>1,
                'pointRadius' => 2,
                'fill'=>true,
            ),
            array(
                'label'=> $this->l('Solved tickets'),
                'data' =>$data_close_tickets,
                'backgroundColor'=>'rgba(139,195,72,0.3)',
                'borderColor'=>'rgba(139,195,72,1)',
                //'lineTension'=> 1,
                'borderWidth'=>1,
                'pointRadius' => 2,
                'fill'=>true,
            )
        );  
        $count_login_customers = $this->getCustomerLoginSocial(true,' AND s.date_login <="'.pSQL(date('Y-m-d', strtotime((6 - (int)$dayofweek).' day', strtotime($date)))).' 23:59:59" AND s.date_login >="'.pSQL(date('Y-m-d', strtotime((0 - (int)$dayofweek).' day', strtotime($date)))).' 00:00:00"');
        $this->context->smarty->assign(
            array(
                'menu_top' => $this->module->displayMenuTop(),
                'ETS_LC_MODULE_URL' => $this->module->url_module,
                'countConversation'=> $this->module->getCountConversation(),
                'countConversationInMonth' => $this->module->getCountConversation(' AND datetime_added >="'.pSQL(date('Y-m-01')).' 00:00:00" AND datetime_added <= "'.pSQL(date('Y-m-d')).' 23:59:59"'),
                'countReceivedTicket' => $this->getCountTicket(),
                'countMessages'=>  $this->module->getCountMessage(false),
                'countMessagesInMonth'=>$this->module->getCountMessage(' AND m.datetime_added >= "'.pSQL(date('Y-m-01')).' 00:00:00" AND m.datetime_added <= "'.pSQL(date('Y-m-d')).' 23:59:59"'),
                'countReceivedTicketInMonth' => $this->getCountTicket(' AND date_add >="'.pSQL(date('Y-m-01')).' 00:00:00" AND date_add <= "'.pSQL(date('Y-m-d')).' 23:59:59"'),
                'countOpenTicket' => $this->getCountTicket(' AND t.status="open"'),
                'countOpenTicketInMonth' => $this->getCountTicket(' AND t.status="open" AND date_add >="'.pSQL(date('Y-m-01')).' 00:00:00" AND date_add <= "'.pSQL(date('Y-m-d')).' 23:59:59"'),
                'countSolvedTicket' => $this->getCountTicket(' AND t.status="close"'),
                'countSolvedTicketInMouth' => $this->getCountTicket(' AND t.status="close" AND date_add >="'.pSQL(date('Y-m-01')).' 00:00:00" AND date_add <= "'.pSQL(date('Y-m-d')).' 23:59:59"'),
                'action' => $this->context->link->getAdminLink('AdminLiveChatDashboard'),
                'conversation_datasets'=>$conversation_datasets,  
                'ticket_datasets' => $ticket_datasets,
                'recentlyConversations' => $this->getRecentlyConversation(),
                'recentlyTickets' => $this->getRecentlyTicket(),
                'active_staffs' => $this->getStaff(),
                'chart_labels' => $chart_labels, 
                'recently_customer' => $this->module->displayRecentlyCustomer($count_login_customers,$login_customers),   
                'ETS_DISPLAY_DASHBOARD_ONLY' => Configuration::get('ETS_DISPLAY_DASHBOARD_ONLY'),                
            )
        );
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'dashboard.tpl');
    }
    public function getCountTicket($filter=false)
    {
        $sql ='SELECT COUNT(t.id_message) FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message t WHERE 1'.(!$this->module->all_shop ? ' AND id_shop='.(int)$this->context->shop->id:'').($filter ? $filter:'');
        return Db::getInstance()->getValue($sql);
    }
    public function getRecentlyConversation()
    {
        $sql = '
            SELECT  lc.*,CONCAT(c.firstname," ",c.lastname) as fullname,c.email FROM '._DB_PREFIX_.'ets_livechat_conversation lc
            INNER JOIN '._DB_PREFIX_.'ets_livechat_message m ON (lc.id_conversation=m.id_conversation)
            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer=lc.id_customer)
            JOIN (SELECT MAX(m2.id_message) max_id_message,lc2.customer_email
            FROM '._DB_PREFIX_.'ets_livechat_conversation lc2
            INNER JOIN '._DB_PREFIX_.'ets_livechat_message m2 ON (lc2.id_conversation=m2.id_conversation) 
            WHERE 1 '.(!$this->module->all_shop ? ' AND lc2.id_shop="'.(int)$this->context->shop->id.'"':'').' AND  m2.id_employee=0
            GROUP BY lc2.customer_email) ty ON (ty.max_id_message=m.id_message)
            WHERE 1 '.(!$this->module->all_shop ? ' AND lc.id_shop="'.(int)$this->context->shop->id.'"':'').'
            ORDER BY ty.max_id_message DESC
            LIMIT 0,5';
        $conversations= Db::getInstance()->executeS($sql);
        if($conversations)
        {
            foreach($conversations as &$conversation)
            {
                $conversation['last_message'] = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_conversation="'.(int)$conversation['id_conversation'].'" AND id_employee=0 ORDER BY id_message DESC');
                if($conversation['last_message'])
                {
                    $conversation['last_message']['datetime_added'] = $this->module->convertDate($conversation['last_message']['datetime_added']);
                    if($this->module->emotions)
                    {
                        foreach($this->module->emotions as $key=> $emotion)
                        {
                            $img = '<span title="'.$emotion['title'].'"><img src="'.$this->module->url_module.'views/img/emotions/'.$emotion['img'].'"></span>';
                            $conversation['last_message']['message'] = str_replace(array(Tools::strtolower($key),$key),array($img,$img),$conversation['last_message']['message']);
                        }
                    }
                }
                $conversation['avatar'] = $this->module->getAvatarCustomer($conversation['id_customer']);
            }
        }
        return $conversations;
    }
    public function getRecentlyTicket()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE 1 '.(!$this->module->all_shop ? ' AND id_shop="'.(int)$this->context->shop->id.'"':'').' ORDER BY id_message DESC LIMIT 0,5';
        $tickets= Db::getInstance()->executeS($sql);
        if($tickets)
        {
            foreach($tickets as &$ticket)
            {
                $ticket['customer'] = $this->module->getEmailCustomer($ticket['id_message']);
                $ticket['date_add']= $this->module->convertDate($ticket['date_add']);
                $ticket['avatar'] = $this->module->getAvatarCustomer($ticket['id_customer']);
            }
        }
        return $tickets;
    }
    public function getStaff()
    {
        $sql = 'SELECT e.*,count(DISTINCT lc.id_conversation) as total_conversation, count(DISTINCT fm.id_message) as total_ticket,FORMAT(count(DISTINCT lc.id_conversation) +count(DISTINCT fm.id_message),0) as total_suport FROM '._DB_PREFIX_.'employee e
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_message m ON (e.id_employee=m.id_employee)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_conversation lc ON (lc.id_conversation = m .id_conversation '.(!$this->module->all_shop ? ' AND lc.id_shop="'.(int)$this->context->shop->id.'"':'').' )
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message_note fmn ON (e.id_employee= fmn.id_employee)
        LEFT JOIN '._DB_PREFIX_.'ets_livechat_ticket_form_message fm ON (fmn.id_message=fm.id_message '.(!$this->module->all_shop ? ' AND fm.id_shop ="'.(int)$this->context->shop->id.'"':'').' AND fm.status="close")
        GROUP BY e.id_employee ORDER BY total_suport DESC';
        $staffs = Db::getInstance()->executeS($sql);
        if($staffs)
        {
            foreach($staffs as &$staff)
            {
                //AVG(lc.rating) as tb_rate_conversation , AVG(fm.rate) as tb_rate_ticket
                $rate_conversation = Db::getInstance()->getRow('SELECT SUM(lc.rating) as total_rating,COUNT(lc.id_conversation) as total FROM '._DB_PREFIX_.'ets_livechat_conversation lc WHERE rating >0 '.(!$this->module->all_shop ? ' AND id_shop="'.(int)$this->context->shop->id.'"':'').' AND id_conversation IN (SELECT id_conversation FROM '._DB_PREFIX_.'ets_livechat_message WHERE id_employee="'.(int)$staff['id_employee'].'")');
                $rate_ticket= Db::getInstance()->getRow('SELECT SUM(rate) as total_rate, COUNT(id_message) as total FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message WHERE rate >0 '.(!$this->module->all_shop ? ' AND id_shop= "'.(int)$this->context->shop->id.'"':'').' AND id_message IN (SELECT id_message FROM '._DB_PREFIX_.'ets_livechat_ticket_form_message_note WHERE id_employee="'.(int)$staff['id_employee'].'")');
                $staff['avg_rate']=$rate_conversation['total'] || $rate_ticket['total'] ?  Tools::ps_round((float)($rate_conversation['total_rating']+$rate_ticket['total_rate'])/(float)($rate_conversation['total']+$rate_ticket['total']),1):0;
                $floor_rate =floor($staff['avg_rate']);
                $staff['du'] = $staff['avg_rate']*10 - $floor_rate*10;
                $staff['avatar'] = $this->module->getAvatarEmployee($staff['id_employee']);
            }
        }
        return $staffs;
    }
    public function getCustomerLoginSocial($count=false,$filter=false,$start=0,$limit=0)
    {
        $sql = 'SELECT '.($count? 'count(*)':'*').' FROM '._DB_PREFIX_.'ets_livechat_social_login s
            LEFT JOIN '._DB_PREFIX_.'customer c ON (s.id_customer=c.id_customer)
            WHERE 1 '.(!$this->module->all_shop ? ' AND c.id_shop="'.(int)$this->context->shop->id.'"':'').($filter ? $filter :'').' ORDER BY date_login desc'.($limit ? ' LIMIT '.(int)$start.','.(int)$limit:'');
        if($count)
            return Db::getInstance()->getValue($sql);
        else
        {
            $customers = Db::getInstance()->executeS($sql);
            if($customers)
            {
                foreach($customers as &$customer)
                {
                    $customer['date_login'] = $this->module->convertDate($customer['date_login']);
                    $customer['social'] = Tools::strtolower($customer['social']);
                }
            }
            return $customers;
        }
        
    }
}