<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

if((isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save_jqx'
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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<form id="save_form_jqx" action="<?php echo site_url($CI->controller_url.'/index/save_crop_type_acres');?>" method="post">
    <input type="hidden" name="upazilla_id" value="<?php echo $options['upazilla_id']; ?>" />
    <input type="hidden" name="crop_id" value="<?php echo $options['crop_id']; ?>" />

    <div id="jqx_inputs">
    </div>
</form>

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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="acres">Acres</label>
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

        $(document).off('click', '#button_action_save_jqx');
        $(document).on("click", "#button_action_save_jqx", function(event)
        {
            $('#save_form_jqx #jqx_inputs').html('');
            var data=$('#system_jqx_container').jqxGrid('getrows');
            for(var i=0;i<data.length;i++)
            {
                $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['id']+']" value="'+data[i]['acres']+'">');
            }
            var sure = confirm('<?php echo $CI->lang->line('MSG_CONFIRM_SAVE'); ?>');
            if(sure)
            {
                $("#save_form_jqx").submit();
            }

        });

        var url = "<?php echo base_url($CI->controller_url.'/index/get_crop_type_acres');?>";

        // prepare the data
        var source =
        {
            type:'POST',
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'acres', type: 'string' }
            ],
            id: 'id',
            data:JSON.parse('<?php echo json_encode($options);?>'),
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            if(column=='acres' && <?php if((isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))){echo 'true';}else{echo 'false';} ?>)
            {
                element.html('<div class="jqxgrid_input">'+value+'</div>');
            }

            return element[0].outerHTML;

        };
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '40%',
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
                rowsheight: 35,
                autoheight:true,
                editable:true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'name',width:'70%',filtertype:'list',cellsrenderer: cellsrenderer,editable:false},
                    { text: 'Acres', dataField: 'acres',width:'30%',cellsalign: 'right',cellsrenderer: cellsrenderer
                        <?php
                        if((isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
                        {
                            ?>
                            ,columntype:'custom',
                            initeditor: function (row, cellvalue, editor, celltext, pressedkey) {
                                editor.html('<div style="margin: 0px;width: 100%;height: 100%;padding: 5px;"><input type="text" value="'+cellvalue+'" class="jqxgrid_input float_type_positive"><div>');
                            },
                            geteditorvalue: function (row, cellvalue, editor) {
                                // return the editor's value.
                                var value=editor.find('input').val();
                                var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);
                                return editor.find('input').val();
                            }
                            <?php
                        }
                        else
                        {
                            ?>
                            ,editable:false
                            <?php
                        }
                        ?>
                    }
                ]
            });
    });
</script>
