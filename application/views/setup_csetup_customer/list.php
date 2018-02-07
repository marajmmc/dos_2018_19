<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1'])&&($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOCUMENT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/document')
    );
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Assign Upazilla',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/assign_upazilla')
    );
}

$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line('ACTION_DETAILS'),
    'class'=>'button_jqx_action',
    'data-action-link'=>site_url($CI->controller_url.'/index/details')
);
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
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>'Download'
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array(
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}

$action_buttons[]=array
(
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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['name']){echo 'checked';}?> value="name"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['name_short']){echo 'checked';}?> value="name_short"><?php echo $CI->lang->line('LABEL_SHORT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['type_name']){echo 'checked';}?> value="type"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['division_name']){echo 'checked';}?> value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['zone_name']){echo 'checked';}?> value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['territory_name']){echo 'checked';}?> value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['district_name']){echo 'checked';}?> value="district_name"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['customer_code']){echo 'checked';}?> value="customer_code"><?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['incharge_name']){echo 'checked';}?> value="incharge">Incharge</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['phone']){echo 'checked';}?> value="phone"><?php echo $CI->lang->line('LABEL_PHONE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['ordering']){echo 'checked';}?> value="ordering"><?php echo $CI->lang->line('LABEL_ORDER'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['status']){echo 'checked';}?> value="status"><?php echo $CI->lang->line('STATUS'); ?></label>
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
                { name: 'name', type: 'string' },
                { name: 'name_short', type: 'string' },
                { name: 'type_name', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'customer_code', type: 'string' },
                { name: 'incharge_name', type: 'string' },
                { name: 'phone', type: 'string' },
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
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',width:'300', hidden: <?php echo $items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_SHORT_NAME'); ?>', dataField: 'name_short',width:'100', hidden: <?php echo $items['name_short']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE'); ?>', dataField: 'type_name',width:'100',filtertype: 'list', hidden: <?php echo $items['type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',filtertype: 'list', hidden: <?php echo $items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', hidden: <?php echo $items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', hidden: <?php echo $items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', hidden: <?php echo $items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?>', dataField: 'customer_code', hidden: <?php echo $items['customer_code']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_INCHARGE'); ?>', dataField: 'incharge_name',filtertype: 'list', hidden: <?php echo $items['incharge_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PHONE'); ?>', dataField: 'phone', hidden: <?php echo $items['phone']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ORDER'); ?>', dataField: 'ordering',width:'100',cellsalign: 'right', hidden: <?php echo $items['ordering']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $items['status']?0:1;?>}
                ]
            });
    });

</script>


