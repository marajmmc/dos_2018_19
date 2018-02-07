<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_password extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Profile_password');
        $this->controller_url='profile_password';

    }

    public function index($action="edit",$id=0)
    {
        if($action=="edit")
        {
            $this->system_edit();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_edit();
        }
    }
    private function system_edit()
    {
        /*if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {*/
            $data['title']='Change Password';
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        /*}
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }*/
    }
    private function system_save()
    {
        $user = User_helper::get_user();
        /*if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }*/
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $data['password']=md5($this->input->post('new_password'));
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();
            Query_helper::update($this->config->item('table_login_setup_user'),$data,array("id = ".$user->user_id));

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_edit();
            }
            else
            {
                $ajax['status']=false;
                $ajax['desk_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password',$this->lang->line('LABEL_PASSWORD'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        if($this->input->post('new_password')!=$this->input->post('re_password'))
        {
            $this->message="Password did not Match";
            return false;
        }
        $user = User_helper::get_user();
        $info=Query_helper::get_info($this->config->item('table_login_setup_user'),array('id'),array('id ='.$user->user_id,'password ="'.md5($this->input->post('old_password')).'"'),1);
        /*echo '<PRE>';
        print_r($this->input->post('old_password'));
        print_r($info);
        echo md5($this->input->post('old_password'));
        echo '</PRE>';*/
        if(!$info)
        {
            $this->message="Old Password did not Match";
            return false;
        }

        return true;
    }
}
