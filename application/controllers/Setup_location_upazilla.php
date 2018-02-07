<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_location_upazilla extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_location_upazilla');
        $this->controller_url='setup_location_upazilla';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="search_crop_type_acres")
        {
            $this->system_search_crop_type_acres($id);
        }
        elseif($action=="list_crop_type_acres")
        {
            $this->system_list_crop_type_acres($id);
        }
        elseif($action=='get_crop_type_acres')
        {
            $this->system_get_edit_crop_type_acres();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_crop_type_acres")
        {
            $this->system_save_crop_type_acres();
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
            $data['title']="Upazilas";
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
        $this->db->from($this->config->item('table_login_setup_location_upazillas').' u');
        $this->db->select('u.id,u.name,u.status,u.ordering');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = u.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->order_by('u.ordering','ASC');
        $this->db->where('u.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Upazilla";
            $data["upazilla"] = Array(
                'id' => 0,
                'division_id'=>0,
                'zone_id'=>0,
                'territory_id'=>0,
                'district_id'=>0,
                'name' => '',
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );
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
                $upazilla_id=$this->input->post('id');
            }
            else
            {
                $upazilla_id=$id;
            }

            $this->db->from($this->config->item('table_login_setup_location_upazillas').' u');
            $this->db->select('u.*');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('u.id',$upazilla_id);
            $data['upazilla']=$this->db->get()->row_array();

            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['upazilla']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_login_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['upazilla']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_login_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['upazilla']['territory_id']));
            $data['title']="Edit Upazilla (".$data['upazilla']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$upazilla_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_search_crop_type_acres($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),'*',array('id ='.$item_id),1);
            if(!$data['item'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Upazilla ID';
                $this->json_return($ajax);
            }

            $data['title']='Crop Type Acres Search for Upazilla ('.$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search_crop_type_acres",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/search_crop_type_acres/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_list_crop_type_acres()
    {
        $reports=$this->input->post('report');

        $data['options']=$reports;
        if(!($reports['crop_id']>0))
        {
            $ajax['status']=false;
            $ajax['system_message']='Please Select a Crop';
            $this->json_return($ajax);
        }

        $crop_info=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$reports['crop_id']),1);
        if(!$crop_info)
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid Crop ID';
            $this->json_return($ajax);
        }

        $data['title']='Crop Type List of ('.$crop_info['name'].')';

        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_crop_type_acres",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->json_return($ajax);
    }
    private function system_get_edit_crop_type_acres()
    {
        $upazilla_id=$this->input->post('upazilla_id');
        $crop_id=$this->input->post('crop_id');

        $this->db->select('crop_type.id,crop_type.name');
        $this->db->select('ct_acres.acres');
        $this->db->from($this->config->item('table_login_setup_classification_crop_types').' crop_type');
        $this->db->join($this->config->item('table_login_setup_crop_type_acres_upazilla').' ct_acres','ct_acres.crop_type_id=crop_type.id AND ct_acres.upazilla_id='.$upazilla_id,'LEFT');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('crop_type.status',$this->config->item('system_status_active'));
        $this->db->order_by('crop_type.ordering','ASC');
        
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if($item['acres']==0)
            {
                $item['acres']=0;
            }
        }
        $this->json_return($items);
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
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
            $data=$this->input->post('upazilla');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();
                Query_helper::update($this->config->item('table_login_setup_location_upazillas'),$data,array("id = ".$id));
            }
            else
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_login_setup_location_upazillas'),$data);
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

    private function system_save_crop_type_acres()
    {
        $upazilla_id = $this->input->post("upazilla_id");
        $crop_id = $this->input->post("crop_id");
        $user = User_helper::get_user();
        $time=time();

        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($upazilla_id<1)
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid try';
            $this->json_return($ajax);
        }
        $items=$this->input->post('items');

        $this->db->select('ct_acres.*');
        $this->db->from($this->config->item('table_login_setup_crop_type_acres_upazilla').' ct_acres');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=ct_acres.crop_type_id','INNER');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('ct_acres.upazilla_id',$upazilla_id);
        $results=$this->db->get()->result_array();

        $items_current=array();
        foreach($results as $result)
        {
            $items_current[$result['crop_type_id']]=$result['acres'];
        }

        $data_add=array(
            'upazilla_id'=>$upazilla_id,
            'revision_acres'=>1,
            'date_created'=>$time,
            'user_created'=>$user->user_id
        );
        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $crop_type_id=>$acres)
        {
            if(isset($items_current[$crop_type_id]))
            {
                if($items_current[$crop_type_id]!=$acres)
                {
                    $this->db->set('acres',$acres);
                    $this->db->set('revision_acres','revision_acres+1',false);
                    $this->db->set('date_updated',$time);
                    $this->db->set('user_updated',$user->user_id);

                    $this->db->where('crop_type_id',$crop_type_id);
                    $this->db->where('upazilla_id',$upazilla_id);

                    $this->db->update($this->config->item('table_login_setup_crop_type_acres_upazilla'));
                }
            }
            else
            {
                if($acres>0)
                {
                    $data_add['crop_type_id']=$crop_type_id;
                    $data_add['acres']=$acres;
                    Query_helper::add($this->config->item('table_login_setup_crop_type_acres_upazilla'),$data_add);
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

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('upazilla[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('upazilla[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

}
