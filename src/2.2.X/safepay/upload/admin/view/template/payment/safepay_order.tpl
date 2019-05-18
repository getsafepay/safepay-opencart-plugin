<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <td colspan="2"><?php $tab_order_safepay_details; ?></td>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($meta_values as $meta_value) { ?>
    <tr>
      <td><?php echo $meta_value['meta_key']; ?></td>
      <td><?php echo $meta_value['meta_value']; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>