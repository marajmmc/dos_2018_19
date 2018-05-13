<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_lc_rollback extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sms_lc_rollback');
        $this->controller_url='sms_lc_rollback';
        $this->load->config('table_sms');
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

}
