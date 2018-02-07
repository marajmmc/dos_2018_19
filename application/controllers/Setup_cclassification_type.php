<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_cclassification_type extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_type');
        $this->controller_url='setup_cclassification_type';
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
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="acres")
        {
            $this->system_acres();
        }
        elseif($action=="get_acres")
        {
            $this->system_get_acres($id);
        }

        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_amount_acres")
        {
            $this->system_save_amount_acres();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']="Crop Types";
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
        $this->db->from($this->config->item('table_login_setup_classification_crop_types').' ct');
        //$this->db->select('ct.id,ct.name,ct.status,ct.ordering');
        $this->db->select('ct.id,ct.name,ct.quantity_kg_acre,ct.status,ct.ordering');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = ct.crop_id','INNER');
        $this->db->where('ct.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('ct.ordering','ASC');
        $items=$this->db->get()->result_array();
//        print_r($items);
//        exit;
        $this->json_return($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {

            $data['title']="Create New Crop Type";
            $data["item"] = array(
                'id' => 0,
                'crop_id'=>0,
                'name' => '',
                'description' => '',
                'quantity_kg_acre' => '',
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_classification_crop_types'),'*',array('id ='.$item_id),1);
            $data['crops']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Edit Type (".$data['item']['name'].')';
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
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
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
            $data=$this->input->post('item');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_login_setup_classification_crop_types'),$data,array("id = ".$id));
            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_login_setup_classification_crop_types'),$data);
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

    private function system_acres()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {

            $data['id']=$this->input->post('id');

            $this->db->from($this->config->item('table_login_setup_classification_crop_types').' t');
            $this->db->select('t.*');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = t.crop_id','INNER');
            $this->db->where('t.id',$data['id']);
            $data['info']=$this->db->get()->row_array();
            if(!$data['info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Type.';
                $this->json_return($ajax);
            }
//            echo $data['id'];
//            exit;
            $data['title']="Acres For Types ".$data['info']['name'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_acres",$data,true));
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

    private function system_get_acres()
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            $item_id=$this->input->post('id');
            $this->db->from($this->config->item('table_login_setup_location_upazillas').' u');
            $this->db->select('u.*');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('z.name zone_name');
            $this->db->select('division.name division_name');
            $this->db->select('acres.quantity_acres');
            $this->db->join($this->config->item('table_login_setup_classification_type_acres').' acres','acres.upazilla_id=u.id AND acres.type_id='.$item_id,'LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' z','z.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = z.division_id','INNER');
            $this->db->order_by('division.ordering','ASC');
            $this->db->order_by('z.ordering','ASC');
            $this->db->order_by('t.ordering','ASC');
            $this->db->order_by('d.ordering','ASC');
            $this->db->order_by('u.ordering','ASC');
            $items=$this->db->get()->result_array();
            foreach($items as &$item)
            {
                if($item['quantity_acres']==0)
                {
                    $item['quantity_acres']=0;
                }
            }

            $this->json_return($items);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_amount_acres()
    {
        $type_id = $this->input->post("id");
        $old_items=Query_helper::get_info($this->config->item('table_login_setup_classification_type_acres'),'*',array('type_id ='.$type_id),1);
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!$this->check_validation_acres_amount())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data['type_id']=$type_id;
            $this->db->trans_start();  //DB Transaction Handle START
            if($old_items)
            {
                $data['type_id']=$type_id;
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;

                $items=$this->input->post('items');
                foreach($items as $index=>$item)
                {
                    $data['quantity_acres']=$item;
                    $this->db->set('revision','revision+1',false);
                    Query_helper::update($this->config->item('table_login_setup_classification_type_acres'),$data,array("type_id = ".$type_id,"upazilla_id = ".$index));
                }
            }
            else
            {
                $data['type_id']=$type_id;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['revision']=1;
                $items=$this->input->post('items');
                foreach($items as $index=>$item)
                {
                    $data['upazilla_id']=$index;
                    $data['quantity_acres']=$item;
                    Query_helper::add($this->config->item('table_login_setup_classification_type_acres'),$data,false);
                }
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_list();
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
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('item[crop_id]',$this->lang->line('LABEL_CROP_NAME'),'required');
        $this->form_validation->set_rules('item[status]',$this->lang->line('STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    public function check_validation_acres_amount()
    {
        return true;
    }
}
