<?php 
  if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
  
  if( !isset( $_REQUEST["order_number"] ) || empty( $_REQUEST["order_number"] )) {
    echo $VM_LANG->_('VM_CHECKOUT_ORDERIDNOTSET');
  } else {
    $order_number = $_GET['order_number'];
    $qv = "SELECT order_id, order_number FROM jos_vm_orders WHERE order_number='" . $order_number . "'";
    $dbbt = new ps_DB;
    $dbbt->query($qv);
    if ($dbbt->next_record()) {
      $d['order_id'] = $dbbt->f("order_id"); ?>
      <h2>Your transaction is pending confirmation on the blockchain.</h2>
      <h2>The payment status will be updated when it's confirmed.</h2>
      <h2>Order number: <?php echo $dbbt->f("order_number") ?> </h2>
      <a href="<?php @$sess->purl(SECUREURL."index.php?option=com_virtuemart&page=account.order_details&order_id=".$d['order_id']) ?>">
        View full payment invoice
      </a>
  <?php
    } else {
      ?>
      <h2> Unable to find order </h2>
      <?php
    }
  } 

?>
  
