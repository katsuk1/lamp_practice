<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'purchase_detail.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>購入明細</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($details) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <td><?php print (h($history_id)); ?></td>
            <th>購入日時</th>
            <td><?php print (h($created)); ?></td>
            <th>合計金額</th>
            <td><?php print (h(number_format($total_price))); ?>円</td>
          </tr>
        </thead>
      </table>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){ ?>
          <tr>
            <td><?php print (h($detail['name'])); ?></td>
            <td><?php print (h(number_format($detail['price']))); ?>円</td>
            <td><?php print (h($detail['amount'])); ?></td>
            <td><?php print (h(number_format($detail['sub_total']))); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入明細はありません。</p>
    <?php } ?> 
  </div>
  
</body>
</html>