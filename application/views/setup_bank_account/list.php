<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
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
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
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
                { name: 'bank_name', type: 'string' },
                { name: 'branch_name', type: 'string' },
                { name: 'account_number', type: 'string' },
                { name: 'account_type_receive', type: 'string' },
                { name: 'account_type_expense', type: 'string' },
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
                altrows: true,
                autoheight: true,
                autorowheight: true,
                columnsreorder: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_BANK_NAME'); ?>', dataField: 'bank_name', hidden: <?php echo $system_preference_items['bank_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_BRANCH_NAME'); ?>', dataField: 'branch_name', hidden: <?php echo $system_preference_items['branch_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ACCOUNT_NUMBER'); ?>', dataField: 'account_number', hidden: <?php echo $system_preference_items['account_number']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ACCOUNT_TYPE_RECEIVE'); ?>', dataField: 'account_type_receive',filtertype: 'list', hidden: <?php echo $system_preference_items['account_type_receive']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ACCOUNT_TYPE_EXPENSE'); ?>', dataField: 'account_type_expense',filtertype: 'list', hidden: <?php echo $system_preference_items['account_type_expense']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS');?>', dataField: 'status',cellsalign: 'center',filtertype: 'list', width:80, hidden: <?php echo $system_preference_items['status']?0:1;?>}
                ]
            });
    });
</script>
