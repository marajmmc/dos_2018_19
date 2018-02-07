<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Setup_csetup_customer extends Root_Controller {

    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_csetup_customer');
        $this->controller_url='setup_csetup_customer';
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="document")
        {
            $this->system_documents($id);
        }
        elseif($action=="assign_upazilla")
        {
            $this->system_assign_upazilla($id);
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_document")
        {
            $this->system_document_save();
        }
        elseif($action=="save_assign_upazilla")
        {
            $this->system_save_assign_upazilla();
        }
        else
        {
            $this->system_list($id);
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);

            $data['items']['name']= 1;
            $data['items']['name_short']= 1;
            $data['items']['type_name']= 1;
            $data['items']['division_name']= 1;
            $data['items']['zone_name']= 1;
            $data['items']['territory_name']= 1;
            $data['items']['district_name']= 1;
            $data['items']['customer_code']= 1;
            $data['items']['incharge_name']= 1;
            $data['items']['phone']= 1;
            $data['items']['ordering']= 1;
            $data['items']['status']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $data['preferences']=json_decode($result['preferences'],true);
                    foreach($data['items'] as $key=>$value)
                    {
                        if(isset($data['preferences'][$key]))
                        {
                            $data['items'][$key]=$value;
                        }
                        else
                        {
                            $data['items'][$key]=0;
                        }
                    }
                }
            }
            $data['title']="Outlets";
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
        $this->db->from($this->config->item('table_login_csetup_customer').' cus');
        $this->db->select('cus.id,cus.status');
        $this->db->select('cus_info.name,cus_info.name_short,cus_info.customer_code,cus_info.phone,cus_info.ordering,cus_info.type');
        $this->db->select('cus_type.name type_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('cus_incharge.name incharge_name');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
        $this->db->join($this->config->item('table_login_csetup_cus_type').' cus_type','cus_type.id = cus_info.type','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->join($this->config->item('table_login_csetup_incharge').' cus_incharge','cus_incharge.id = cus_info.incharge','LEFT');
        $this->db->order_by('cus_info.type','ASC');
        $this->db->order_by('cus_info.customer_code','ASC');
        $this->db->order_by('division.ordering','ASC');
        $this->db->order_by('zone.ordering','ASC');
        $this->db->order_by('t.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus_info.ordering','ASC');
        $this->db->where('cus_info.revision',1);
        $this->db->where('cus.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if(strlen($item['incharge_name'])<1)
            {
                $item['incharge_name']='Not assigned';
            }
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Outlet";
            $data["customer"] = Array(
                'id' => 0,
                'status' => $this->config->item('system_status_active')
            );
            $data["customer_info"] = Array(
                'type' => '',
                'incharge' => '',
                'name_short' => '',
                'division_id'=>'',
                'zone_id'=>'',
                'territory_id'=>'',
                'district_id'=>'',
                'name' => '',
                'customer_code' => '',
                'credit_limit' => '500000',
                'name_owner' => '',
                'name_market' => '',
                'address' => '',
                'map_address' => '',
                'phone' => '',
                'nid' => '',
                'tin' => '',
                'image_name' => '',
                'image_location' => '',
                'opening_date' => System_helper::display_date(time()),
                'closing_date' => '',
                'email' => '',
                'status_agreement' => $this->config->item('system_status_not_done'),
                'ordering' => 9999,
                'remarks' => ''
            );
            $data['customer_types']=Query_helper::get_info($this->config->item('table_login_csetup_cus_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['incharge']=Query_helper::get_info($this->config->item('table_login_csetup_incharge'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();

            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }

            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->select('cus.id cus_id,cus.status');
            $this->db->select('cus_info.*');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('cus.id',$customer_id);
            $this->db->where('cus_info.revision',1);
            $data['customer_info']=$this->db->get()->row_array();
            if(!$data['customer_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Outlet';
                $this->json_return($ajax);
            }
            $data['customer']['id']=$data['customer_info']['customer_id'];
            $data['customer']['status']=$data['customer_info']['status'];

            $data['customer_types']=Query_helper::get_info($this->config->item('table_login_csetup_cus_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['incharge']=Query_helper::get_info($this->config->item('table_login_csetup_incharge'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['customer_info']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['customer_info']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['customer_info']['territory_id']));
            $data['title']="Edit Outlet (".$data['customer_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$customer_id);
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
        $id = $this->input->post("id");
        $user=User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();

            $data_customer=$this->input->post('customer');

            $data_customer_info=$this->input->post('customer_info');

            if($data_customer_info['phone'])
            {
                $data_customer_info['phone'] = str_replace(' ', '', $data_customer_info['phone']);
            }
            else
            {
                $data_customer_info['phone']=null;
            }

            if($data_customer_info['opening_date'])
            {
                $data_customer_info['opening_date']=System_helper::get_time($data_customer_info['opening_date']);
            }
            else
            {
                $data_customer_info['opening_date']=null;
            }

            if($data_customer_info['closing_date'])
            {
                $data_customer_info['closing_date']=System_helper::get_time($data_customer_info['closing_date']);
            }
            else
            {
                $data_customer_info['closing_date']=null;
            }

            $this->db->trans_start();  //DB Transaction Handle START

            if($id>0)
            {
                $result=Query_helper::get_info($this->config->item('table_login_csetup_customer'),array('id'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
                if(!$result)
                {
                    System_helper::invalid_try('Update Non Exists.',$id);
                    $ajax['status']=false;
                    $ajax['system_message']='Invalid Outlet.';
                    $this->json_return($ajax);
                    die();
                }

                $data_customer['user_updated'] = $user->user_id;
                $data_customer['date_updated'] = $time;
                Query_helper::update($this->config->item('table_login_csetup_customer'),$data_customer,array("id = ".$id));

                $revision_history_data=array();
                $revision_history_data['date_updated']=$time;
                $revision_history_data['user_updated']=$user->user_id;
                Query_helper::update($this->config->item('table_login_csetup_cus_info'),$revision_history_data,array('revision=1','customer_id='.$id),false);

                $this->db->where('customer_id',$id);
                $this->db->set('revision', 'revision+1', FALSE);
                $this->db->update($this->config->item('table_login_csetup_cus_info'));

                $data_customer_info['customer_id']=$id;
                $data_customer_info['revision']=1;
                $data_customer_info['user_created'] = $user->user_id;
                $data_customer_info['date_created'] = $time;
                $dir=(FCPATH).'images/customer_profiles/'.$id;
                if(!is_dir($dir))
                {
                    mkdir($dir, 0777);
                }
                $uploaded_image = System_helper::upload_file('images/customer_profiles/'.$id);
                if(array_key_exists('image_profile',$uploaded_image))
                {
                    if(!$uploaded_image['image_profile']['status'])
                    {
                        $ajax['status']=false;
                        $ajax['system_message']=$uploaded_image['image_profile']['message'];
                        $this->json_return($ajax);
                    }
                    $data_customer_info['image_name']=$uploaded_image['image_profile']['info']['file_name'];
                    $data_customer_info['image_location']='images/customer_profiles/'.$id.'/'.$uploaded_image['image_profile']['info']['file_name'];
                }
                Query_helper::add($this->config->item('table_login_csetup_cus_info'),$data_customer_info,false);
            }
            else
            {
                $data_customer['user_created'] = $user->user_id;
                $data_customer['date_created'] = $time;
                $customer_id=Query_helper::add($this->config->item('table_login_csetup_customer'),$data_customer);
                if($customer_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->json_return($ajax);
                }
                else
                {
                    $data_customer_info['customer_id']=$customer_id;
                    $data_customer_info['revision']=1;
                    $data_customer_info['user_created'] = $user->user_id;
                    $data_customer_info['date_created'] = $time;

                    $dir=(FCPATH).'images/customer_profiles/'.$customer_id;
                    if(!is_dir($dir))
                    {
                        mkdir($dir, 0777);
                    }
                    $uploaded_image = System_helper::upload_file('images/customer_profiles/'.$customer_id);
                    if(array_key_exists('image_profile',$uploaded_image))
                    {
                        if(!$uploaded_image['image_profile']['status'])
                        {
                            $ajax['status']=false;
                            $ajax['system_message']=$uploaded_image['image_profile']['message'];
                            $this->json_return($ajax);
                        }
                        $data_customer_info['image_name']=$uploaded_image['image_profile']['info']['file_name'];
                        $data_customer_info['image_location']='images/customer_profiles/'.$customer_id.'/'.$uploaded_image['image_profile']['info']['file_name'];
                    }
                    Query_helper::add($this->config->item('table_login_csetup_cus_info'),$data_customer_info,false);
                }
            }

            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }

            $this->db->from($this->config->item('table_login_csetup_customer').' cus');
            $this->db->select('cus.id,cus.status');
            $this->db->select('cus_info.*');
            $this->db->select('cus_type.name type_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');
            $this->db->select('cus_incharge.name incharge_name');
            $this->db->select('GROUP_CONCAT(u.name) upazilla_names');
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = cus.id','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_type').' cus_type','cus_type.id = cus_info.type','INNER');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_login_csetup_incharge').' cus_incharge','cus_incharge.id = cus_info.incharge','LEFT');
            $this->db->join($this->config->item('table_login_csetup_cus_assign_upazillas').' au','au.customer_id = cus.id AND au.revision=1','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.id = au.upazilla_id','LEFT');
            $this->db->where('cus.id',$customer_id);
            $this->db->where('cus_info.revision',1);
            $this->db->group_by('cus.id');
            $data['customer_info']=$this->db->get()->row_array();
            if(!$data['customer_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $data['customer']['id']=$data['customer_info']['customer_id'];
            $data['customer']['status']=$data['customer_info']['status'];
            $data['assign_upazillas']=array();
            if($data['customer_info']['upazilla_names'])
            {
                $data['assign_upazillas']=explode(",",$data['customer_info']['upazilla_names']);
            }

            $data['file_details']=array();

            $results=Query_helper::get_info($this->config->item('table_login_csetup_cus_document'),'*',array('customer_id ='.$customer_id,'revision=1'));
            if($results)
            {
                foreach($results as $result)
                {
                    $data['file_details'][]=$result;
                }
            }
            $data['title']="Outlet (".$data['customer_info']['name'].') Details';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$customer_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_documents($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }
            $result=Query_helper::get_info($this->config->item('table_login_csetup_customer'),'*',array('id ='.$customer_id));
            if(!$result)
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$customer_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $results=Query_helper::get_info($this->config->item('table_login_csetup_cus_document'),'*',array('customer_id ='.$customer_id,'revision=1'));
            $info=Query_helper::get_info($this->config->item('table_login_csetup_cus_info'),'*',array('customer_id ='.$customer_id,'revision=1'));
            $data['customer']['id']=$customer_id;
            $data['file_details']=array();
            if($results)
            {
                $data['file_details']=$results;
            }

            $data['title']='Outlet ('.$info[0]['name'].') Documents :';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/document",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/document/'.$customer_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_assign_upazilla($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $customer_id=$this->input->post('id');
            }
            else
            {
                $customer_id=$id;
            }
            $data['customer_info']['id']=$customer_id;

            $this->db->from($this->config->item('table_login_csetup_cus_info').' cus_info');
            $this->db->select('cus_info.name,cus_info.type');
            $this->db->select('u.id upazilla_id,u.name upazilla_name');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.district_id = cus_info.district_id AND u.status ="' .$this->config->item('system_status_active').'"','INNER');
            $this->db->join($this->config->item('table_login_csetup_cus_assign_upazillas').' cau','cau.upazilla_id=u.id AND cau.customer_id !='.$customer_id.' AND cau.revision=1','LEFT');
            $this->db->where('cus_info.customer_id',$customer_id);
            $this->db->where('cus_info.revision',1);
            $this->db->where('cau.upazilla_id',NULL);
            $results=$this->db->get()->result_array();
//            print_r($this->db->last_query());exit;
            foreach($results as $result)
            {
                if($result['type']!=1)
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Please select a showroom to assign upazilla.';
                    $this->json_return($ajax);
                    die();
                }
                $data['customer_info']['name']=$result['name'];
                $data['upazillas'][]=$result;
            }
            $data['assigned_upazillas']=array();
            $results=Query_helper::get_info($this->config->item('table_login_csetup_cus_assign_upazillas'),'*',array('customer_id ='.$customer_id,'revision=1'));
            if($results)
            {
                foreach($results as $result)
                {
                    $data['assigned_upazillas'][]=$result['upazilla_id'];
                }
            }
            $data['title']="Assign upazillas for ".$data['customer_info']['name'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_upazilla",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_upazilla/'.$customer_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_document_save()
    {
        $id = $this->input->post("id");
        $user=User_helper::get_user();
        $time=time();

        $this->db->trans_start();  //DB Transaction Handle START

        $results=Query_helper::get_info($this->config->item('table_login_csetup_cus_document'),'*',array('customer_id ='.$id));
        if($results)
        {
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_csetup_cus_document'),$revision_history_data,array('revision=1','customer_id='.$id),false);

            $this->db->where('customer_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_csetup_cus_document'));
        }
        /*else
        {
            System_helper::invalid_try('Update Non Exists (Document).',$id);
            $ajax['status']=false;
            $ajax['system_message']='Invalid Outlet.';
            $this->json_return($ajax);
            die();
        }*/
        $file_folder='images/customer_documents/'.$id;
        $dir=(FCPATH).$file_folder;
        if(!is_dir($dir))
        {
            mkdir($dir, 0777);
        }
        $types='gif|jpg|png|jpeg|doc|docx|pdf|xls|xlsx|ppt|pptx|txt';
        $uploaded_files = System_helper::upload_file($file_folder,$types);
        foreach($uploaded_files as $file)
        {
            if(!$file['status'])
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']=$file['message'];
                $this->json_return($ajax);
            }
        }

        $files=array();
        $remarks=array();
        if($this->input->post('files')){
            $files=$this->input->post('files');
        }
        if($this->input->post('remarks')){
            $remarks=$this->input->post('remarks');
        }

        foreach($remarks as $index=>$remark)
        {
            $data=array();
            $data['customer_id']=$id;
            if(isset($uploaded_files['file_'.$index]))
            {
                $data['file_location']=$file_folder.'/'.$uploaded_files['file_'.$index]['info']['file_name'];
                $data['file_name']=$uploaded_files['file_'.$index]['info']['file_name'];
                $data['file_type']=$uploaded_files['file_'.$index]['info']['file_type'];
            }
            elseif(isset($files['file_'.$index]))
            {
                $data['file_location']=$file_folder.'/'.$files['file_'.$index];
                $data['file_name']=$files['file_'.$index];
                $data['file_type']=$files['file_type_'.$index];
            }
            $data['file_remarks']=$remark;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision']=1;
            Query_helper::add($this->config->item('table_login_csetup_cus_document'),$data,false);
        }

        $this->db->trans_complete(); //DB Transaction Handle END

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
    private function system_save_assign_upazilla()
    {
        $customer_id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_for_assigned_upazillas())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_login_csetup_customer'),array('id'),array('id ='.$customer_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (Assign Upazilla).',$customer_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Outlet.';
                $this->json_return($ajax);
                die();
            }
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_csetup_cus_assign_upazillas'),$revision_history_data,array('revision=1','customer_id='.$customer_id), false);
            $this->db->where('customer_id',$customer_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_csetup_cus_assign_upazillas'));
            $upazillas=$this->input->post('upazillas');
            if(is_array($upazillas))
            {
                foreach($upazillas as $upazilla)
                {
                    $data=array();
                    $data['customer_id']=$customer_id;
                    $data['upazilla_id']=$upazilla;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_login_csetup_cus_assign_upazillas'),$data, false);
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
    }
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['items']['name']= 1;
            $data['items']['name_short']= 1;
            $data['items']['type_name']= 1;
            $data['items']['division_name']= 1;
            $data['items']['zone_name']= 1;
            $data['items']['territory_name']= 1;
            $data['items']['district_name']= 1;
            $data['items']['customer_code']= 1;
            $data['items']['incharge_name']= 1;
            $data['items']['phone']= 1;
            $data['items']['ordering']= 1;
            $data['items']['status']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $data['preferences']=json_decode($result['preferences'],true);
                    foreach($data['items'] as $key=>$value)
                    {
                        if(isset($data['preferences'][$key]))
                        {
                            $data['items'][$key]=$value;
                        }
                        else
                        {
                            $data['items'][$key]=0;
                        }
                    }
                }
            }
            $data['title']="Set Preference";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/preference",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('customer_info[type]',$this->lang->line('LABEL_CUSTOMER_TYPE'),'required');
        $this->form_validation->set_rules('customer_info[incharge]',$this->lang->line('LABEL_INCHARGE'),'required');
        $this->form_validation->set_rules('customer_info[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('customer_info[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required');
        $this->form_validation->set_rules('customer_info[credit_limit]',$this->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT'),'required|numeric');
        $this->form_validation->set_rules('customer_info[nid]',$this->lang->line('LABEL_NID'),'required|numeric');
        $this->form_validation->set_rules('customer_info[opening_date]',$this->lang->line('LABEL_DATE_OPENING'),'required');
        //$this->form_validation->set_rules('customer_info[email]',$this->lang->line('LABEL_EMAIL'),'required|valid_email');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $id=$this->input->post('id');
        if($id>0)
        {
            $this->db->from($this->config->item('table_login_csetup_cus_info').' cus_info');
            $this->db->select('cus_info.*');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('cus_info.customer_id',$id);
            $this->db->where('cus_info.revision',1);
            $customer=$this->db->get()->row_array();
            if(!$customer)
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack trying to edit an id that does not exits');
                $this->message="Invalid Try";
                return false;
            }
        }
        return true;
    }
    private function check_validation_for_assigned_upazillas()
    {
        return true;
    }
}