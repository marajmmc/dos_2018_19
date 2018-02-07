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
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Change Principals',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/change_principals')
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Pricing',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/pricing')
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Packing Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/packing_setup')
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
    $action_buttons[]=array
    (
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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['id']){echo 'checked';}?> value="id"><?php echo $CI->lang->line('ID'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['name']){echo 'checked';}?> value="name"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['crop_name']){echo 'checked';}?> value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['crop_type_name']){echo 'checked';}?> value="crop_type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['whose']){echo 'checked';}?> value="whose"><?php echo $CI->lang->line('LABEL_WHOSE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['competitor_name']){echo 'checked';}?> value="competitor_name"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  <?php if($items['stock_id']){echo 'checked';}?> value="stock_id"><?php echo $CI->lang->line('LABEL_STOCK_ID'); ?></label>
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

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'whose', type: 'string' },
                { name: 'competitor_name', type: 'string' },
                { name: 'stock_id', type: 'string' },
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
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',width:'40',cellsalign: 'right', hidden: <?php echo $items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name', hidden: <?php echo $items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list', hidden: <?php echo $items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', hidden: <?php echo $items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_WHOSE'); ?>', dataField: 'whose', hidden: <?php echo $items['whose']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?>', dataField: 'competitor_name', hidden: <?php echo $items['competitor_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_ID'); ?>', dataField: 'stock_id', hidden: <?php echo $items['stock_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ORDER'); ?>', dataField: 'ordering',width:'100',cellsalign: 'right', hidden: <?php echo $items['ordering']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',filtertype: 'list',width:'150',cellsalign: 'right', hidden: <?php echo $items['status']?0:1;?>}
                ]
            });
    });
</script>
