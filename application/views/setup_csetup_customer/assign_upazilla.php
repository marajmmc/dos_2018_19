<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_assign_upazilla');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $customer_info['id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php

        ?>
        <div style="" class="row show-grid">
            <div class="col-xs-12">
                <?php
                foreach($upazillas as $upazilla)
                {
                    ?>
                    <div class="checkbox">
                        <label title="<?php echo $upazilla['upazilla_name']; ?>">
                            <input type="checkbox" name="upazillas[]" value="<?php echo $upazilla['upazilla_id']; ?>" <?php if(in_array($upazilla['upazilla_id'],$assigned_upazillas)){echo 'checked';} ?>><?php echo $upazilla['upazilla_name']; ?>
                        </label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
