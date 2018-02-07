<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/search_crop_type_acres/'.$item['id'])
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
    <form id="search_form" action="<?php echo site_url($CI->controller_url.'/index/list_crop_type_acres');?>" method="post">
        <input type="hidden" name="report[upazilla_id]" value="<?php echo $item['id']; ?>">
        <div class="row show-grid">
            <div style="" class="row show-grid" id="crop_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <select id="crop_id" name="report[crop_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>

        </div>
            <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_report" type="button" class="btn" data-form="#search_form">Load Form</button>
                </div>

            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>


<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $('#crop_id').html(get_dropdown_with_select(system_crops));
        $(document).off('change', '#crop_id');
        $(document).on('change', '#crop_id',function()
        {
            $('#system_report_container').empty();
        });
    });

</script>
