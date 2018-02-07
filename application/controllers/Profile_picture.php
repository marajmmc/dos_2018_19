<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_picture extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Profile_picture');
        $this->controller_url='profile_picture';

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
            $user=User_helper::get_user();
            $user_id=$user->user_id;

            $data['user_info']=Query_helper::get_info($this->config->item('table_login_setup_user_info'),array('image_location','name'),array('user_id ='.$user_id,'revision =1'),1);
            $data['title']='Change Profile Picture';

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$user_id);
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
            $time=time();
            $user_id=$user->user_id;

            $dir=(FCPATH).'images/profiles/'.$user_id;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_image = System_helper::upload_file("images/profiles/".$user_id);
            if(array_key_exists('image_profile',$uploaded_image))
            {
                if($uploaded_image['image_profile']['status'])
                {
                    $data_user_info=Query_helper::get_info($this->config->item('table_login_setup_user_info'),array('*'),array('user_id ='.$user_id,'revision =1'),1);
                    unset($data_user_info['id']);
                    $data_user_info['user_created'] = $user->user_id;
                    $data_user_info['date_created'] = $time;
                    $data_user_info['revision'] = 1;
                    $data_user_info['image_name']=$uploaded_image['image_profile']['info']['file_name'];
                    $data_user_info['image_location']="images/profiles/".$user_id.'/'.$uploaded_image['image_profile']['info']['file_name'];

                    $revision_history_data=array();
                    $revision_history_data['date_updated']=$time;
                    $revision_history_data['user_updated']=$user->user_id;
                    Query_helper::update($this->config->item('table_login_setup_user_info'),$revision_history_data,array('revision=1','user_id='.$user_id));

                    $this->db->trans_start();  //DB Transaction Handle START
                    $this->db->where('user_id',$user_id);
                    $this->db->set('revision', 'revision+1', FALSE);
                    $this->db->update($this->config->item('table_login_setup_user_info'));


                    Query_helper::add($this->config->item('table_login_setup_user_info'),$data_user_info);
                    $this->db->trans_complete();   //DB Transaction Handle END
                    if ($this->db->trans_status() === TRUE)
                    {
                        $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                        $this->system_edit();
                    }
                    else
                    {
                        $ajax['status']=false;
                        $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                        $this->json_return($ajax);
                    }
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_image['image_profile']['message'];
                    $this->json_return($ajax);
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_NO_FILE_UPLOADED");
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        return true;
    }
}
