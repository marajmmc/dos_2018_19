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
        elseif($action=="edit_employee_id")
        {
            $this->system_edit_employee_id($id);
        }
        elseif($action=="edit_status")
        {
            $this->system_edit_status($id);
        }
        elseif($action=="edit_area")
        {
            $this->system_edit_area($id);
        }
        elseif($action=='details')
        {
            $this->system_details($id);
        }
        elseif($action=="assign_sites")
        {
            $this->system_assign_sites($id);
        }
        elseif($action=="change_company")
        {
            $this->system_change_company($id);
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
        elseif($action=="save_employee_id")
        {
            $this->system_save_employee_id();
        }
        elseif($action=="save_status")
        {
            $this->system_save_status();
        }
        elseif($action=="save_area")
        {
            $this->system_save_area();
        }
        elseif($action=="save_assign_sites")
        {
            $this->system_save_assign_sites();
        }
        elseif($action=="save_assign_company")
        {
            $this->system_save_assign_company();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        elseif($action=="change_user_group")
        {
            $this->system_change_user_group($id);
        }
        elseif($action=="save_change_user_group")
        {
            $this->system_save_change_user_group();
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['items']['id']= 1;
            $data['items']['employee_id']= 1;
            $data['items']['user_name']= 1;
            $data['items']['name']= 1;
            $data['items']['email']= 1;
            $data['items']['designation_name']= 1;
            $data['items']['department_name']= 1;
            $data['items']['mobile_no']= 1;
            $data['items']['blood_group']= 1;
            $data['items']['group_name']= 1;
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

        $this->db->from($this->config->item('table_login_setup_user').' user');
        $this->db->select('user.id,user.employee_id,user.user_name,user.status');
        $this->db->select('user_info.name,user_info.email,user_info.ordering,user_info.blood_group,user_info.mobile_no');
        $this->db->select('ug.name group_name');
        $this->db->select('designation.name designation_name');
        $this->db->select('department.name department_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id = user_info.user_group','LEFT');
        $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $this->db->join($this->config->item('table_login_setup_department').' department','department.id = user_info.department_id','LEFT');
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
            if($item['blood_group']=='')
            {
                $item['blood_group']='Not Assigned';
            }
        }

        //$items=Query_helper::get_info($this->config->item('table_setup_user'),array('id','name','status','ordering'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
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
            $data['user_types']=Query_helper::get_info($this->config->item('table_login_setup_user_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['designations']=Query_helper::get_info($this->config->item('table_login_setup_designation'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['companies']=Query_helper::get_info($this->config->item('table_login_setup_company'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['offices']=Query_helper::get_info($this->config->item('table_login_setup_offices'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $user=User_helper::get_user();

            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name','status'),array('id ='.$user_id),1);
            if(!$data['user'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            //$data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id),1);
            $data['title']="Edit User (".$data['user_info']['name'].')';

            $data['offices']=Query_helper::get_info($this->config->item('table_login_setup_offices'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['designations']=Query_helper::get_info($this->config->item('table_login_setup_designation'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['departments']=Query_helper::get_info($this->config->item('table_login_setup_department'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['user_types']=Query_helper::get_info($this->config->item('table_login_setup_user_type'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['employee_classes']=Query_helper::get_info($this->config->item('table_login_setup_employee_class'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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
            $user_id=Query_helper::add($this->config->item('table_login_setup_user'),$data_user);
            if($user_id===false)
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
            else
            {
                //$id=$user_id;
                $data_area=$this->input->post('area');
                $data_area['user_id']=$user_id;
                $data_area['user_created'] = $user->user_id;
                $data_area['date_created'] = $time;
                $data_area['revision'] = 1;
                Query_helper::add($this->config->item('table_login_setup_user_area'),$data_area);

                $companies=$this->input->post('company');
                if(is_array($companies))
                {
                    foreach($companies as $company)
                    {
                        $data_company=array();
                        $data_company['user_id']=$user_id;
                        $data_company['company_id']=$company;
                        $data_company['user_created'] = $user->user_id;
                        $data_company['date_created'] = $time;
                        $data_company['revision'] = 1;
                        Query_helper::add($this->config->item('table_login_setup_users_company'),$data_company);
                    }
                }

                /// user info
                $data_user_info['user_id']=$user_id;
                $data_user_info['user_created'] = $user->user_id;
                $data_user_info['date_created'] = $time;
                $data_user_info['revision'] = 1;

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
                Query_helper::add($this->config->item('table_login_setup_user_info'),$data_user_info,false);

            }
        }
        else
        {
            $dir=(FCPATH).'images/profiles/'.$id;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_image = System_helper::upload_file('images/profiles/'.$id);
            if(array_key_exists('image_profile',$uploaded_image))
            {
                if(!$uploaded_image['image_profile']['status'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_image['image_profile']['message'];
                    $this->json_return($ajax);
                }
                $data_user_info['image_name']=$uploaded_image['image_profile']['info']['file_name'];
                $data_user_info['image_location']='images/profiles/'.$id.'/'.$uploaded_image['image_profile']['info']['file_name'];
            }

            /// user info


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
            Query_helper::update($this->config->item('table_login_setup_user_info'),$revision_history_data,array('revision=1','user_id='.$id), false);

            $revision_change_data=array();
            $this->db->set('revision', 'revision+1', FALSE);
            //$revision_change_data['revision']='revision+1';
            Query_helper::update($this->config->item('table_login_setup_user_info'),$revision_change_data,array('user_id='.$id), false);

            $data_user_info['revision'] = 1;
            $data_user_info['user_id']=$id;
            $data_user_info['user_created'] = $user->user_id;
            $data_user_info['date_created'] = $time;
            Query_helper::add($this->config->item('table_login_setup_user_info'),$data_user_info,false);

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
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
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
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),'*',array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
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
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));
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
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (User Name)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),'*',array('id ='.$user_id),1);
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
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
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
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

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
    private function system_edit_employee_id($id)
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
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (Employee ID)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),'*',array('id ='.$user_id),1);
            $data['title']="Reset Employee ID of (".$data['user_info']['name'].')';
            $data['employee_id']=$data['user']['employee_id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_employee_id",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_employee_id/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_employee_id()
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
        if(!$this->check_validation_employee_id())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Edit Non Exists (Employee ID)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data['employee_id']=$this->input->post('new_employee_id');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

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
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),'*',array('id ='.$user_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Edit Non Exists (User Status)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $status=$this->config->item('system_status_inactive');
            if($result['status']==$this->config->item('system_status_inactive'))
            {
                $status=$this->config->item('system_status_active');
            }

            $this->db->trans_start();  //DB Transaction Handle START
            Query_helper::update($this->config->item('table_login_setup_user'),array('status'=>$status),array("id = ".$user_id));
            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $this->message='Status Changed to '.$status;
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
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
        $id = $this->input->post("id");
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
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (User Status)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data['status']=$this->input->post('status');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            if($this->input->post('status')==$this->config->item('system_status_inactive'))
            {
                $data['date_deactivated'] = $time;
            }
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$id));

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
    private function system_details($id)
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }

            $this->db->select('user.employee_id,user.user_name,user.status,user.date_created user_date_created');
            $this->db->select('user_info.*');
            $this->db->select('office.name office_name');
            $this->db->select('designation.name designation_name');
            $this->db->select('department.name department_name');
            $this->db->select('u_type.name type_name');
            $this->db->select('e_class.name employee_class_name');
            $this->db->select('u_group.name group_name');
            $this->db->from($this->config->item('table_login_setup_user').' user');
            $this->db->join($this->config->item('table_login_setup_user_info').' user_info','user_info.user_id=user.id');
            $this->db->join($this->config->item('table_login_setup_offices').' office','office.id=user_info.office_id','left');
            $this->db->join($this->config->item('table_login_setup_department').' department','department.id=user_info.department_id','left');
            $this->db->join($this->config->item('table_login_setup_designation').' designation','designation.id=user_info.designation','left');
            $this->db->join($this->config->item('table_login_setup_user_type').' u_type','u_type.id=user_info.user_type_id','left');
            $this->db->join($this->config->item('table_login_setup_employee_class').' e_class','e_class.id=user_info.employee_class_id','left');
            $this->db->join($this->config->item('table_system_user_group').' u_group','u_group.id=user_info.user_group','left');
            $this->db->where('user.id',$user_id);
            //$this->db->where('user_info.revision',1);
            $data['user_info']=$this->db->get()->row_array();

            if(!$data['user_info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $data['title']="Details of User (".$data['user_info']['name'].')';

            $data['companies']=Query_helper::get_info($this->config->item('table_login_setup_company'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $assigned_companies=Query_helper::get_info($this->config->item('table_login_setup_users_company'),array('company_id'),array('user_id ='.$user_id,'revision =1'));
            $data['assigned_companies']=array();
            foreach($assigned_companies as $row)
            {
                $data['assigned_companies'][]=$row['company_id'];
            }

            $this->db->from($this->config->item('table_login_setup_user_area').' aa');
            $this->db->select('aa.*');
            $this->db->select('union.name union_name');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');
            $this->db->join($this->config->item('table_login_setup_location_unions').' union','union.id = aa.union_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.id = aa.upazilla_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = aa.district_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
            $this->db->where('aa.revision',1);
            $this->db->where('aa.user_id',$user_id);
            $data['assigned_area']=$this->db->get()->row_array();
            if($data['assigned_area'])
            {
                $this->db->from($this->config->item('table_login_setup_user_area').' aa');
                if($data['assigned_area']['division_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','INNER');
                }
                if($data['assigned_area']['zone_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.division_id = division.id','INNER');
                    $this->db->where('zone.id',$data['assigned_area']['zone_id']);
                }
                if($data['assigned_area']['territory_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.zone_id = zone.id','INNER');
                    $this->db->where('t.id',$data['assigned_area']['territory_id']);
                }
                if($data['assigned_area']['district_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.territory_id = t.id','INNER');
                    $this->db->where('d.id',$data['assigned_area']['district_id']);
                }
                if($data['assigned_area']['upazilla_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.district_id = d.id','INNER');
                    $this->db->where('u.id',$data['assigned_area']['upazilla_id']);
                }
                if($data['assigned_area']['union_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_unions').' union','union.upazilla_id = u.id','INNER');
                    $this->db->where('union.id',$data['assigned_area']['union_id']);
                }
                $this->db->where('aa.revision',1);
                $this->db->where('aa.user_id',$user_id);
                $info=$this->db->get()->row_array();
                if(!$info)
                {
                    $data['message']="Relation between assigned area is not correct.Please re-assign this user.";
                }
            }
            else
            {
                $data['assigned_area']['division_name']=false;
                $data['assigned_area']['zone_name']=false;
                $data['assigned_area']['territory_name']=false;
                $data['assigned_area']['district_name']=false;
                $data['assigned_area']['upazilla_name']=false;
                $data['assigned_area']['union_name']=false;
            }

            $data['sites']=Query_helper::get_info($this->config->item('table_login_system_other_sites'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $results=Query_helper::get_info($this->config->item('table_login_setup_users_other_sites'),'*',array('revision =1','user_id='.$user_id));
            $data['assigned_sites']=array();
            foreach($results as $result)
            {
                $data['assigned_sites'][]=$result['site_id'];
            }
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
    private function system_assign_sites($id)
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
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$user_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$data['user'])
            {
                System_helper::invalid_try('Edit Non Exists (User Assign Site)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Assign Sites for ".$data['user_info']['name'];
            $data['sites']=Query_helper::get_info($this->config->item('table_login_system_other_sites'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $results=Query_helper::get_info($this->config->item('table_login_setup_users_other_sites'),'*',array('user_id ='.$user_id,'revision =1'));
            $data['assigned_sites']=array();
            foreach($results as $result)
            {
                $data['assigned_sites'][]=$result['site_id'];
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_sites",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_sites/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_assign_sites()
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
        if(!$this->check_validation_for_assigned_sites())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (User Assign Site)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }

            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_setup_users_other_sites'),$revision_history_data,array('revision=1','user_id='.$id),false);

            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_setup_users_other_sites'));
            $sites=$this->input->post('sites');
            if(is_array($sites))
            {
                foreach($sites as $site)
                {
                    $data=array();
                    $data['user_id']=$id;
                    $data['site_id']=$site;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_login_setup_users_other_sites'),$data,false);
                }
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $ajax['status']=false;
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
    private function system_change_company($id)
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
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$user_id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$data['user'])
            {
                System_helper::invalid_try('Edit Non Exists (Change Company)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }

            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            $data['title']="Assign Company for ".$data['user_info']['name'];
            $data['companies']=Query_helper::get_info($this->config->item('table_login_setup_company'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $results=Query_helper::get_info($this->config->item('table_login_setup_users_company'),'*',array('user_id ='.$user_id,'revision =1'));
            $data['assigned_company']=array();
            foreach($results as $result)
            {
                $data['assigned_company'][]=$result['company_id'];
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/change_company",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/change_company/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_assign_company()
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
        if(!$this->check_validation_for_assigned_company())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data['user']=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$data['user'])
            {
                System_helper::invalid_try('Update Non Exists (Change Company)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
            }
            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START
            $companies=$this->input->post('companies');
            if(count($companies)==0)
            {
                $ajax['status']=false;
                $ajax['system_message']='At least one company needed';
                $this->json_return($ajax);
            }
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_setup_users_company'),$revision_history_data,array('revision=1','user_id='.$id),false);
            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_setup_users_company'));
            if(is_array($companies))
            {
                foreach($companies as $company)
                {
                    $data=array();
                    $data['user_id']=$id;
                    $data['company_id']=$company;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision'] = 1;
                    Query_helper::add($this->config->item('table_login_setup_users_company'),$data, false);
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
    private function system_edit_area($id)
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
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
            if(!$data['user_info'])
            {
                System_helper::invalid_try('Edit Non Exists (Area)',$user_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }
            $data['title']="Assign (".$data['user_info']['name'].') to an Area';

            $this->db->from($this->config->item('table_login_setup_user_area').' aa');
            $this->db->select('aa.*');
            $this->db->select('union.name union_name');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');
            $this->db->join($this->config->item('table_login_setup_location_unions').' union','union.id = aa.union_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.id = aa.upazilla_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = aa.district_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
            $this->db->where('aa.revision',1);
            $this->db->where('aa.user_id',$user_id);
            $data['assigned_area']=$this->db->get()->row_array();
            if($data['assigned_area'])
            {
                $this->db->from($this->config->item('table_login_setup_user_area').' aa');
                if($data['assigned_area']['division_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = aa.division_id','INNER');
                }
                if($data['assigned_area']['zone_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.division_id = division.id','INNER');
                    $this->db->where('zone.id',$data['assigned_area']['zone_id']);
                }
                if($data['assigned_area']['territory_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.zone_id = zone.id','INNER');
                    $this->db->where('t.id',$data['assigned_area']['territory_id']);
                }
                if($data['assigned_area']['district_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.territory_id = t.id','INNER');
                    $this->db->where('d.id',$data['assigned_area']['district_id']);
                }
                if($data['assigned_area']['upazilla_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_upazillas').' u','u.district_id = d.id','INNER');
                    $this->db->where('u.id',$data['assigned_area']['upazilla_id']);
                }
                if($data['assigned_area']['union_id']>0)
                {
                    $this->db->join($this->config->item('table_login_setup_location_unions').' union','union.upazilla_id = u.id','INNER');
                    $this->db->where('union.id',$data['assigned_area']['union_id']);
                }
                $this->db->where('aa.revision',1);
                $this->db->where('aa.user_id',$user_id);
                $info=$this->db->get()->row_array();
                if(!$info)
                {
                    $data['message']="Relation between assigned area is not correct.Please re-assign this user.";
                }
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_area",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_area/'.$user_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_area()
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
        if(!$this->check_validation_area())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $result=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id','employee_id','user_name'),array('id ='.$id, 'status !="'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists (Area)',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid User.';
                $this->json_return($ajax);
                die();
            }

            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START

            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_setup_user_area'),$revision_history_data,array('revision=1','user_id='.$id),false);

            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_setup_user_area'));

            $data=$this->input->post('area');
            $data['user_id']=$id;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_login_setup_user_area'),$data,false);

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
            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$user_id,'revision =1'),1);
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

            $data=Query_helper::get_info($this->config->item('table_login_setup_user_info'),'*',array('user_id ='.$id,'revision =1'),1);
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
            Query_helper::update($this->config->item('table_login_setup_user_info'),$revision_history_data,array('revision=1','user_id='.$id),false);

            $this->db->where('user_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_setup_user_info'));

            $user_group_id=$this->input->post('user_group_id');

            unset($data['id']);
            unset($data['date_updated']);
            unset($data['user_updated']);
            $data['user_group']=$user_group_id;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_login_setup_user_info'),$data, false);
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
    private function check_validation_for_add()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user[user_name]',$this->lang->line('LABEL_USERNAME'),'required');
        $this->form_validation->set_rules('user[password]',$this->lang->line('LABEL_PASSWORD'),'required');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('user_info[user_type_id]',$this->lang->line('LABEL_USER_TYPE'),'required');
        if($this->input->post('user_info[user_type_id]')==$this->config->item('USER_TYPE_EMPLOYEE'))
        {
            $this->form_validation->set_rules('user[employee_id]',$this->lang->line('LABEL_EMPLOYEE_ID'),'required');
            $this->form_validation->set_rules('user_info[office_id]',$this->lang->line('LABEL_OFFICE_NAME'),'required|is_natural_no_zero');
            $this->form_validation->set_rules('user_info[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required|is_natural_no_zero');
            $this->form_validation->set_rules('user_info[designation]',$this->lang->line('LABEL_DESIGNATION_NAME'),'required|is_natural_no_zero');
            //$this->form_validation->set_rules('user_info[email]',$this->lang->line('LABEL_EMAIL'),'required|email');
            $data_companies=$this->input->post('company');
            if(count($data_companies)==0)
            {
                $ajax['system_message']='At least one company needed';
                $this->json_return($ajax);
            }
        }

        $data_area=$this->input->post('area');
        if($data_area['union_id']>0)
        {
            $this->form_validation->set_rules('area[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['upazilla_id']>0)
        {
            $this->form_validation->set_rules('area[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['district_id']>0)
        {
            $this->form_validation->set_rules('area[territory_id]',$this->lang->line('LABEL_TERRITORY_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['territory_id']>0)
        {
            $this->form_validation->set_rules('area[zone_id]',$this->lang->line('LABEL_ZONE_NAME'),'required|is_natural_no_zero');
        }
        if($data_area['zone_id']>0)
        {
            $this->form_validation->set_rules('area[division_id]',$this->lang->line('LABEL_DIVISION_NAME'),'required|is_natural_no_zero');
        }

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
        $duplicate_username_check=Query_helper::get_info($this->config->item('table_login_setup_user'),array('user_name'),array('user_name ="'.$data_user['user_name'].'"'),1);
        if($duplicate_username_check)
        {
            $ajax['system_message']='This Username is already exists';
            $this->json_return($ajax);
        }
        if($data_user['employee_id'])
        {
            $duplicate_employee_id_check=Query_helper::get_info($this->config->item('table_login_setup_user'),array('employee_id'),array('employee_id ="'.$data_user['employee_id'].'"'),1);
            if($duplicate_employee_id_check)
            {
                $ajax['system_message']='This Employee ID is already exists';
                $this->json_return($ajax);
            }
        }
        return true;
    }
    private function check_validation_for_edit()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_info[name]',$this->lang->line('LABEL_NAME'),'required');

        $this->form_validation->set_rules('user_info[user_type_id]',$this->lang->line('LABEL_USER_TYPE'),'required');
        if($this->input->post('user_info[user_type_id]')==$this->config->item('USER_TYPE_EMPLOYEE'))
        {
            $this->form_validation->set_rules('user_info[office_id]',$this->lang->line('LABEL_OFFICE_NAME'),'required');
            $this->form_validation->set_rules('user_info[department_id]',$this->lang->line('LABEL_DEPARTMENT_NAME'),'required');
            $this->form_validation->set_rules('user_info[designation]',$this->lang->line('LABEL_DESIGNATION_NAME'),'required');
            //$this->form_validation->set_rules('user_info[email]',$this->lang->line('LABEL_EMAIL'),'required|email');
        }

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
            $duplicate_username_check=Query_helper::get_info($this->config->item('table_login_setup_user'),array('user_name'),array('id!='.$id,'user_name ="'.$this->input->post('new_username').'"'),1);
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
    private function check_validation_employee_id()
    {
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_employee_id',$this->lang->line('LABEL_EMPLOYEE_ID'),'required');

        if($this->input->post('new_employee_id'))
        {
            $duplicate_employee_id_check=Query_helper::get_info($this->config->item('table_login_setup_user'),array('employee_id'),array('id!='.$id,'employee_id ="'.$this->input->post('new_employee_id').'"'),1);
            if($duplicate_employee_id_check)
            {
                $ajax['system_message']='This employee ID is already exists';
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
    private function check_validation_status()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('status',$this->lang->line('STATUS'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_area()
    {
        $this->load->library('form_validation');
        $data=$this->input->post('area');
        if($data['union_id']>0)
        {
            $this->form_validation->set_rules('area[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|is_natural_no_zero');
        }
        if($data['upazilla_id']>0)
        {
            $this->form_validation->set_rules('area[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required|is_natural_no_zero');
        }
        if($data['district_id']>0)
        {
            $this->form_validation->set_rules('area[territory_id]',$this->lang->line('LABEL_TERRITORY_NAME'),'required|is_natural_no_zero');
        }
        if($data['territory_id']>0)
        {
            $this->form_validation->set_rules('area[zone_id]',$this->lang->line('LABEL_ZONE_NAME'),'required|is_natural_no_zero');
        }
        if($data['zone_id']>0)
        {
            $this->form_validation->set_rules('area[division_id]',$this->lang->line('LABEL_DIVISION_NAME'),'required|is_natural_no_zero');
        }
        $this->form_validation->set_rules('id',$this->lang->line('LABEL_USER_NAME'),'required|is_natural_no_zero');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_validation_for_assigned_sites()
    {
        return true;
    }
    private function check_validation_for_assigned_company()
    {
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
    private function system_set_preference()
    {
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['items']['id']= 1;
            $data['items']['employee_id']= 1;
            $data['items']['user_name']= 1;
            $data['items']['name']= 1;
            $data['items']['email']= 1;
            $data['items']['designation_name']= 1;
            $data['items']['department_name']= 1;
            $data['items']['mobile_no']= 1;
            $data['items']['blood_group']= 1;
            $data['items']['group_name']= 1;
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
}
