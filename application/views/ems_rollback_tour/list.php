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
                { name: 'name', type: 'string' },
                { name: 'employee_id', type: 'string'},
                { name: 'department_name', type: 'string'},
                { name: 'designation', type: 'string'},
                { name: 'title', type: 'string'},
                { name: 'date_from', type: 'string'},
                { name: 'date_to', type: 'string'},
                { name: 'amount_iou_request', type: 'string'},
                { name: 'amount_iou_adjustment', type: 'string'},
                { name: 'status_forwarded_tour', type: 'string'},
                { name: 'status_approved_tour', type: 'string'},
                { name: 'status_forwarded_reporting', type: 'string'},
                { name: 'status_approved_reporting', type: 'string'},
                { name: 'status_approved_payment', type: 'string'},
                { name: 'status_paid_payment', type: 'string'},
                { name: 'status_approved_adjustment', type: 'string'},
                { name: 'status', type: 'string'}
            ],
            id: 'id',
            type: 'POST',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column=='amount_iou_request')
            {
                element.html(get_string_amount(record.amount_iou_request));
            }
            if(column=='amount_iou_adjustment')
            {
                element.html(get_string_amount(record.amount_iou_adjustment));
            }
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
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
                pagesizeoptions: ['50', '100', '200','300','500','1000','5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50'},
                        { text: 'Name', pinned: true, dataField: 'name', width: '180', rendered: tooltiprenderer},
                        { text: 'Employee ID', pinned: true, dataField: 'employee_id', filtertype: 'list', width: '80', rendered: tooltiprenderer},
                        { text: 'Department', pinned: true, dataField: 'department_name', filtertype: 'list', width: '80', rendered: tooltiprenderer},
                        { text: 'Designation', pinned: true, dataField: 'designation', filtertype: 'list', width: '100', rendered: tooltiprenderer},
                        { text: 'Title', dataField: 'title', rendered: tooltiprenderer},
                        { text: 'Date From', dataField: 'date_from', width: '100', rendered: tooltiprenderer},
                        { text: 'Date To', dataField: 'date_to', width: '100', rendered: tooltiprenderer},
                        { text: 'IOU Amount (Request)', dataField: 'amount_iou_request', width: '100', rendered: tooltiprenderer, cellsAlign:'right',cellsrenderer: cellsrenderer},
                        { text: 'IOU Amount (Adjustment)', dataField: 'amount_iou_adjustment', width: '100', rendered: tooltiprenderer, cellsAlign:'right',cellsrenderer: cellsrenderer},
                        { text: 'Forward Status', dataField: 'status_forwarded_tour', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Approve Status', dataField: 'status_approved_tour', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Report Forward Status', dataField: 'status_forwarded_reporting', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Report Approve Status', dataField: 'status_approved_reporting', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Payment Approve Status', dataField: 'status_approved_payment', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Payment Paid Status', dataField: 'status_paid_payment', filtertype: 'list', width: '110', rendered: tooltiprenderer},
                        { text: 'Payment Adjustment Approve Status', dataField: 'status_approved_adjustment', filtertype: 'list', width: '110', rendered: tooltiprenderer}
                    ]
            });
    });
</script>
