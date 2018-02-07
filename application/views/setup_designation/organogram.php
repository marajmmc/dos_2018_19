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
    'href'=>site_url($CI->controller_url.'/index/organogram_view')
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
    <div class="col-xs-12" style="overflow-x: auto;">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th><?php echo $CI->lang->line("ID"); ?></th>
                <th><?php echo $CI->lang->line("NAME"); ?></th>
                <th><?php echo $CI->lang->line("LABEL_ORDER"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(sizeof($items)>0)
            {
                foreach($items as $item)
                {
                    ?>
                    <tr>
                        <td><?php echo $item['designation']['id']; ?></td>
                        <td><?php echo $item['prefix']; ?><?php echo $item['designation']['name']; ?></td>
                        <td><?php echo $item['designation']['ordering']; ?></td>
                    </tr>
                <?php
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="20" class="text-center alert-danger">
                        <?php echo $CI->lang->line('NO_DATA_FOUND'); ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
