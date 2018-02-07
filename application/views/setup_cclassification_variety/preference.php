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
                    <label><input type="checkbox" name="items[id]" <?php if($items['id']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('ID'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[name]" <?php if($items['name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[crop_name]" <?php if($items['crop_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[crop_type_name]" <?php if($items['crop_type_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[whose]" <?php if($items['whose']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_WHOSE'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[competitor_name]" <?php if($items['competitor_name']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?></span></label>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="checkbox">
                    <label><input type="checkbox" name="items[stock_id]" <?php if($items['stock_id']){echo 'checked';}?> value="1"><span class="label label-success"><?php echo $CI->lang->line('LABEL_STOCK_ID'); ?></span></label>
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