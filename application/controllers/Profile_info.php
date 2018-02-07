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
            $this->db->where('user_info.revision',1);
            $data['user_info']=$this->db->get()->row_array();

            if(!$data['user_info'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong input. You use illegal way.';
                $this->json_return($ajax);
            }

            $data['title']=$data['user_info']['name'];

            $this->db->select('comp.*');
            $this->db->from($this->config->item('table_login_setup_users_company').' u_comp');
            $this->db->join($this->config->item('table_login_setup_company').' comp','comp.id=u_comp.company_id');
            $this->db->where('u_comp.user_id',$user_id);
            $this->db->where('u_comp.revision',1);
            $this->db->order_by('comp.ordering');
            $data['assigned_companies']=$this->db->get()->result_array();

            $this->db->select('os.*');
            $this->db->from($this->config->item('table_login_setup_users_other_sites').' uos');
            $this->db->join($this->config->item('table_login_system_other_sites').' os','os.id=uos.site_id');
            $this->db->where('uos.user_id',$user_id);
            $this->db->where('uos.revision',1);
            $this->db->order_by('os.ordering');
            $data['assigned_sites']=$this->db->get()->result_array();

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
