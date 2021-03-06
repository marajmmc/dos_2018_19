<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse_preference" href="#">+ Preference</a></label>
        </div>
        <div class="clearfix"></div>
    </div>
    <div id="collapse_preference" class="panel-collapse collapse">
        <?php
        if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
        {
            $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
        }
        ?>
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
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size', type: 'string' },
                <?php
                foreach($warehouses as $warehouse)
                {
                ?>
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_in_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_excess_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_delivery_short_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_ww_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_convert_bulk_pack_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_lc_pkt', type: 'string' },

                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_sample_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_rnd_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_demonstration_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_short_inventory_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_delivery_excess_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_convert_bulk_pack_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_ww_pkt', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_wo_pkt', type: 'string' },

                { name: 'warehouse_<?php echo $warehouse['value'];?>_pkt', type: 'string' },

                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_in_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_excess_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_stock_delivery_short_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_ww_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_convert_bulk_pack_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_in_lc_kg', type: 'string' },

                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_sample_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_rnd_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_demonstration_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_short_inventory_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_stock_delivery_excess_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_convert_bulk_pack_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_ww_kg', type: 'string' },
                { name: 'warehouse_<?php echo $warehouse['value'];?>_out_wo_kg', type: 'string' },

                { name: 'warehouse_<?php echo $warehouse['value'];?>_kg', type: 'string' },
                <?php
                    }
                    ?>
                { name: 'current_stock_pkt', type: 'string' },
                { name: 'current_stock_kg', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            //console.log(defaultHtml);
            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;

        };

        var aggregates=function (total, column, element, record)
        {
            //console.log(record);
            //console.log(record['warehouse_5_pkt']);
            if(record.crop_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['variety_name']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_PACK_SIZE'); ?>', dataField: 'pack_size',pinned:true,width:'100',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['pack_size']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        <?php
                    foreach($warehouses as $warehouse)
                    {
                    ?>
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_IN_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_in_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_in_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_EXCESS_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_excess_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_excess_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_DELIVERY_SHORT_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_delivery_short_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_delivery_short_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_WW_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_ww_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_ww_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_CONVERT_BULK_PACK_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_convert_bulk_pack_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_convert_bulk_pack_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_LC_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_lc_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_lc_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},

                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_SAMPLE_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_sample_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_sample_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_RND_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_rnd_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_rnd_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_DEMONSTRATION_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_demonstration_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_demonstration_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_SHORT_INVENTORY_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_short_inventory_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_short_inventory_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_DELIVERY_EXCESS_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_delivery_excess_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_delivery_excess_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_CONVERT_BULK_PACK_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_convert_bulk_pack_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_convert_bulk_pack_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_WW_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_ww_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_ww_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_WO_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_wo_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_wo_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},

                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_PKT'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_pkt']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},

                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_IN_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_in_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_in_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_EXCESS_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_excess_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_excess_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_STOCK_DELIVERY_SHORT_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_stock_delivery_short_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_stock_delivery_short_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_WW_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_ww_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_ww_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_CONVERT_BULK_PACK_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_convert_bulk_pack_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_convert_bulk_pack_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_IN_LC_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_in_lc_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_in_lc_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},

                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_SAMPLE_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_sample_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_sample_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_RND_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_rnd_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_rnd_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_DEMONSTRATION_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_demonstration_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_demonstration_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_SHORT_INVENTORY_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_short_inventory_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_short_inventory_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_STOCK_DELIVERY_EXCESS_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_stock_delivery_excess_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_stock_delivery_excess_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_CONVERT_BULK_PACK_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_convert_bulk_pack_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_convert_bulk_pack_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_WW_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_ww_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_ww_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_OUT_WO_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_out_wo_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_out_wo_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},

                        { text: '<?php echo $CI->lang->line('LABEL_'.'WAREHOUSE_'.$warehouse['value'].'_KG'); ?>', dataField: 'warehouse_<?php echo $warehouse['value']?>_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['warehouse_'.$warehouse['value'].'_kg']?0:1;?>, width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        <?php
                            }
                            ?>
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_PKT'); ?>', dataField: 'current_stock_pkt',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['current_stock_pkt']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?>', dataField: 'current_stock_kg',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,hidden: <?php echo $system_preference_items['current_stock_kg']?0:1;?>,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
                    ]
            });
    });
</script>
