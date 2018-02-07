<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
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
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_preference');?>" method="post">
    <input type="hidden" id="id" name="id" value="" />
    <input type="hidden" id="method_name" name="preference[method_name]" value="list" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-12 text-center">
                <div class="checkbox  btn btn-danger">
                    <label>
                        <input type="checkbox" class="allSelectCheckbox" name="" checked>
                        <?php echo $CI->lang->line('ALL_SELECT_CHECKBOX'); ?>
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[name]" <?php if($items['name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[name_short]" <?php if($items['name_short']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_SHORT_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[type_name]" <?php if($items['type_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CUSTOMER_TYPE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[division_name]" <?php if($items['division_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[zone_name]" <?php if($items['zone_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[territory_name]" <?php if($items['territory_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[district_name]" <?php if($items['district_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[customer_code]" <?php if($items['customer_code']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[incharge_name]" <?php if($items['incharge_name']){echo 'checked';}?> value="1"><span class="label label-success">In-charge</span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[phone]" <?php if($items['phone']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_PHONE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[ordering]" <?php if($items['ordering']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_ORDER'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[status]" <?php if($items['status']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('STATUS'); ?></span></label>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        //system_preset({controller:'<?php //echo $CI->router->class; ?>'});

        $(document).on("click",'.allSelectCheckbox',function()
        {
            if($(this).is(':checked'))
            {
                $('input:checkbox').prop('checked', true);
            }
            else
            {
                $('input:checkbox').prop('checked', false);
            }
        });
    });

</script>