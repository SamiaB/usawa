<?php

require_once("include/usawa_base.inc.php");

function form_vrrp_details_per_server($virtual_router_id = NULL)
{
  global $mysqli;
  
  if ( $virtual_router_id ) {
  
    $sql = "select 
              v.name as vrrp_name, 
              s.lb_id, 
              s.name as server_name, 
              state, 
              priority 
            from 
              vrrp_instance v
            left join server s on v.cluster_id = s.cluster_id 
            left join vrrp_details_per_server d on s.lb_id = d.lb_id 
            where v.virtual_router_id='$virtual_router_id'
            group by s.lb_id";

    if ( ($res = $mysqli->query($sql) ) && $res->num_rows) {
      $row = $res->fetch_assoc();
      extract($row);
    }
  } else {
    return(false);
  }
    
?>

  <form name = "vrrp_details_form" method="POST">
  <fieldset>
    <legend>Server details for VRRP instance <?php echo $vrrp_name ?></legend>
    <div class="error_box"></div>
    
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="f_type" value="vrrp_details_per_server" />
    <input type="hidden" name="virtual_router_id" value="<?php echo $virtual_router_id ?>" />

<?php
  $cpt_server = 0;
  $res->data_seek(0);
  while ( $row = $res->fetch_assoc() )
  {
    $cpt_server++;
    extract($row);
        
?>        
    <div>
      <label><?php echo $server_name; ?> Prio.</label>
      <input type="text" style="width:3em; display:inline" name="priority[<?php echo $lb_id ?>]" maxlength="3" value="<?php echo $priority?$priority:VRRP_DEFAULT_PRIORITY ?>" />
      <input type="radio" name="state[<?php echo $lb_id ?>]" value="MASTER" <?php echo (@$state=="MASTER")?'checked="checked"':"" ?>/>MASTER
      <input type="radio" name="state[<?php echo $lb_id ?>]" value="BACKUP" <?php echo (!@$state||$state=="BACKUP")?'checked="checked"':"" ?>/>BACKUP
    </div>

<?php
  }

?>
    <div><label for="buttons">&nbsp;</label> <input class="styled-button-10" type="submit" value="Submit" /></div>
    
    
  </fieldset>
  </form>
  
<?php

}

if(isset( $_REQUEST['virtual_router_id'] ) ) $virtual_router_id = $_REQUEST['virtual_router_id']; else $virtual_router_id= NULL;


form_vrrp_details_per_server($virtual_router_id);