<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Rollback',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
);

$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
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
                { name: 'status_open', type: 'string' },
                { name: 'status_open_forward', type: 'string' },
                { name: 'status_release', type: 'string' },
                { name: 'status_receive', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
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
                pagesize:500,
                pagesizeoptions: ['200', '500', '1000', '2000','3000','5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                columnsreorder: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', width:50, cellsAlign:'right'},
                    { text: 'Status', dataField: 'status_open',filtertype: 'list'},
                    { text: 'Status Forward', dataField: 'status_open_forward',filtertype: 'list'},
                    { text: 'Status Release', dataField: 'status_release',filtertype: 'list'},
                    { text: 'Status Receive', dataField: 'status_receive',filtertype: 'list'}
                ]
            });
    });
</script>
