<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";

    }

    //location setup

    public function get_dropdown_upazillas_by_districtid()
    {
        $district_id = $this->input->post('district_id');
        $html_container_id='#upazilla_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        if($this->input->post('select_label'))
        {
            $data['select_label']=$this->input->post('select_label');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }

    public function get_dropdown_unions_by_upazillaid()
    {
        $upazilla_id = $this->input->post('upazilla_id');
        $html_container_id='#union_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        if($this->input->post('select_label'))
        {
            $data['select_label']=$this->input->post('select_label');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_location_unions'),array('id value','name text'),array('upazilla_id ='.$upazilla_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }

    //crop classification

    public function get_dropdown_varieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        if($this->input->post('select_label'))
        {
            $data['select_label']=$this->input->post('select_label');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }

}
