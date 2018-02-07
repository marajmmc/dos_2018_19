<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
        'data-form'=>'#save_form'
    );
}

$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $user['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="employee_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[employee_id]" id="employee_id" class="form-control" value="<?php echo $user['employee_id']; ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="user_name" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USERNAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[user_name]" id="user_name" class="form-control" value="<?php echo $user['user_name']; ?>">
            </div>
        </div>
        <div style="font-size: 12px;margin-top: -10px;font-style: italic;" class="row show-grid">
            <div class="col-xs-4"></div>
            <div class="col-sm-4 col-xs-8">
                Username only support small letters, numbers and _ . Username's first and last character will not be _
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="password" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PASSWORD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user[password]" id="password" class="form-control" value="">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="name" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[name]" id="name" class="form-control" value="<?php echo $user_info['name'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="user_type_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_USER_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="user_type_id" name="user_info[user_type_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach($user_types as $user_type)
                    {
                        ?>
                        <option value="<?php echo $user_type['value']; ?>" <?php if($user_type['value']==$user_info['user_type_id']){echo 'selected';} ?>><?php echo $user_type['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="email" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMAIL');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[email]" id="email" class="form-control" value="<?php echo $user_info['email'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPANY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                    foreach($companies as $company)
                    {
                        ?>
                        <div class="checkbox">
                            <label title="<?php echo $company['full_name']; ?>">
                                <input type="checkbox" name="company[]" value="<?php echo $company['id']; ?>"><?php echo $company['short_name']; ?>
                            </label>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="office_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OFFICE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="office_id" name="user_info[office_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach($offices as $office)
                    {
                        ?>
                        <option value="<?php echo $office['value']; ?>" <?php if($office['value']==$user_info['office_id']){echo 'selected';} ?>><?php echo $office['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="department_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="department_id" name="user_info[department_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    foreach($departments as $department)
                    {
                        ?>
                        <option value="<?php echo $department['value']; ?>" <?php if($department['value']==$user_info['department_id']){echo 'selected';} ?>><?php echo $department['text']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="designation" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="designation" name="user_info[designation]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                        foreach($designations as $designation)
                        {
                            ?>
                            <option value="<?php echo $designation['value']; ?>" <?php if($designation['value']==$user_info['designation']){echo 'selected';} ?>><?php echo $designation['text']; ?></option>
                            <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="date_join" class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_JOIN');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[date_join]" id="date_join" class="form-control dob" value="<?php echo $user_info['date_join'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="division_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="division_id" name="area[division_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label for="zone_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="zone_id" name="area[zone_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label for="territory_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="territory_id" name="area[territory_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label for="district_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="district_id" name="area[district_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label for="upazilla_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="upazilla_id" name="area[upazilla_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="display: none" class="row show-grid" id="union_id_container">
            <div class="col-xs-4">
                <label for="union_id" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="union_id" name="area[union_id]" class="form-control">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="user_info[ordering]" id="ordering" class="form-control" value="<?php echo $user_info['ordering'] ?>" >
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).off('input','#employee_id');
		$(document).off('input','#user_name');
		$(document).off('change','#designation');
		$(document).off('change','#office_id');
		$(document).off('change','#department_id');
        $(document).off('change','#division_id');
        $(document).off('change','#zone_id');
        $(document).off('change','#territory_id');
        $(document).off('change','#district_id');
        $(document).off('change','#upazilla_id');
        $(document).off('change','#union_id');
        
        $('#division_id').html(get_dropdown_with_select(system_divisions,'','All'));
        $("#date_join").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "-100:+0"});

        $(document).on("input","#employee_id",function()
        {
            $('#user_name').val($(this).val());
            $('#password').val($(this).val());
        });
        $(document).on("input","#user_name",function()
        {
            $('#password').val($(this).val());
        });
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id],'','All'));
            }
            else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#zone_id",function()
        {
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id],'','All'));
            }
            else
            {
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
                $('#district_id').html(get_dropdown_with_select(system_districts[territory_id],'','All'));
            }
            else
            {
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var district_id=$("#district_id").val();
            if(district_id>0)
            {
                $('#upazilla_id_container').show();
                $('#union_id_container').hide();
                $.ajax({
                    url: '<?php echo site_url('common_controller/get_dropdown_upazillas_by_districtid'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        district_id:district_id,
                        html_container_id:'#upazilla_id',
                        select_label:'All'
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#upazilla_id_container').hide();
                $('#union_id_container').hide();
            }
        });
        $(document).on("change","#upazilla_id",function()
        {
            $("#union_id").val("");
            var upazilla_id=$("#upazilla_id").val();
            if(upazilla_id>0)
            {
                $('#union_id_container').show();
                $.ajax({
                    url: '<?php echo site_url('common_controller/get_dropdown_unions_by_upazillaid'); ?>',
                    type: 'POST',
                    datatype: "JSON",
                    data:{
                        upazilla_id:upazilla_id,
                        html_container_id:'#union_id',
                        select_label:'All'
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#union_id_container').hide();
            }
        });
    });
</script>
