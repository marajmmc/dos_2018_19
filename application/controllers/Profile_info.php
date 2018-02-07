<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_info extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Profile_info');
        $this->controller_url='profile_info';

    }

    public function index($action="details",$id=0)
    {
        //may be include edit options if required
        if($action=="details")
        {
            $this->system_details();
        }
        else
        {
            $this->system_details();
        }
    }

    private function system_details()
    {
        /*if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {*/
            $user=User_helper::get_user();
            $user_id=$user->user_id;

            $this->db->select('user.employee_id,user.user_name,user.status,user.date_created user_date_created');
            $this->db->select('user_info.*');
            $this->db->select('u_group.name group_name');
            $this->db->from($this->config->item('table_dos_setup_user').' user');
            $this->db->join($this->config->item('table_dos_setup_user_info').' user_info','user_info.user_id=user.id');
            $this->db->join($this->config->item('table_system_user_group').' u_group','u_group.id=user_info.user_group','left');
            $this->db->where('user.id',$user_id);
            $this->db->where('user_info.revision',1);
            $data['user_info']=$this->db->get()->row_array();

            if(!$data['user_info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $data['title']=$data['user_info']['name'];

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/details',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$user_id);
            $this->json_return($ajax);
        /*}
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }*/
    }
}
