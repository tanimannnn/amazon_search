<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Cross Search - あまぞん商品検索</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <style type="text/css">
        div.box {
            width:400px; height:250px;
            margin:5px; padding:10px; border:1px solid black;
            background-color:white;
        } 
    </style>
</head>

<body>
<h1>Cross Search - あまぞん商品検索</h1>
<form action="" method="get">
<p>商品カテゴリ</p>
<?php
$indexList = array(
        "Apparel" => " アパレル",
        "Baby" => " ベビー&マタニティー",
        "Beauty" => " コスメ",
        "Books" => " 本（和書）",
        "Classical" => " クラシック音楽",
        "DVD" => " DVD",
        "Electronics" => " 家電・デジタル用品",
        "ForeignBooks" => " 洋書",
        "Grocery" => " 食品&飲料",
        "HealthPersonalCare" => " ヘルスケア",
        "Hobbies" => " ホビー",
        "Jewelry" => " ジュエリー",
        "Kitchen" => " ホーム&キッチン",
        "Music" => " ミュージック",
        "MusicTracks" => " 曲名",
        "OfficeProducts"    => " 文房具・オフィス用品",
        "Shoes"             => " シューズ&バッグ",
        "SoftWare" => " ソフトウェア",
        "SportingGoods" => " スポーツ&アウトドア",
        "Toys" => " おもちゃ",
        "VHS" => " VHS",
        "Vide" => " ビデオ",
        "VideoGames" => " ゲーム",
        "Watches"       => " 時計",
        );
$checkIndex = htmlspecialchars($_REQUEST['index'], ENT_QUOTES);
echo "<select name='index'>";
foreach ($indexList as $key => $value) {
    if ($key === $checkIndex) {
        echo "<option value='$key' selected>$value</option>";
        //echo "<input type='checkbox' name='index' value='$name' checked >$value";
    } else {
        echo "<option value='$key'>$value</option>";
        //echo "<input type='checkbox' name='index' value='$name'>$value";
    }
}
echo "</select>";
$keyword = "";
if(!empty($_REQUEST["keyword"])) {
    $keyword = htmlspecialchars($_REQUEST['keyword'], ENT_QUOTES);
}
echo '<input type="search" name="keyword" id="search-basic" size="35" maxlength="55" value="'.$keyword.'" />';
echo '<input type="submit" value="search" />';
echo '</form>';

require_once '/home/y/share/search/logic/AccessLibrary.php';

$access = new AccessLibrary();
if(!empty($_REQUEST["keyword"])) {
    $keyword = htmlspecialchars($_REQUEST['keyword'], ENT_QUOTES);
    $keyword = rawurlencode($keyword);
    $page    = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
    $index   = htmlspecialchars($_REQUEST['index'], ENT_QUOTES);
    //echo $access->search('Books', $keyword);
    $response = $access->Amazon($keyword, $index, $page);
    //var_dump($access->getCount());
    
    //var_dump($access->getpage());
    
    $items = $access->getItems();
   //var_dump($items);

    foreach($items as $item) {
        //var_dump($item);
        $url = $item['DetailPageURL'];
        $title = $item['ItemAttributes']['Title'];
        $price = $item['OfferSummary']['LowestNewPrice']['FormattedPrice'];
        $image = $item['MediumImage']['URL'];
        echo "<div class='box'>";
        echo "<a href=$url>$title</a><br/>";
        echo "<a href=$url><img src='$image'></a>";
        echo "<p>price: $price</p>";
        echo "</div>";
    }

    //var_dump($response->OperationRequest);
    //var_dump($response->OperationRequest->Arguments);
    //var_dump($access->Amazon($keyword));
    //var_dump(get_object_vars($access->Amazon($keyword)));

    echo '検索結果 : ' . $access->getCount() . '件';
    $totalPage = $access->getpage();
    if ($totalPage > 10) {
        $totalPage = 10;
    }
    for ($i = 1; $i <= $totalPage; $i++) { 
        $url = "?keyword=$keyword&page=$i&index=$index";
        echo "<a href='$url'>$i</a> ";
    }
}
    echo "検索ワードを入力してください。<br/>";

?>

<p>powered by amazon Product Advertising API</a></p>
</body>

</html>
