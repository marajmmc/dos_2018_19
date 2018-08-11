<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ems_rollback_tour extends Root_Controller
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
            $data['title']="Tour All List";
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
        $this->db->from($this->config->item('table_ems_tour_setup') . ' tour');
        $this->db->select('tour.*');
        $this->db->join($this->config->item('table_login_setup_user') . ' user', 'user.id = tour.user_id', 'INNER');
        $this->db->select('user.employee_id, user.user_name, user.status');
        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id=user.id', 'INNER');
        $this->db->select('user_info.name,user_info.ordering');
        $this->db->join($this->config->item('table_login_setup_designation') . ' designation', 'designation.id = user_info.designation', 'LEFT');
        $this->db->select('designation.name AS designation');
        $this->db->join($this->config->item('table_login_setup_department') . ' department', 'department.id = user_info.department_id', 'LEFT');
        $this->db->select('department.name AS department_name');
        $this->db->where('tour.status !="' . $this->config->item('system_status_delete') . '"');
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('tour.id', 'DESC');
        $this->db->limit($pagesize, $current_records);
        $items = $this->db->get()->result_array();

        foreach ($items as $key => &$item)
        {
            $items[$key]['date_from'] = System_helper::display_date($item['date_from']);
            $items[$key]['date_to'] = System_helper::display_date($item['date_to']);
            if ($item['designation'] == '')
            {
                $items[$key]['designation'] = '-';
            }
            if ($item['department_name'] == '')
            {
                $items[$key]['department_name'] = '-';
            }
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

            $this->db->from($this->config->item('table_ems_tour_setup').' tour');
            $this->db->select('tour.*');
            $this->db->where('tour.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Tour setup data not found.';
                $this->json_return($ajax);
            }

            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Tour setup already deleted.';
                $this->json_return($ajax);
            }

            if($data['item']['status_approved_adjustment']==$this->config->item('system_status_approved'))
            {
                $data['item']['message']='Current Status : Tour Payment Adjustment Approved. <br /> New Status: Tour Payment Adjustment Forwarded.';
            }
            else
            {
                if($data['item']['status_approved_adjustment']==$this->config->item('system_status_forwarded'))
                {
                    $data['item']['message']='Current Status : Tour Payment Adjustment Forwarded. <br /> New Status: Tour Payment Adjustment Forwarded Pending.';
                }
                else
                {
                    if($data['item']['status_approved_reporting']==$this->config->item('system_status_approved'))
                    {
                        $data['item']['message']='Current Status : Tour Reporting Approved. <br /> New Status: Tour Reporting Approved Pending.';
                    }
                    else
                    {
                        if($data['item']['status_forwarded_reporting']==$this->config->item('system_status_forwarded'))
                        {
                            $data['item']['message']='Current Status : Tour Reporting Forwarded. <br /> New Status: Tour Reporting Forwarded Pending.';
                        }
                        else
                        {
                            if($data['item']['status_paid_payment']==$this->config->item('system_status_paid'))
                            {
                                $data['item']['message']='Current Status : Tour Payment Paid. <br /> New Status: Tour Payment Un-Paid.';
                            }
                            else
                            {
                                if($data['item']['status_approved_payment']==$this->config->item('system_status_approved'))
                                {
                                    $data['item']['message']='Current Status : Tour Payment Approved. <br /> New Status: Tour Payment Approved Pending.';
                                }
                                else
                                {
                                    if($data['item']['status_approved_tour']==$this->config->item('system_status_approved'))
                                    {
                                        $data['item']['message']='Current Status : Tour Approved. <br /> New Status: Tour Approved Pending.';
                                    }
                                    else if($data['item']['status_approved_tour']==$this->config->item('system_status_rejected'))
                                    {
                                        $data['item']['message']='Current Status : Tour rejected. <br /> New Status: Tour forwarded Pending.';
                                    }
                                    else
                                    {
                                        $ajax['status']=false;
                                        $ajax['system_message']='Nothing to rollback.';
                                        $this->json_return($ajax);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $data['title']='Tour Setup Rollback';
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


        $this->db->from($this->config->item('table_ems_tour_setup').' tour');
        $this->db->select('tour.*');
        $this->db->where('tour.id',$item_id);
        $item=$this->db->get()->row_array();
        if(!$item)
        {
            $ajax['status']=false;
            $ajax['system_message']='Tour data not found.';
            $this->json_return($ajax);
        }

        if($item['status']==$this->config->item('system_status_delete'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Tour already deleted.';
            $this->json_return($ajax);
        }

        /* remarks massage */
        $remarks='';
        if($item['status_approved_adjustment']==$this->config->item('system_status_approved'))
        {
            $remarks='Current Status : Tour Payment Adjustment Approved. <br /> New Status: Tour Payment Adjustment Forwarded.';
        }
        else
        {
            if($item['status_approved_adjustment']==$this->config->item('system_status_forwarded'))
            {
                $remarks='Current Status : Tour Payment Adjustment Forwarded. <br /> New Status: Tour Payment Adjustment Forwarded Pending.';
            }
            else
            {
                if($item['status_approved_reporting']==$this->config->item('system_status_approved'))
                {
                    $remarks='Current Status : Tour Reporting Approved. <br /> New Status: Tour Reporting Approved Pending.';
                }
                else
                {
                    if($item['status_forwarded_reporting']==$this->config->item('system_status_forwarded'))
                    {
                        $remarks='Current Status : Tour Reporting Forwarded. <br /> New Status: Tour Reporting Forwarded Pending.';
                    }
                    else
                    {
                        if($item['status_paid_payment']==$this->config->item('system_status_paid'))
                        {
                            $remarks='Current Status : Tour Payment Paid. <br /> New Status: Tour Payment Un-Paid.';
                        }
                        else
                        {
                            if($item['status_approved_payment']==$this->config->item('system_status_approved'))
                            {
                                $remarks='Current Status : Tour Payment Approved. <br /> New Status: Tour Payment Approved Pending.';
                            }
                            else
                            {
                                if($item['status_approved_tour']==$this->config->item('system_status_approved'))
                                {
                                    $remarks='Current Status : Tour Approved. <br /> New Status: Tour Approved Pending.';
                                }
                                else if($item['status_approved_tour']==$this->config->item('system_status_rejected'))
                                {
                                    $remarks='Current Status : Tour rejected. <br /> New Status: Tour forwarded Pending.';
                                }
                                else
                                {
                                    $ajax['status']=false;
                                    $ajax['system_message']='Nothing to rollback.';
                                    $this->json_return($ajax);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($item['status_approved_adjustment']==$this->config->item('system_status_approved'))
        {
            //$remarks='Current Status : Tour Payment Adjustment Approved. <br /> New Status: Tour Payment Adjustment Approved Forwarded.';
            $data=array();
            $data['status_approved_adjustment']=$this->config->item('system_status_forwarded');
            Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

            $data = array();
            $data['site'] = 'EMS_2018_19';
            $data['reference_id'] = $item_id;
            $data['controller_name'] = $this->controller_url;
            $data['field_name'] = 'status_approved_adjustment';
            $data['current_status'] = $this->config->item('system_status_approved');
            $data['new_status'] = $this->config->item('system_status_forwarded');
            $data['old_data'] = json_encode($item);
            $data['remarks'] = $remarks;
            $data['reason'] = $item_head['reason'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
        }
        else
        {
            if($item['status_approved_adjustment']==$this->config->item('system_status_forwarded'))
            {
                //$remarks='Current Status : Tour Payment Adjustment Forwarded. <br /> New Status: Tour Payment Adjustment Forwarded Pending.';
                $data=array();
                $data['status_approved_adjustment']=$this->config->item('system_status_pending');
                Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                $data = array();
                $data['site'] = 'EMS_2018_19';
                $data['reference_id'] = $item_id;
                $data['controller_name'] = $this->controller_url;
                $data['field_name'] = 'status_approved_adjustment';
                $data['current_status'] = $this->config->item('system_status_forwarded');
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
                if($item['status_approved_reporting']==$this->config->item('system_status_approved'))
                {
                    //$remarks='Current Status : Tour Reporting Approved. <br /> New Status: Tour Reporting Approved Pending.';
                    $data=array();
                    $data['status_approved_reporting']=$this->config->item('system_status_pending');
                    Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                    $data = array();
                    $data['site'] = 'EMS_2018_19';
                    $data['reference_id'] = $item_id;
                    $data['controller_name'] = $this->controller_url;
                    $data['field_name'] = 'status_approved_reporting';
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
                    if($item['status_forwarded_reporting']==$this->config->item('system_status_forwarded'))
                    {
                        //$remarks='Current Status : Tour Reporting Forwarded. <br /> New Status: Tour Reporting Forwarded Pending.';
                        $data=array();
                        $data['status_approved_reporting']=$this->config->item('system_status_pending');
                        $data['status_forwarded_reporting']=$this->config->item('system_status_pending');
                        Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                        $data = array();
                        $data['site'] = 'EMS_2018_19';
                        $data['reference_id'] = $item_id;
                        $data['controller_name'] = $this->controller_url;
                        $data['field_name'] = 'status_forwarded_reporting';
                        $data['current_status'] = $this->config->item('system_status_forwarded');
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
                        if($item['status_paid_payment']==$this->config->item('system_status_paid'))
                        {
                            //$remarks='Current Status : Tour Payment Paid. <br /> New Status: Tour Payment Un-Paid.';
                            $data=array();
                            $data['status_paid_payment']=$this->config->item('system_status_pending');
                            /*$data['status_approved_reporting']=$this->config->item('system_status_pending');
                            $data['status_forwarded_reporting']=$this->config->item('system_status_pending');*/
                            Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                            $data = array();
                            $data['site'] = 'EMS_2018_19';
                            $data['reference_id'] = $item_id;
                            $data['controller_name'] = $this->controller_url;
                            $data['field_name'] = 'status_paid_payment';
                            $data['current_status'] = $this->config->item('system_status_paid');
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
                            if($item['status_approved_payment']==$this->config->item('system_status_approved'))
                            {
                                //$remarks='Current Status : Tour Payment Approved. <br /> New Status: Tour Payment Approved Pending.';
                                $data=array();
                                $data['status_approved_payment']=$this->config->item('system_status_pending');
                                /*$data['status_paid_payment']=$this->config->item('system_status_pending');
                                $data['status_approved_reporting']=$this->config->item('system_status_pending');
                                $data['status_forwarded_reporting']=$this->config->item('system_status_pending');*/
                                Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                                $data = array();
                                $data['site'] = 'EMS_2018_19';
                                $data['reference_id'] = $item_id;
                                $data['controller_name'] = $this->controller_url;
                                $data['field_name'] = 'status_approved_payment';
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
                                if($item['status_approved_tour']==$this->config->item('system_status_approved'))
                                {
                                    //$remarks='Current Status : Tour Approved. <br /> New Status: Tour Approved Pending.';
                                    $data=array();
                                    $data['status_approved_tour']=$this->config->item('system_status_pending');
                                    /*$data['status_approved_payment']=$this->config->item('system_status_pending');
                                    $data['status_paid_payment']=$this->config->item('system_status_pending');
                                    $data['status_approved_reporting']=$this->config->item('system_status_pending');
                                    $data['status_forwarded_reporting']=$this->config->item('system_status_pending');*/
                                    Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                                    $data = array();
                                    $data['site'] = 'EMS_2018_19';
                                    $data['reference_id'] = $item_id;
                                    $data['controller_name'] = $this->controller_url;
                                    $data['field_name'] = 'status_approved_tour';
                                    $data['current_status'] = $this->config->item('system_status_approved');
                                    $data['new_status'] = $this->config->item('system_status_pending');
                                    $data['old_data'] = json_encode($item);
                                    $data['remarks'] = $remarks;
                                    $data['reason'] = $item_head['reason'];
                                    $data['date_created'] = $time;
                                    $data['user_created'] = $user->user_id;
                                    Query_helper::add($this->config->item('table_dos_rollback_status'), $data);
                                }
                                else if($item['status_approved_tour']==$this->config->item('system_status_rejected'))
                                {
                                    //$remarks='Current Status : Tour rejected. <br /> New Status: Tour forwarded Pending.';
                                    $data=array();
                                    $data['status_approved_tour']=$this->config->item('system_status_pending');
                                    /*$data['status_approved_payment']=$this->config->item('system_status_pending');
                                    $data['status_paid_payment']=$this->config->item('system_status_pending');
                                    $data['status_approved_reporting']=$this->config->item('system_status_pending');
                                    $data['status_forwarded_reporting']=$this->config->item('system_status_pending');*/
                                    Query_helper::update($this->config->item('table_ems_tour_setup'),$data,array('id='.$item_id));

                                    $data = array();
                                    $data['site'] = 'EMS_2018_19';
                                    $data['reference_id'] = $item_id;
                                    $data['controller_name'] = $this->controller_url;
                                    $data['field_name'] = 'status_approved_tour';
                                    $data['current_status'] = $this->config->item('system_status_rejected');
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
                                    $ajax['status']=false;
                                    $ajax['system_message']='Nothing to rollback.';
                                    $this->json_return($ajax);
                                }
                            }
                        }
                    }
                }
            }
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
            $this->db->from($this->config->item('table_sms_transfer_oo').' transfer_oo');
            $this->db->select('transfer_oo.*');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info_source','outlet_info_source.customer_id=transfer_oo.outlet_id_source AND outlet_info_source.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info_source.customer_id outlet_id_source, outlet_info_source.name outlet_name_source, outlet_info_source.customer_code outlet_code_source');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' outlet_info_destination','outlet_info_destination.customer_id=transfer_oo.outlet_id_destination AND outlet_info_destination.type="'.$this->config->item('system_customer_type_outlet_id').'"','INNER');
            $this->db->select('outlet_info_destination.customer_id outlet_id_destination, outlet_info_destination.name outlet_name_destination, outlet_info_destination.customer_code outlet_code_destination');

            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info','pos_setup_user_info.user_id=transfer_oo.user_updated_delivery','LEFT');
            $this->db->select('pos_setup_user_info.name full_name_delivery_edit');
            $this->db->join($this->config->item('table_pos_setup_user_info').' pos_setup_user_info_forward','pos_setup_user_info_forward.user_id=transfer_oo.user_updated_delivery_forward','LEFT');
            $this->db->select('pos_setup_user_info_forward.name full_name_delivery_forward');
            $this->db->join($this->config->item('table_sms_transfer_oo_courier_details').' wo_courier_details','wo_courier_details.transfer_oo_id=transfer_oo.id','LEFT');
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
            $this->db->where('transfer_oo.status !=',$this->config->item('system_status_delete'));
            $this->db->where('transfer_oo.id',$item_id);
            $this->db->order_by('transfer_oo.id','DESC');
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

            $this->db->from($this->config->item('table_sms_transfer_oo_details').' transfer_oo_details');
            $this->db->select('transfer_oo_details.*');
            $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=transfer_oo_details.variety_id','INNER');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
            $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
            $this->db->select('crop.id crop_id, crop.name crop_name');
            $this->db->where('transfer_oo_details.transfer_oo_id',$item_id);
            $this->db->where('transfer_oo_details.status',$this->config->item('system_status_active'));
            $this->db->order_by('transfer_oo_details.id');
            $data['items']=$this->db->get()->result_array();

            $data['title']="Showroom to Showroom Transfer Details";
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
