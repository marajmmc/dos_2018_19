<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_users extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
        $this->permissions=User_helper::get_permission('Setup_users');
        $this->controller_url='setup_users';
    }

    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='add')
        {
            $this->system_add();
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=="edit_username")
        {
            $this->system_edit_username($id);
        }
        elseif($action=="edit_password")
        {
            $this->system_edit_password($id);
        }
        elseif($action=="edit_status")
        {
            $this->system_edit_status($id);
        }
        elseif($action=="save_status")
        {
            $this->system_save_status();
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        elseif($action=="save_password")
        {
            $this->system_save_password();
        }
        elseif($action=="save_username")
        {
            $this->system_save_username();
        }

        elseif($action=="change_user_group")
        {
            $this->system_change_user_group($id);
        }
        elseif($action=="save_change_user_group")
        {
            $this->system_save_change_user_group();
        }
        elseif($action=="edit_authentication_setup")
        {
            $this->system_edit_authentication_setup($id);
        }
        elseif($action=="save_authentication_setup")
        {
            $this->system_save_authentication_setup();
        }
        elseif($action=='details')
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
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='List of Users';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
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
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $user = User_helper::get_user();
        $this->db->from($this->config->item('table_dos_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->select('user_info.name');
        $this->db->select('ug.name group_name');
        $this->db->join($this->config->item('table_dos_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id = user_info.user_group','LEFT');
        $this->db->where('user_info.revision',1);
        $this->db->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $this->db->where('user_info.user_group !=',1);
        }

        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if($item['group_name']==null)
            {
                $item['group_name']='Not Assigned';
            }
        }

        //$items=Query_helper::get_info($this->config->item('table_setup_user'),array('id','name','status','ordering'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $user=User_helper::get_user();
            $data['title']='Create New User';
            $data['user'] = array(
                'id' => 0,
                'employee_id' => '',
                'user_name' => ''
            );
            $data['user_info'] = array(
                'name' => '',
                'user_type_id' => '',
                'email' => '',
                'office_id' => '',
                'department_id' => '',
                'date_join' => System_helper::display_date(time()),
                'designation' => '',
                'ordering' => 999
            );
            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id !=1'));
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $user_id=$id;
            }
            else
            {
                $user_id=$this->input->post('id');
            }
            $user=User_helper::get_user();

            $data['user']=Query_helper::get_info($this->config->item('table_dos_setup_user'),array('id','employee_id','user_name','status'),array('id ='.$user_id),1);
            if(!$data['user'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            //$data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id),1);
            $data['title']="Edit User (".$data['user_info']['name'].')';

            if($user->user_group==1)
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            }
            else
            {
                $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"','id !=1'));
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$user_id);
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
        // get user id
        $id = $this->input->post('id');
        // get user info post value
        $data_user_info=$this->input->post('user_info');
        // get session information
        $user = User_helper::get_user();
        // check save or update permission
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if(!$this->check_validation_for_edit())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
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
            if(!$this->check_validation_for_add())
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->json_return($ajax);
            }
        }

        $time=time();

        $this->db->trans_start();  //DB Transaction Handle START
        // new user or user update - revision information
        if($id==0)
        {
            $data_user=$this->input->post('user');

            $data_user['password']=md5($data_user['password']);
            $data_user['status']=$this->config->item('system_status_active');
            $data_user['user_created'] = $user->user_id;
            $data_user['date_created'] = $time;
            $user_id=Query_helper::add($this->config->item('table_dos_setup_user'),$data_user);
            if($user_id===false)
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
            else
            {
                /// user info
                $data_user_info['user_id']=$user_id;
                $data_user_info['user_created'] = $user->user_id;
                $data_user_info['date_created'] = $time;
                $data_user_info['revision'] = 1;
                Query_helper::add($this->config->item('table_dos_setup_user_info'),$data_user_info,false);

            }
        }
        else
        {
            if(isset($data_user_info['date_birth']))
            {
                $data_user_info['date_birth']=System_helper::get_time($data_user_info['date_birth']);
                if($data_user_info['date_birth']===0)
                {
                    unset($data_user_info['date_birth']);
                }
            }
            if(isset($data_user_info['date_join']))
            {
                $data_user_info['date_join']=System_helper::get_time($data_user_info['date_join']);
                if($data_user_info['date_join']===0)
                {
                    unset($data_user_info['date_join']);
                }
            }

            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_dos_setup_user_info'),$revision_history_data,array('revision=1','user_id='.$id), false);

            $revision_change_data=array();
            $this->db->set('revision', 'revision+1', FALSE);
            //$revision_change_data['revision']='revision+1';
            Query_helper::update($this->config->item('table_dos_setup_user_info'),$revision_change_data,array('user_id='.$id), false);

            $data_user_info['revision'] = 1;
            $data_user_info['user_id']=$id;
            $data_user_info['user_created'] = $user->user_id;
            $data_user_info['date_created'] = $time;
            Query_helper::add($this->config->item('table_dos_setup_user_info'),$data_user_info,false);

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
    private function system_edit_password($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (Change Password)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }
            $data['title']="Reset Password of (".$data['user_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_password",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_password/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_password()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_password())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_dos_setup_user'),'*',array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (Change Password)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data['password']=md5($this->input->post('new_password'));
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_dos_setup_user'),$data,array("id = ".$id));
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $ajax['status']=true;
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
    private function system_edit_username($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (User Name)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['user']=Query_helper::get_info($this->config->item('table_dos_setup_user'),'*',array('id ='.$user_id),1);
            $data['title']="Reset Username of (".$data['user_info']['name'].')';
            $data['user_name']=$data['user']['user_name'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_username",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_username/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_username()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_username())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_dos_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (User Name)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data['user_name']=$this->input->post('new_username');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_dos_setup_user'),$data,array("id = ".$id));

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
    private function system_edit_status($id)
    {
        if(isset($this->permissions['action3']) && ($this->permissions['action3']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try(__FUNCTION__,$user_id,'Edit Status Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }

            $data['user']=Query_helper::get_info($this->config->item('table_dos_setup_user'),'*',array('id ='.$user_id),1);
            $data['title']="Change Status of (".$data['user_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_status",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_status/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_status()
    {
        $time=time();
        $user_id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_status())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_dos_setup_user'),array('*'),array('id ='.$user_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$user_id,'Edit Status Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data=array();
            $data['status']=$this->config->item('system_status_inactive');
            $data['remarks_status_change']=$this->input->post('remarks_status_change');
            $data['date_status_changed'] = $time;
            $data['user_status_changed'] = $user->user_id;
            if($result['status']==$this->config->item('system_status_inactive'))
            {
                $data['status']=$this->config->item('system_status_active');
            }
            $this->db->trans_start();  //DB Transaction Handle START

            Query_helper::update($this->config->item('table_dos_setup_user'),$data,array("id = ".$user_id));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message='Status Changed to '.$data['status'];
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
    private function check_validation_status()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('remarks_status_change','Change Reason','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_change_user_group($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (User Group)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['title']="Assign User Group for ".$data['user_info']['name'];
            $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/change_user_group",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/change_user_group/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_change_user_group()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_for_assigned_user_group())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START

            $data=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$id,'revision =1'),1);
            if(!$data)
            {
                System_helper::invalid_try('Update Non Exists (User Group)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_dos_setup_user_info'),$revision_history_data,array('revision=1','user_id='.$id),false);

            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_dos_setup_user_info'));

            $user_group_id=$this->input->post('user_group_id');

            unset($data['id']);
            unset($data['date_updated']);
            unset($data['user_updated']);
            $data['user_group']=$user_group_id;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_dos_setup_user_info'),$data, false);
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
    private function system_edit_authentication_setup($user_id)
    {
        $time=time();
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(!($user_id>0))
            {
                $user_id=$this->input->post('id');
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_dos_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try(__FUNCTION__,$user_id,'Edit Authentication user Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['user']=Query_helper::get_info($this->config->item('table_dos_setup_user'),'*',array('id ='.$user_id),1);
            if($data['user']['time_mobile_authentication_off_end']>$time)
            {
                $data['user']['day_mobile_authentication_off_end']=ceil(($data['user']['time_mobile_authentication_off_end']-$time)/(3600*24));
            }
            else
            {
                $data['user']['day_mobile_authentication_off_end']=0;
            }


            $data['title']="Change Authentication Setup of (".$data['user_info']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_authentication_setup",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_authentication_setup/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_authentication_setup()
    {
        $time=time();
        $user_id = $this->input->post("id");
        $item = $this->input->post("item");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        if(!$this->check_validation_authentication_setup())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_dos_setup_user'),'*',array('id ='.$user_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try(__FUNCTION__,$user_id,'Authentication setup user Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data=array();
            $data['max_logged_browser']=$item['max_logged_browser'];
            if(!(($result['time_mobile_authentication_off_end']==0)&&($item['day_mobile_authentication_off_end']==0)))
            {
                $data['time_mobile_authentication_off_end']=$time+$item['day_mobile_authentication_off_end']*3600*24;
            }
            $data['date_authentication_setup_changed'] = $time;
            $data['user_authentication_setup_changed'] = $user->user_id;
            $this->db->trans_start();  //DB Transaction Handle START

            Query_helper::update($this->config->item('table_dos_setup_user'),$data,array("id = ".$user_id));
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
    private function check_validation_authentication_setup()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[day_mobile_authentication_off_end]','Days for inactive mobile verification','required');
        $this->form_validation->set_rules('item[max_logged_browser]','Maximum Allowed Browser','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {

            if($id>0)
            {
                $user_id=$id;
            }
            else
            {
                $user_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_dos_setup_user').' user');
            $this->db->select('user.employee_id,user.user_name,user.status,user.date_created user_date_created');
            $this->db->select('user.status,user.user_status_changed,user.date_status_changed,user.remarks_status_change');
            $this->db->select('user.max_logged_browser,user.time_mobile_authentication_off_end,user.date_authentication_setup_changed,user.user_authentication_setup_changed');

            $this->db->join($this->config->item('table_dos_setup_user_info').' user_info','user_info.user_id=user.id');
            $this->db->select('user_info.*');
            $this->db->join($this->config->item('table_system_user_group').' u_group','u_group.id=user_info.user_group','left');
            $this->db->select('u_group.name group_name');
            $this->db->where('user.id',$user_id);
            $this->db->where('user_info.revision',1);
            $data['user_info']=$this->db->get()->row_array();

            if(!$data['user_info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $data['title']="Details of User (".$data['user_info']['name'].')';

            $user_ids=array();
            $user_ids[$data['user_info']['user_created']]=$data['user_info']['user_created'];
            if($data['user_info']['user_status_changed']>0)
            {
                $user_ids[$data['user_info']['user_status_changed']]=$data['user_info']['user_status_changed'];
            }
            if($data['user_info']['user_authentication_setup_changed']>0)
            {
                $user_ids[$data['user_info']['user_authentication_setup_changed']]=$data['user_info']['user_authentication_setup_changed'];
            }

            $data['users']=System_helper::get_users_info($user_ids);

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/details',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function check_validation_for_add()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user[user_name]',$this->lang->line('LABEL_USERNAME'),'required');
        $this->form_validation->set_rules('user[password]',$this->lang->line('LABEL_PASSWORD'),'required');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');



        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        $data_user=$this->input->post('user');
        if(!preg_match('/^[a-z0-9][a-z0-9_]*[a-z0-9]$/',$data_user['user_name']))
        {
            $ajax['system_message']='Username create rules violation';
            $this->json_return($ajax);
        }
        $duplicate_username_check=Query_helper::get_info($this->config->item('table_dos_setup_user'),array('user_name'),array('user_name ="'.$data_user['user_name'].'"'),1);
        if($duplicate_username_check)
        {
            $ajax['system_message']='This Username is already exists';
            $this->json_return($ajax);
        }
        return true;
    }
    private function check_validation_for_edit()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_password()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password',$this->lang->line('LABEL_PASSWORD'),'required');
        $this->form_validation->set_rules('re_password',$this->lang->line('LABEL_RE_PASSWORD'),'required');
        if($this->input->post('new_password')!=$this->input->post('re_password'))
        {
            $this->message="Password did not Match";
            return false;
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_username()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_username',$this->lang->line('LABEL_USERNAME'),'required');

        if(!preg_match('/^[a-z0-9][a-z0-9_]*[a-z0-9]$/',$this->input->post('new_username')))
        {
            $ajax['system_message']='Username create rules violation';
            $this->json_return($ajax);
        }
        if($this->input->post('new_username'))
        {
            $duplicate_username_check=Query_helper::get_info($this->config->item('table_dos_setup_user'),array('user_name'),array('id!='.$id,'user_name ="'.$this->input->post('new_username').'"'),1);
            if($duplicate_username_check)
            {
                $ajax['system_message']='This username is already exists';
                $this->json_return($ajax);
            }
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_for_assigned_user_group()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_group_id',$this->lang->line('LABEL_USER_GROUP'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
