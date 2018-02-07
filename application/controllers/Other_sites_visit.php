<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Other_sites_visit extends CI_Controller
{

	public function index()
	{

	}
    public function visit_site($site_id)
    {
        $user=User_helper::get_user();
        if($user)
        {
            $site_info=Query_helper::get_info($this->config->item('table_login_system_other_sites'),array('site_url,replace_text'),array('id ='.$site_id),1);
            if($site_info)
            {
                //$key=md5(time());
                $key=(time().'_'.$user->user_id);

                $info=Query_helper::get_info($this->config->item('table_login_other_sites_visit'),array('id'),array('user_id ='.$user->user_id,'site_id ='.$site_id),1);
                if($info)
                {
                    $this->db->where('id',$info['id']);
                    $this->db->set('count', 'count+1', FALSE);
                    $this->db->set('status', $this->config->item('system_status_active'));
                    $this->db->set('auth_key', $key);
                    $this->db->update($this->config->item('table_login_other_sites_visit'));

                }
                else
                {
                    $data['user_id']=$user->user_id;
                    $data['site_id']=$site_id;
                    $data['count']=1;
                    $data['status']=$this->config->item('system_status_active');
                    $data['auth_key']=$key;
                    $this->db->insert($this->config->item('table_login_other_sites_visit'),$data);
                }
                $site_url=str_replace($site_info['replace_text'],$key,$site_info['site_url']);
                redirect($site_url);
            }
            else
            {
                echo 'invalid try';
            }
        }
        else
        {
            redirect(base_url());
        }
    }
}
