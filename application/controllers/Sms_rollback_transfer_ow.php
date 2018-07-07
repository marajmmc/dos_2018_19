<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_rollback_transfer_ow extends Root_Controller
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
            $data['title']="Outlet to HQ Transfer All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list');
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
            $pagesize=1000;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
        $this->db->select(
            '
            transfer_ow.id,
            transfer_ow.date_request,
            transfer_ow.quantity_total_request_kg quantity_total_request,
            transfer_ow.quantity_total_approve_kg quantity_total_approve,
            transfer_ow.quantity_total_receive_kg quantity_total_receive,
            transfer_ow.status, transfer_ow.status_request,
            transfer_ow.status_approve,
            transfer_ow.status_delivery,
            transfer_ow.status_receive,
            transfer_ow.status_receive_forward,
            transfer_ow.status_receive_approve,
            transfer_ow.status_system_delivery_receive
            ');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
        $this->db->select('outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
        $this->db->select('districts.name district_name');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->select('territories.name territory_name');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->select('zones.name zone_name');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
        $this->db->select('divisions.name division_name');
        $this->db->where('outlet_info.revision',1);
        $this->db->order_by('transfer_ow.id','DESC');
        $this->db->limit($pagesize,$current_records);
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['barcode']=$result['id'];
            $item['outlet_name']=$result['outlet_name'];
            $item['date_request']=System_helper::display_date($result['date_request']);
            $item['outlet_code']=$result['outlet_code'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['quantity_total_request']=number_format($result['quantity_total_request'],3,'.','');
            $item['quantity_total_approve']=number_format($result['quantity_total_approve'],3,'.','');
            $item['quantity_total_receive']=number_format($result['quantity_total_receive'],3,'.','');
            $item['status']=$result['status'];
            $item['status_request']=$result['status_request'];
            $item['status_approve']=$result['status_approve'];
            $item['status_delivery']=$result['status_delivery'];
            $item['status_receive']=$result['status_receive'];
            $item['status_receive_forward']=$result['status_receive_forward'];
            $item['status_receive_approve']=$result['status_receive_approve'];
            $item['status_system_delivery_receive']=$result['status_system_delivery_receive'];
            if($result['status_approve']==$this->config->item('system_status_rejected'))
            {
                $item['status_delivery']='';
                $item['status_receive']='';
                $item['status_receive_forward']='';
                $item['status_receive_approve']='';
                $item['status_system_delivery_receive']='';
            }
            if($result['status_system_delivery_receive']==$this->config->item('system_status_yes'))
            {
                $item['status_receive_forward']='';
                $item['status_receive_approve']='';
            }
            $items[]=$item;
        }
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

            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->where('transfer_ow.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet to HQ transfer order not found.';
                $this->json_return($ajax);
            }

            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet to HQ transfer order already deleted.';
                $this->json_return($ajax);
            }

            if($data['item']['status_request']==$this->config->item('system_status_forwarded'))
            {
                if($data['item']['status_approve']==$this->config->item('system_status_approved'))
                {
                    if($data['item']['status_delivery']==$this->config->item('system_status_delivered'))
                    {
                        if($data['item']['status_receive']==$this->config->item('system_status_received') && $data['item']['status_system_delivery_receive']==$this->config->item('system_status_yes'))
                        {
                            $data['item']['message']='Current Status : Outlet to HQ transfer received. <br /> New Status: Receive pending. {normal receive: HQ Stock In (reverse)}';
                            // HQ Stock In (reverse) & check outlet stock available
                        }
                        else
                        {
                            if($data['item']['status_receive_forward']==$this->config->item('system_status_forwarded') && $data['item']['status_receive_approve']==$this->config->item('system_status_pending'))
                            {
                                $data['item']['message']='Current Status : Outlet to HQ transfer receive forwarded. <br /> New Status: Receive forward pending.';
                            }
                            elseif($data['item']['status_receive_forward']==$this->config->item('system_status_forwarded') && $data['item']['status_receive_approve']==$this->config->item('system_status_approved'))
                            {
                                $data['item']['message']='Current Status : Outlet to HQ transfer receive approved. <br /> New Status: Receive approve pending. {HQ Stock In (reverse)}';
                                // HQ Stock In (reverse) & check outlet stock available
                            }
                            else
                            {
                                $data['item']['message']='Current Status : Outlet to HQ transfer delivered. <br /> New Status: Delivery pending. {Outlet Stock out (reverse) & outlet stock available checking}';
                                // Outlet Stock out (reverse) & outlet stock available checking
                            }
                        }
                    }
                    else
                    {
                        $data['item']['message']='Current Status : Outlet to HQ transfer approved. <br /> New Status: Approve pending.';
                    }
                }
                else
                {
                    if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
                    {
                        $data['item']['message']='Current Status : Outlet to HQ transfer rejected. <br /> New Status: Approve pending.';
                    }
                    else
                    {
                        $data['item']['message']='Current Status : Outlet to HQ transfer forwarded. <br /> New Status: Request forwarded pending.';
                    }
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']='Nothing to rollback.';
                $this->json_return($ajax);
            }

            $data['title']='Outlet to HQ Transfer Rollback';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
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


        $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
        $this->db->select('transfer_ow.*');
        $this->db->where('transfer_ow.id',$item_id);
        $item=$this->db->get()->row_array();
        if(!$item)
        {
            $ajax['status']=false;
            $ajax['system_message']='Outlet to HQ transfer order not found.';
            $this->json_return($ajax);
        }

        if($item['status']==$this->config->item('system_status_delete'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Outlet to HQ transfer order already deleted.';
            $this->json_return($ajax);
        }
        /* remarks massage */
        $remarks='';
        if($item['status_request']==$this->config->item('system_status_forwarded'))
        {
            if($item['status_approve']==$this->config->item('system_status_approved'))
            {
                if($item['status_delivery']==$this->config->item('system_status_delivered'))
                {
                    if($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes'))
                    {
                        $remarks="Current Status : Outlet to HQ transfer received. \n New Status: Receive pending. {normal receive: Outlet Stock Out (reverse) & check outlet stock available}";
                        // Outlet Stock Out (reverse)
                        $results=Query_helper::get_info($this->config->item('table_sms_transfer_ow_details'),'*',array('transfer_ow_id='.$item_id,"status='".$this->config->item('system_status_active')."'"));
                        $variety_ids=array();
                        foreach($results as $result)
                        {
                            $variety_ids[$result['variety_id']]=$result['variety_id'];
                        }
                        $current_stocks=Stock_helper::get_variety_stock($variety_ids);
                        foreach($results as $result)
                        {
                            if(!isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]) || !($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['current_stock'])>$result['quantity_receive'])
                            {
                                $ajax['status']=false;
                                $ajax['system_message']='HQ stock will be negative.';
                                $this->json_return($ajax);
                            }
                        }
                    }
                    else
                    {
                        if($item['status_receive_forward']==$this->config->item('system_status_forwarded') && $item['status_receive_approve']==$this->config->item('system_status_pending'))
                        {
                            $remarks="Current Status : Outlet to HQ transfer receive forwarded. \n New Status: Receive forward pending.";
                        }
                        elseif($item['status_receive_forward']==$this->config->item('system_status_forwarded') && $item['status_receive_approve']==$this->config->item('system_status_approved'))
                        {
                            $remarks="Current Status : Outlet to HQ transfer receive approved. \n New Status: Receive approve pending. {Outlet Stock Out (reverse) & check outlet stock available}";
                            // Outlet Stock Out (reverse)
                            $results=Query_helper::get_info($this->config->item('table_sms_transfer_ow_details'),'*',array('transfer_ow_id='.$item_id,"status='".$this->config->item('system_status_active')."'"));
                            $variety_ids=array();
                            foreach($results as $result)
                            {
                                $variety_ids[$result['variety_id']]=$result['variety_id'];
                            }
                            $current_stocks=Stock_helper::get_variety_stock($variety_ids);
                            foreach($results as $result)
                            {
                                if(!isset($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]) || !($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['current_stock'])>$result['quantity_receive'])
                                {
                                    $ajax['status']=false;
                                    $ajax['system_message']='HQ stock will be negative.';
                                    $this->json_return($ajax);
                                }
                            }
                        }
                        else
                        {
                            $remarks="Current Status : Outlet to HQ transfer delivered. \n New Status: Delivery pending. {Head office Stock reverse}";
                            // Head office Stock reverse
                        }
                    }
                }
                else
                {
                    $remarks="Current Status : Outlet to HQ transfer approved. \n New Status: Approve pending.";
                }
            }
            else
            {
                if($item['status_approve']==$this->config->item('system_status_rejected'))
                {
                    $remarks="Current Status : Outlet to HQ transfer rejected. \n New Status: Approve pending.";
                }
                else
                {
                    $remarks="Current Status : Outlet to HQ transfer forwarded. \n New Status: Request forwarded pending.";
                }
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']='Nothing to rollback.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($item['status_request']==$this->config->item('system_status_forwarded'))
        {
            if($item['status_approve']==$this->config->item('system_status_approved'))
            {
                if($item['status_delivery']==$this->config->item('system_status_delivered'))
                {
                    if($item['status_receive']==$this->config->item('system_status_received') && $item['status_system_delivery_receive']==$this->config->item('system_status_yes'))
                    {
                        // Outlet Stock Out (reverse) & check outlet stock available

                        // Outlet office Stock Out
                        // Rollback transfer normal receive to Receive pending.
                        // Set status_receive = system_status_pending
                        $data=array();
                        $data['status_receive']=$this->config->item('system_status_pending');
                        $data['status_receive_forward']=$this->config->item('system_status_pending');
                        $data['status_receive_approve']=$this->config->item('system_status_pending');
                        Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                        $results=Query_helper::get_info($this->config->item('table_sms_transfer_ow_details'),'*',array('transfer_ow_id='.$item_id,"status='".$this->config->item('system_status_active')."'"));
                        $variety_ids=array();
                        foreach($results as $result)
                        {
                            $variety_ids[$result['variety_id']]=$result['variety_id'];
                        }
                        $current_stocks=Stock_helper::get_variety_stock($variety_ids);
                        // current stock update
                        foreach($results as $result)
                        {
                            $data=array();
                            $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['current_stock']-$result['quantity_receive']);
                            $data['in_ow']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['in_ow']-$result['quantity_receive']);
                            Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'warehouse_id='.$result['warehouse_id']));
                        }

                        $data = array();
                        $data['site'] = 'SMS_2018_19';
                        $data['reference_id'] = $item_id;
                        $data['controller_name'] = $this->controller_url;
                        $data['field_name'] = 'status_receive';
                        $data['current_status'] = $this->config->item('system_status_received');
                        $data['new_status'] = $this->config->item('system_status_pending');
                        $data['old_data'] = json_encode($item);
                        $data['remarks'] = $remarks;
                        $data['reason'] = $item_head['reason'];
                        $data['date_created'] = $time;
                        $data['user_created'] = $user->user_id;
                        Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
                    }
                    else
                    {
                        if($item['status_receive_forward']==$this->config->item('system_status_forwarded') && $item['status_receive_approve']==$this->config->item('system_status_pending'))
                        {
                            // Outlet office Stock Out
                            // Rollback transfer receive forwarded to forward pending.
                            // Set status_receive_forward = system_status_pending
                            $data=array();
                            $data['status_receive_forward']=$this->config->item('system_status_pending');
                            $data['status_system_delivery_receive']=$this->config->item('system_status_yes');
                            Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                            $data = array();
                            $data['site'] = 'SMS_2018_19';
                            $data['reference_id'] = $item_id;
                            $data['controller_name'] = $this->controller_url;
                            $data['field_name'] = 'status_receive_forward';
                            $data['current_status'] = $this->config->item('system_status_forwarded');
                            $data['new_status'] = $this->config->item('system_status_pending');
                            $data['old_data'] = json_encode($item);
                            $data['remarks'] = $remarks;
                            $data['reason'] = $item_head['reason'];
                            $data['date_created'] = $time;
                            $data['user_created'] = $user->user_id;
                            Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
                        }
                        elseif($item['status_receive_forward']==$this->config->item('system_status_forwarded') && $item['status_receive_approve']==$this->config->item('system_status_approved'))
                        {
                            // Outlet office Stock Out
                            // Rollback transfer receive approved to Receive approved pending.
                            // Set status_receive_approve = system_status_pending
                            $data=array();
                            $data['status_receive']=$this->config->item('system_status_pending');
                            $data['status_receive_approve']=$this->config->item('system_status_pending');
                            $data['status_system_delivery_receive']=$this->config->item('system_status_no');
                            Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                            $data=array();
                            $data['status']=$this->config->item('system_status_delete');
                            Query_helper::update($this->config->item('table_sms_transfer_ow_receive_solves'),$data,array('transfer_ow_id='.$item_id));

                            $results=Query_helper::get_info($this->config->item('table_sms_transfer_ow_details'),'*',array('transfer_ow_id='.$item_id,"status='".$this->config->item('system_status_active')."'"));
                            $variety_ids=array();
                            foreach($results as $result)
                            {
                                $variety_ids[$result['variety_id']]=$result['variety_id'];
                            }
                            $current_stocks=Stock_helper::get_variety_stock($variety_ids);
                            // current stock update
                            foreach($results as $result)
                            {
                                $data=array();
                                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['current_stock']-$result['quantity_receive']);
                                $data['in_ow']=($current_stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]['in_ow']-$result['quantity_receive']);
                                Query_helper::update($this->config->item('table_sms_stock_summary_variety'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'warehouse_id='.$result['warehouse_id']));
                            }

                            $data = array();
                            $data['site'] = 'SMS_2018_19';
                            $data['reference_id'] = $item_id;
                            $data['controller_name'] = $this->controller_url;
                            $data['field_name'] = 'status_receive_approve';
                            $data['current_status'] = $this->config->item('system_status_approved');
                            $data['new_status'] = $this->config->item('system_status_pending');
                            $data['old_data'] = json_encode($item);
                            $data['remarks'] = $remarks;
                            $data['reason'] = $item_head['reason'];
                            $data['date_created'] = $time;
                            $data['user_created'] = $user->user_id;
                            Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
                        }
                        else
                        {
                            // Head office Stock reverse
                            // Rollback transfer delivered to Delivery pending.
                            // Set status_delivery = system_status_pending
                            $data=array();
                            $data['status_delivery']=$this->config->item('system_status_pending');
                            Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                            $results=Query_helper::get_info($this->config->item('table_sms_transfer_ow_details'),'*',array('transfer_ow_id='.$item_id,"status='".$this->config->item('system_status_active')."'"));
                            $variety_ids=array();
                            foreach($results as $result)
                            {
                                $variety_ids[$result['variety_id']]=$result['variety_id'];
                            }
                            $current_stocks=Stock_helper::get_variety_stock_outlet($item['outlet_id'],$variety_ids);
                            // current stock update
                            foreach($results as $result)
                            {
                                $data=array();
                                $data['current_stock']=($current_stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']+$result['quantity_approve']);
                                $data['out_ow']=($current_stocks[$result['variety_id']][$result['pack_size_id']]['out_ow']-$result['quantity_approve']);
                                Query_helper::update($this->config->item('table_pos_stock_summary_variety'),$data,array('variety_id='.$result['variety_id'],'pack_size_id='.$result['pack_size_id'],'outlet_id='.$item['outlet_id']));
                            }

                            $data = array();
                            $data['site'] = 'SMS_2018_19';
                            $data['reference_id'] = $item_id;
                            $data['controller_name'] = $this->controller_url;
                            $data['field_name'] = 'status_delivery';
                            $data['current_status'] = $this->config->item('system_status_delivered');
                            $data['new_status'] = $this->config->item('system_status_pending');
                            $data['old_data'] = json_encode($item);
                            $data['remarks'] = $remarks;
                            $data['reason'] = $item_head['reason'];
                            $data['date_created'] = $time;
                            $data['user_created'] = $user->user_id;
                            Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
                        }
                    }
                }
                else
                {
                    // Rollback transfer approved to Approve pending.
                    // Set status_approve = system_status_pending
                    $data=array();
                    $data['status_approve']=$this->config->item('system_status_pending');
                    Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                    $data=array();
                    $data['site'] = 'SMS_2018_19';
                    $data['reference_id'] = $item_id;
                    $data['controller_name'] = $this->controller_url;
                    $data['field_name'] = 'status_approve';
                    $data['current_status'] = $this->config->item('system_status_approved');
                    $data['new_status']=$this->config->item('system_status_pending');
                    $data['old_data']=json_encode($item);
                    $data['remarks']=$remarks;
                    $data['reason']=$item_head['reason'];
                    $data['date_created'] = $time;
                    $data['user_created'] = $user->user_id;
                    Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
                }
            }
            else
            {
                if($item['status_approve']==$this->config->item('system_status_rejected'))
                {
                    // Rollback transfer transfer rejected to Request forwarded.
                    // Set status_approve = system_status_pending
                    $data=array();
                    $data['status_approve']=$this->config->item('system_status_pending');
                    Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                    $data=array();
                    $data['site'] = 'SMS_2018_19';
                    $data['reference_id'] = $item_id;
                    $data['controller_name'] = $this->controller_url;
                    $data['field_name'] = 'status_approve';
                    $data['current_status'] = $this->config->item('system_status_rejected');
                    $data['new_status']=$this->config->item('system_status_pending');
                    $data['old_data']=json_encode($item);
                    $data['remarks']=$remarks;
                    $data['reason']=$item_head['reason'];
                    $data['date_created'] = $time;
                    $data['user_created'] = $user->user_id;
                    Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
                }
                else
                {
                    // Rollback transfer forwarded to Forward pending.
                    // Set status_request = system_status_pending
                    $data=array();
                    $data['status_request']=$this->config->item('system_status_pending');
                    Query_helper::update($this->config->item('table_sms_transfer_ow'),$data,array('id='.$item_id));

                    $data=array();
                    $data['site'] = 'SMS_2018_19';
                    $data['reference_id'] = $item_id;
                    $data['controller_name'] = $this->controller_url;
                    $data['field_name'] = 'status_request';
                    $data['current_status'] = $this->config->item('system_status_forwarded');
                    $data['new_status']=$this->config->item('system_status_pending');
                    $data['old_data']=json_encode($item);
                    $data['remarks']=$remarks;
                    $data['reason']=$item_head['reason'];
                    $data['date_created'] = $time;
                    $data['user_created'] = $user->user_id;
                    Query_helper::add($this->config->item('table_dos_rollback_status'),$data);
                }
            }
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
            $this->db->from($this->config->item('table_sms_transfer_ow').' transfer_ow');
            $this->db->select('transfer_ow.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info','outlet_info.customer_id=transfer_ow.outlet_id AND outlet_info.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info.customer_id outlet_id, outlet_info.name outlet_name, outlet_info.customer_code outlet_code');
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = outlet_info.district_id','INNER');
            $this->db->select('districts.id district_id, districts.name district_name');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            $this->db->select('territories.id territory_id, territories.name territory_name');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_ow.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_ow.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_ow_courier_details').' wo_courier_details','wo_courier_details.transfer_ow_id=transfer_ow.id','LEFT');
            $this->db->select('
                                wo_courier_details.date_delivery courier_date_delivery,
                                wo_courier_details.date_challan,
                                wo_courier_details.challan_no,
                                wo_courier_details.courier_tracing_no,
                                wo_courier_details.place_booking_source,
                                wo_courier_details.place_destination,
                                wo_courier_details.date_booking,
                                wo_courier_details.remarks remarks_couriers
                                ');
            $this->db->join($this->config->item('table_login_basic_setup_couriers').' courier','courier.id=wo_courier_details.courier_id','LEFT');
            $this->db->select('courier.name courier_name');
            $this->db->where('transfer_ow.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_ow.id',$item_id);
            $this->db->where('outlet_info.revision',1);
            $this->db->order_by('transfer_ow.id','DESC');
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('details',$item_id,'View Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $user_ids=array();
            $user_ids[$data['item']['user_created_request']]=$data['item']['user_created_request'];
            $user_ids[$data['item']['user_updated_request']]=$data['item']['user_updated_request'];
            $user_ids[$data['item']['user_updated_forward']]=$data['item']['user_updated_forward'];
            $user_ids[$data['item']['user_updated_approve']]=$data['item']['user_updated_approve'];
            $user_ids[$data['item']['user_updated_approve_forward']]=$data['item']['user_updated_approve_forward'];
            $user_ids[$data['item']['user_updated_receive']]=$data['item']['user_updated_receive'];
            $user_ids[$data['item']['user_updated_receive_forward']]=$data['item']['user_updated_receive_forward'];
            $data['users']=System_helper::get_users_info($user_ids);

            $this->db->from($this->config->item('table_sms_transfer_ow_details').' transfer_ow_details');
            $this->db->select('transfer_ow_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_ow_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_ow_details.transfer_ow_id',$item_id);
            $this->db->where('transfer_ow_details.status',$this->config->item('system_status_active'));
            $this->db->order_by('transfer_ow_details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Outlet to HQ Transfer Details";
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
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
}
