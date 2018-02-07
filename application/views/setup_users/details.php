<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'href'=>site_url($CI->controller_url.'/index/edit/'.$user_info['user_id'])
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PROFILE_PICTURE');?></label>
        </div>
        <div class="col-sm-8">
            <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_base_url_profile_picture').$user_info['image_location']; ?>" alt="<?php echo $user_info['name']; ?>">
        </div>
    </div>
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse1" href="#">
                        Credentials</a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['employee_id'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USERNAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['user_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">User Creation Date</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo System_helper::display_date($user_info['user_date_created']);?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse2" href="#">
                        User's Company</a>
                </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
                <?php
                foreach($companies as $company)
                {
                    ?>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label title="<?php echo $company['full_name'];?>" class="control-label pull-right"><?php echo $company['short_name'];?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if(in_array($company['id'],$assigned_companies)){echo 'YES';}else{echo 'NO';}?></label>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse3" href="#">
                        Employee Type, Designation and Office</a>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OFFICE_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['office_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['department_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['designation_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USER_TYPE');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['type_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Employee Class</label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['employee_class_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USER_GROUP');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['group_name'];?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse4" href="#">
                        User's Area</a>
                </h4>
            </div>
            <div id="collapse4" class="panel-collapse collapse">
                <?php
                if(isset($message))
                {
                    ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php
                }
                else
                {
                    ?>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['division_name']){echo $assigned_area['division_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['zone_name']){echo $assigned_area['zone_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['territory_name']){echo $assigned_area['territory_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['district_name']){echo $assigned_area['district_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['upazilla_name']){echo $assigned_area['upazilla_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNION_NAME');?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if($assigned_area['union_name']){echo $assigned_area['union_name']; }else{echo 'All';};?></label>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse" data-target="#collapse5" href="#">
                        User's Site</a>
                </h4>
            </div>
            <div id="collapse5" class="panel-collapse collapse">
                <?php
                foreach($sites as $site)
                {
                    ?>
                    <div style="" class="row show-grid">
                        <div class="col-xs-4">
                            <label title="<?php echo $site['full_name'];?>" class="control-label pull-right"><?php echo $site['short_name'];?></label>
                        </div>
                        <div class="col-sm-4 col-xs-8">
                            <label class="control-label"><?php if(in_array($site['id'],$assigned_sites)){echo 'YES';}else{echo 'NO';}?></label>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse6" href="#">
                        Employee Personal Information</a>
                </h4>
            </div>
            <div id="collapse6" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMAIL');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['email'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FATHER_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['father_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MOTHER_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['mother_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_BIRTH');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo System_helper::display_date($user_info['date_birth']);?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_GENDER');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['gender'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_MARITAL_STATUS');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['status_marital'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SPOUSE_NAME');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['spouse_name'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NID');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['nid'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TIN');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['tin'];?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse7" href="#">
                        Address</a>
                </h4>
            </div>
            <div id="collapse7" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS_PRESENT');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['address_present'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS_PERMANENT');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['address_permanent'];?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse8" href="#">
                        Join and Salary Info</a>
                </h4>
            </div>
            <div id="collapse8" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_JOIN');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo System_helper::display_date($user_info['date_join']);?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SALARY_BASIC');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['salary_basic'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SALARY_OTHER');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['salary_other'];?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="external" data-toggle="collapse" data-target="#collapse9" href="#">
                        Contact Info</a>
                </h4>
            </div>
            <div id="collapse9" class="panel-collapse collapse">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BLOOD_GROUP');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['blood_group'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MOBILE_NO');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['mobile_no'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TEL_NO');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['tel_no'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTACT_PERSON');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['contact_person'];?></label>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CONTACT_NO');?></label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <label class="control-label"><?php echo $user_info['contact_no'];?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $user_info['ordering'];?></label>
        </div>
    </div>

</div>

<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
