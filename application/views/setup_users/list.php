<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line('ACTION_NEW'),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change password',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_password')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Assign sites',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/assign_sites')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change company',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/change_company')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change area',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_area')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change user group',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/change_user_group')
    );
}
if(isset($CI->permissions['action3']) && ($CI->permissions['action3']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change Employee ID',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_employee_id')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change username',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_username')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Change status',
        'data-message-confirm'=>'Are you sure to Change Status?',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_status')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['id']){echo 'checked';}?> value="id"><?php echo $CI->lang->line('ID'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['employee_id']){echo 'checked';}?> value="employee_id"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['user_name']){echo 'checked';}?> value="user_name"><?php echo $CI->lang->line('LABEL_USERNAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['name']){echo 'checked';}?> value="name"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['email']){echo 'checked';}?> value="email"><?php echo $CI->lang->line('LABEL_EMAIL'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['designation_name']){echo 'checked';}?> value="designation_name"><?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['department_name']){echo 'checked';}?> value="department_name"><?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['mobile_no']){echo 'checked';}?> value="mobile_no"><?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['blood_group']){echo 'checked';}?> value="blood_group"><?php echo $CI->lang->line('LABEL_BLOOD_GROUP'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['group_name']){echo 'checked';}?> value="group_name"><?php echo $CI->lang->line('LABEL_USER_GROUP'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['ordering']){echo 'checked';}?> value="ordering"><?php echo $CI->lang->line('LABEL_ORDER'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" <?php if($items['status']){echo 'checked';}?> value="status"><?php echo $CI->lang->line('STATUS'); ?></label>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'employee_id', type: 'string' },
                { name: 'user_name', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'email', type: 'string' },
                { name: 'designation_name', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'mobile_no', type: 'string' },
                { name: 'blood_group', type: 'string' },
                { name: 'group_name', type: 'string' },
                { name: 'ordering', type: 'int' },
                { name: 'status', type: 'string' }
            ],
            id: 'id',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                enablebrowserselection: true,
                columnsreorder: true,
                altrows: true,
                autoheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right', hidden: <?php echo $items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?>', dataField: 'employee_id',width:'100', hidden: <?php echo $items['employee_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_USERNAME'); ?>', dataField: 'user_name',width:'150', hidden: <?php echo $items['user_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',width:'300', hidden: <?php echo $items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_EMAIL'); ?>', dataField: 'email',width:'200', hidden: <?php echo $items['email']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?>', dataField: 'designation_name',width:'200', hidden: <?php echo $items['designation_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?>', dataField: 'department_name',width:'200', hidden: <?php echo $items['department_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?>', dataField: 'mobile_no', hidden: <?php echo $items['mobile_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_BLOOD_GROUP'); ?>', dataField: 'blood_group',filtertype: 'list', hidden: <?php echo $items['blood_group']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_USER_GROUP'); ?>', dataField: 'group_name',filtertype: 'list', hidden: <?php echo $items['group_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ORDER'); ?>', dataField: 'ordering',width:'100',cellsalign: 'right', hidden: <?php echo $items['ordering']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $items['status']?0:1;?>}

                ]
            });
    });
</script>
