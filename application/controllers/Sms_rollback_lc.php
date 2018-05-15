<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_rollback_lc extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class($this));
        $this->controller_url=strtolower(get_class($this));
        $this->load->config('table_sms');
        $this->lang->load('sms');
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="LC List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=100;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $this->db->from($this->config->item('table_sms_lc_open').' lc');
        $this->db->select('lc.*');
        $this->db->order_by('lc.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }


            $this->db->from($this->config->item('table_sms_lc_open').' lco');
            $this->db->select('lco.*');
            $this->db->where('lco.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='LC Not Found.';
                $this->json_return($ajax);
            }

            if($data['item']['status_open']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='LC already deleted.';
                $this->json_return($ajax);
            }
            if($data['item']['status_open']==$this->config->item('system_status_complete'))
            {
                $data['item']['message']='Current Status : LC Completed <br /> New Status: Received';
            }
            elseif($data['item']['status_receive']==$this->config->item('system_status_complete'))
            {
                $data['item']['message']='Current Status : Received <br /> New Status: Receive Pending';
            }
            elseif($data['item']['status_release']==$this->config->item('system_status_complete'))
            {
                $data['item']['message']='Current Status : Released (Receive Pending)<br /> New Status: Release Pending';
            }
            elseif($data['item']['status_open_forward']==$this->config->item('system_status_yes'))
            {
                $data['item']['message']='Current Status : Forwarded (Release Pending) <br /> New Status: LC Open';
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='Nothing to rollback.';
                $this->json_return($ajax);
            }

            $data['title']='LC Rollback';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_id = $this->input->post("id");
        $item_head=$this->input->post('item');
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!($item_head['reason']))
        {
            $ajax['status']=false;
            $ajax['system_message']='Reason field is required.';
            $this->json_return($ajax);
        }
        if($item_head['rollback']!=$this->config->item('system_status_yes'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Did not select anything.';
            $this->json_return($ajax);
        }
        $this->db->from($this->config->item('table_sms_lc_open').' lco');
        $this->db->select('lco.*');
        $this->db->where('lco.id',$item_id);
        $item=$this->db->get()->row_array();
        if(!$item)
        {
            $ajax['status']=false;
            $ajax['system_message']='LC Not Found.';
            $this->json_return($ajax);
        }

        if($item['status_open']==$this->config->item('system_status_delete'))
        {
            $ajax['status']=false;
            $ajax['system_message']='LC already deleted.';
            $this->json_return($ajax);
        }
        if($item['status_receive']==$this->config->item('system_status_complete'))
        {
            // checking current stock negative
            $lc_details=Query_helper::get_info($this->config->item('table_sms_lc_details'),'*',array('lc_id='.$item_id,'quantity_open > 0'));
            $variety_ids=array();
            foreach($lc_details as $lc_detail)
            {
                $variety_ids[$lc_detail['variety_id']]=$lc_detail['variety_id'];
            }
            $current_stocks=Stock_helper::get_variety_stock($variety_ids);

            foreach($lc_details as $lc_detail)
            {
                if($lc_detail['quantity_receive']>$current_stocks[$lc_detail['variety_id']][$lc_detail['pack_size_id']][$lc_detail['receive_warehouse_id']]['current_stock'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']='New stock will be negative.';
                    $this->json_return($ajax);
                }
            }
        }

        /* remarks massage */
        if($item['status_open']==$this->config->item('system_status_complete'))
        {
            $remarks="Current Status : LC Completed \n New Status: Received";
        }
        elseif($item['status_receive']==$this->config->item('system_status_complete'))
        {
            $remarks="Current Status : Received \n New Status: Receive Pending";
        }
        elseif($item['status_release']==$this->config->item('system_status_complete'))
        {
            $remarks="Current Status : Released (Receive Pending)\n New Status: Release Pending";
        }
        elseif($item['status_open_forward']==$this->config->item('system_status_yes'))
        {
            $remarks="Current Status : Forwarded (Release Pending) \n New Status: LC Open";
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']='Nothing to rollback.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($item['status_open']==$this->config->item('system_status_complete'))
        {
            // Rollback closed to receive.
            // Set status status_open = system_status_active
            $data=array();
            $data['status_open']=$this->config->item('system_status_active');
            Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$item_id));

            $data=array();
            $data['site'] = 'SMS_2018_19';
            $data['reference_id'] = $item_id;
            $data['controller_name'] = $this->controller_url;
            $data['field_name'] = 'status_open';
            $data['current_status'] = $this->config->item('system_status_complete');
            $data['new_status']=$this->config->item('system_status_active');
            $data['old_data']=json_encode($item);
            $data['remarks']=$remarks;
            $data['reason']=$item_head['reason'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
        }
        elseif($item['status_receive']==$this->config->item('system_status_complete'))
        {
            // Rollback receive complete to receive pending.
            // Set status status_receive = system_status_pending
            // Rollback summery stock
            $data=array();
            $data['status_receive']=$this->config->item('system_status_pending');
            Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$item_id));
            // current stock update
            foreach($lc_details as $lc_detail)
            {
                $data=array();
                $data['current_stock']=($current_stocks[$lc_detail['variety_id']][$lc_detail['pack_size_id']][$lc_detail['receive_warehouse_id']]['current_stock']-$lc_detail['quantity_receive']);
                $data['in_lc']=($current_stocks[$lc_detail['variety_id']][$lc_detail['pack_size_id']][$lc_detail['receive_warehouse_id']]['in_lc']-$lc_detail['quantity_receive']);
                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$lc_detail['variety_id'],'pack_size_id='.$lc_detail['pack_size_id'],'warehouse_id='.$lc_detail['receive_warehouse_id']));
            }

            $data=array();
            $data['site'] = 'SMS_2018_19';
            $data['reference_id'] = $item_id;
            $data['controller_name'] = $this->controller_url;
            $data['field_name'] = 'status_receive';
            $data['current_status'] = $this->config->item('system_status_complete');
            $data['new_status']=$this->config->item('system_status_pending');
            $data['old_data']=json_encode($item);
            $data['remarks']=$remarks;
            $data['reason']=$item_head['reason'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
        }
        elseif($item['status_release']==$this->config->item('system_status_complete'))
        {
            // Rollback release complete to release pending.
            // Set status status_release = system_status_pending
            $data=array();
            $data['status_release']=$this->config->item('system_status_pending');
            Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$item_id));

            $data=array();
            $data['site'] = 'SMS_2018_19';
            $data['reference_id'] = $item_id;
            $data['controller_name'] = $this->controller_url;
            $data['field_name'] = 'status_release';
            $data['current_status'] = $this->config->item('system_status_complete');
            $data['new_status']=$this->config->item('system_status_pending');
            $data['old_data']=json_encode($item);
            $data['remarks']=$remarks;
            $data['reason']=$item_head['reason'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
        }
        elseif($item['status_open_forward']==$this->config->item('system_status_yes'))
        {
            // Rollback lc forwarded to forward option.
            // Set status status_open_forward = system_status_no
            $data=array();
            $data['status_open_forward']=$this->config->item('system_status_no');
            Query_helper::update($this->config->item('table_sms_lc_open'),$data,array('id='.$item_id));

            $data=array();
            $data['site'] = 'SMS_2018_19';
            $data['reference_id'] = $item_id;
            $data['controller_name'] = $this->controller_url;
            $data['field_name'] = 'status_open_forward';
            $data['current_status'] = $this->config->item('system_status_yes');
            $data['new_status']=$this->config->item('system_status_no');
            $data['old_data']=json_encode($item);
            $data['remarks']=$remarks;
            $data['reason']=$item_head['reason'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
        }
        else
        {

        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_sms_lc_open').' lco');
            $this->db->select('lco.*');
            $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id = lco.fiscal_year_id','INNER');
            $this->db->select('fy.name fiscal_year');
            $this->db->join($this->config->item('table_login_setup_currency').' currency','currency.id = lco.currency_id','INNER');
            $this->db->select('currency.name currency_name');
            $this->db->join($this->config->item('table_login_basic_setup_principal').' principal','principal.id = lco.principal_id','INNER');
            $this->db->select('principal.name principal_name');
            $this->db->join($this->config->item('table_login_setup_bank_account').' ba','ba.id = lco.bank_account_id','INNER');
            $this->db->join($this->config->item('table_login_setup_bank').' bank','bank.id = ba.bank_id','INNER');
            $this->db->select("CONCAT_WS(' ( ',ba.account_number,  CONCAT_WS('', bank.name,' - ',ba.branch_name,')')) bank_account_number");
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_forward','ui_forward.user_id = lco.user_open_forward','LEFT');
            $this->db->select('ui_forward.name forward_user_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_release_completed','ui_release_completed.user_id = lco.user_release_updated','LEFT');
            $this->db->select('ui_release_completed.name release_completed_user_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_receive_completed','ui_receive_completed.user_id = lco.user_receive_updated','LEFT');
            $this->db->select('ui_receive_completed.name receive_completed_user_full_name');
            $this->db->join($this->config->item('table_login_setup_user_info').' ui_expense_completed','ui_expense_completed.user_id = lco.user_expense_completed','LEFT');
            $this->db->select('ui_expense_completed.name expense_completed_user_full_name');
            $this->db->where('lco.id',$item_id);
            $this->db->where('lco.status_open !=',$this->config->item('system_status_delete'));
            $data['item']=$this->db->get()->row_array();

            if(!$data['item'])
            {
                System_helper::invalid_try('View Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid LC.';
                $this->json_return($ajax);
            }

            $this->db->from($this->config->item('table_sms_lc_details').' lcd');
            $this->db->select('lcd.*');
            $this->db->select('pack.name pack_size');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = lcd.variety_id','INNER');
            $this->db->select('v.id variety_id, v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_variety_principals').' vp','vp.variety_id = v.id AND vp.principal_id = '.$data['item']['principal_id'].' AND vp.revision = 1','INNER');
            $this->db->select('vp.name_import variety_name_import');
            $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id = lcd.pack_size_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','LEFT');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','LEFT');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_login_basic_setup_warehouse').' warehouse','warehouse.id = lcd.receive_warehouse_id','LEFT');
            $this->db->select('warehouse.name warehouse_name');
            $this->db->where('lcd.lc_id',$item_id);
            $this->db->where('lcd.quantity_open >0');
            $this->db->order_by('lcd.id ASC');
            $data['items']=$this->db->get()->result_array();

            $this->db->from($this->config->item('table_sms_lc_expense').' lce');
            $this->db->select('lce.*');
            $this->db->join($this->config->item('table_login_setup_direct_cost_items').' dci','dci.id=lce.dc_id','INNER');
            $this->db->select('dci.name dc_name');
            $this->db->where('lce.lc_id',$item_id);
            $data['dc_items']=$this->db->get()->result_array();

            $results=Query_helper::get_info($this->config->item('table_sms_lc_expense_varieties'),'*',array('lc_id ='.$item_id),0,0,array(''));
            $dc_expenses_varieties=array();
            foreach($results as $result)
            {
                $dc_expenses_varieties[$result['variety_id']][$result['pack_size_id']][$result['dc_id']]=$result;
            }
            $data['dc_expense_varieties']=$dc_expenses_varieties;

            $data['title']="LC Details";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=true;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

}
