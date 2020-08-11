<?php header("X-FRAME-OPTIONS: DENY"); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print (h(STYLESHEET_PATH . 'index.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php  ?>
    <?php 
      if(isset($items)){
        if($now == $pages_num){
          print ('<p>全件数' . (h($items_num['num'])) . '件中' . (h($start + 1)) . '〜' . (h($items_num['num'])) . '件目の商品'); 
        }else{
          print ('<p>全件数' . (h($items_num['num'])) . '件中' . (h($start + 1)) . '〜' . (h($start + ITEMS_MAX_VIEW)) . '件目の商品'); 
        }
      }
    ?>
    <div class="card-deck">
      <div class="row">
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print (h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print (h(IMAGE_PATH . $item['image'])); ?>">
              <figcaption>
                <?php print (h(number_format($item['price']))); ?>円
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name = "csrf_token" value="<?php print $token;?>">
                    <input type="hidden" name="item_id" value="<?php print (h($item['item_id'])); ?>">
                  </form>
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <nav aria-label="Page Navigation" class="mt-3">
      <ul class="pagination justify-content-center">
        <?php if($now > 1){ ?>
          <li class="page-item">
            <a  class="page-link" href="./index.php?page=<?php print (h($now - 1)); ?>" aria-label="Previous Page">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
        <?php } ?>
        <?php
          for($i=1; $i <= $pages_num; $i++){
            if($i == $now){
              print ('<li class="page-item active"><a class="page-link" href="#">' . $now . '</a></li>');
            }else{
              print ('<li class="page-item"><a class="page-link" href="' . './index.php?page=' . $i . '">' . $i . '</a></li>');
            }
          }
        ?>
        <?php if($pages_num > $now){ ?>
          <li class="page-item">
            <a  class="page-link" href="./index.php?page=<?php print (h($now + 1)); ?>" aria-label="Next Page">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        <?php } ?>
      </ul>
    </nav>
  </div>

</body>
</html>