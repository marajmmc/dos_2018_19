<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_bank_account extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_bank_account');
        $this->controller_url='setup_bank_account';
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
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference();
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
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
            $user = User_helper::get_user();
            $result=Query_helper::get_info($this->config->item('table_system_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$this->controller_url.'"','method ="list"'),1);
            $data['system_preference_items']['bank_name']= 1;
            $data['system_preference_items']['branch_name']= 1;
            $data['system_preference_items']['account_number']= 1;
            $data['system_preference_items']['account_type_receive']= 1;
            $data['system_preference_items']['account_type_expense']= 1;
            $data['system_preference_items']['status']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }

            $data['title']="Bank Account List";
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            $ajax['status']=true;
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
        $this->db->from($this->config->item('table_login_setup_bank_account').' ba');
        $this->db->select('ba.*');
        $this->db->select('IF(ba.account_type_receive=1, "Yes","No") account_type_receive');
        $this->db->select('IF(ba.account_type_expense=1, "Yes","No") account_type_expense');
        $this->db->join($this->config->item('table_login_setup_bank').' bank',"bank.id = ba.bank_id AND bank.status !='".$this->config->item('system_status_delete')."'",'INNER');
        $this->db->select('bank.name bank_name');
        $this->db->where('ba.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Create New Bank Account";
            $data['item']['id']=0;
            $data['item']['bank_id']=0;
            $data['item']['branch_name']='';
            $data['item']['account_number']='';
            $data['item']['account_type_receive']=0;
            $data['item']['account_type_expense']=0;
            $data['item']['description']='';
            $data['item']['status']='Active';
            $data['items']=array();

            $data['banks']=Query_helper::get_info($this->config->item('table_login_setup_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
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
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $data['item']=Query_helper::get_info($this->config->item('table_login_setup_bank_account'),array('*'),array('id ='.$item_id,'status !="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Bank Account.';
                $this->json_return($ajax);
            }

            $data['items']=Query_helper::get_info($this->config->item('table_login_setup_bank_account_purpose'),array('*'),array('bank_account_id ='.$item_id,'revision = 1'),0);
            $data['banks']=Query_helper::get_info($this->config->item('table_login_setup_bank'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering'));

            $data['title']="Edit Bank Account :: ".$data['item']['branch_name']. " ( " .$data['item']['account_number']." )";
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
        $time=time();
        $item=$this->input->post('item');
        $items=$this->input->post('items');

        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_login_setup_bank_account'),'*',array('id ='.$id, 'status != "'.$this->config->item('system_status_delete').'"'),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Bank Account.';
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



        $this->db->trans_start();  //DB Transaction Handle START
        if($id>0)
        {
            $data=array();
            $data['date_updated'] = $time;
            $data['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_login_setup_bank_account_purpose'),$data, array('bank_account_id='.$id,'revision=1'), false);

            $this->db->where('bank_account_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_login_setup_bank_account_purpose'));

            foreach($items['purpose'] as $purpose)
            {
                $data=array();
                $data['bank_account_id'] = $id;
                $data['purpose'] = $purpose;
                $data['revision'] = 1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_login_setup_bank_account_purpose'),$data, false);
            }

            if(isset($item['account_type_receive']) && $item['account_type_receive']==1)
            {
                $item['account_type_receive']=1;
            }
            else
            {
                $item['account_type_receive']=0;
            }
            if(isset($item['account_type_expense']) && $item['account_type_expense']==1)
            {
                $item['account_type_expense']=1;
            }
            else
            {
                $item['account_type_expense']=0;
            }
            $item['date_updated']=$time;
            $item['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_login_setup_bank_account'),$item,array('id='.$id));
        }
        else
        {
            if(isset($item['account_type_receive']) && $item['account_type_receive']==1)
            {
                $item['account_type_receive']=1;
            }
            else
            {
                $item['account_type_receive']=0;
            }
            if(isset($item['account_type_expense']) && $item['account_type_expense']==1)
            {
                $item['account_type_expense']=1;
            }
            else
            {
                $item['account_type_expense']=0;
            }
            $item['date_created']=$time;
            $item['user_created']=$user->user_id;
            $id=Query_helper::add($this->config->item('table_login_setup_bank_account'),$item,array('id='.$id));

            foreach($items['purpose'] as $purpose)
            {
                $data=array();
                $data['bank_account_id'] = $id;
                $data['purpose'] = $purpose;
                $data['revision'] = 1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_login_setup_bank_account_purpose'),$data, false);
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
    private function check_validation()
    {
        $item=$this->input->post('item');
        $items=$this->input->post('items');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[bank_id]',$this->lang->line('LABEL_BANK_NAME'),'required');
        $this->form_validation->set_rules('item[branch_name]',$this->lang->line('LABEL_BRANCH_NAME'),'required');
        $this->form_validation->set_rules('item[account_number]',$this->lang->line('LABEL_ACCOUNT_NUMBER'),'required');
        if(!(isset($item['account_type_receive']) || isset($item['account_type_expense'])))
        {
            $this->message= $this->lang->line('LABEL_ACCOUNT_TYPE'). ' is empty. '.$this->lang->line('MSG_SELECT_ONE');
            return false;
        }
        if(!(sizeof($items)>0))
        {
            $this->message=$this->lang->line('LABEL_ACCOUNT_PURPOSE'). ' is empty. '.$this->lang->line('MSG_SELECT_ONE');
            return false;
        }

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
            $data['system_preference_items']['bank_name']= 1;
            $data['system_preference_items']['branch_name']= 1;
            $data['system_preference_items']['account_number']= 1;
            $data['system_preference_items']['account_type_receive']= 1;
            $data['system_preference_items']['account_type_expense']= 1;
            $data['system_preference_items']['status']= 1;
            if($result)
            {
                if($result['preferences']!=null)
                {
                    $preferences=json_decode($result['preferences'],true);
                    foreach($data['system_preference_items'] as $key=>$value)
                    {
                        if(isset($preferences[$key]))
                        {
                            $data['system_preference_items'][$key]=$value;
                        }
                        else
                        {
                            $data['system_preference_items'][$key]=0;
                        }
                    }
                }
            }
            $data['preference_method_name']='list';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
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
