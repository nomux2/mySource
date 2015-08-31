<?php 

//定数
define("TAX", 0.05);
define("SIZE_SMALL_WEIGHT", 0.25);
define("SIZE_BASIC_WEIGHT", 9);
define("SIZE_BIG_WEIGHT", 30);

define("SIZE_SMALL_WIDTH_LENGTH", 25);
define("SIZE_SMALL_HEIGHT_LENGTH", 18);
define("SIZE_SMALL_LEN_LENGTH", 2);

define("SIZE_BASIC_WIDTH_LENGTH", 45);
define("SIZE_BASIC_HEIGHT_LENGTH", 35);
define("SIZE_BASIC_LEN_LENGTH", 20);

define("SIZE_BIG1_LENGTHT", 100);
define("SIZE_BIG2_LENGTHT", 140);

define("SIZE_HIGH_PRICE", 45000);

define("HOKAN_BASIC_PRICE", 8.126);

define("SYUKA_MEDIA_SMALL_PRICE", 86);
define("SYUKA_MEDIA_BASIC_PRICE", 86);
define("SYUKA_NOMEDIA_SMALL_PRICE", 76);
define("SYUKA_NOMEDIA_BASIC_PRICE", 98);
define("SYUKA_BIG1_PRICE", 515);
define("SYUKA_BIG2_PRICE", 555);
define("SYUKA_BIG3_PRICE", 590);
define("SYUKA_HIGH_PRICE", 0);

define("HAISO_MEDIA_SMALL_PRICE", 56);
define("HAISO_MEDIA_BASIC_PRICE", 78);
define("HAISO_NOMEDIA_SMALL_PRICE", 163);
define("HAISO_NOMEDIA_BASIC_PRICE", 221);
define("HAISO_BIG1_PRICE", 0);
define("HAISO_BIG2_PRICE", 0);
define("HAISO_BIG3_PRICE", 0);
define("HAISO_HIGH_PRICE", 0);


//****************************************************************************
// Function     : getConditionName
// Description  : コンディション名(日本語)を取得する
//****************************************************************************
function getConditionName($inStr)
{
    switch ($inStr) {
        case "New":
            return "新品";
            break;
        case "Used":
            return "中古品";
            break;
        case "Collectible":
            return "コレクター品";
            break;
        case "Refurbished":
            return "再生品";
            break;
        default:
            return inStr;
            break;
    }
}

//****************************************************************************
// Function     : getItemSubconditionName
// Description  : サブコンディション名(日本語)を取得する
//****************************************************************************
function getItemSubconditionName($inStr)
{
    switch ($inStr) {
        case "New":
            return "新品";
            break;
        case "Mint":
            return "ほぼ新品";
            break;
        case "Very Good":
            return "非常に良い";
            break;
        case "VeryGood":
            return "非常に良い";
            break;
        case "Good":
            return "良い";
            break;
        case "Acceptable":
            return "可";
            break;
        default:
            return inStr;
            break;
    }
}

//****************************************************************************
// Function     : getItemSubconditionShortName
// Description  : サブコンディション略名(日本語)を取得する
//****************************************************************************
function getItemSubconditionShortName($inStr)
{
    switch ($inStr) {
        case "New":
            return "新品";
            break;
        case "Mint":
            return "ほ新";
            break;
        case "Very Good":
            return "非良";
            break;
        case "VeryGood":
            return "非良";
            break;
        case "Good":
            return "良";
            break;
        case "Acceptable":
            return "可";
            break;
        default:
            return inStr;
            break;
    }
}


//****************************************************************************
// Function     : getFulfillmentChannelName
// Description  : 出荷元(日本語)を取得する
//****************************************************************************
function getFulfillmentChannelName($inStr)
{
    switch ($inStr) {
        case "Amazon":
            return "FBA";
            break;
        case "Merchant":
            return "出品者";
            break;
        default:
            return inStr;
            break;
    }
}


//****************************************************************************
// Function     : getPriceName
// Description  : 価格(通貨単位付き)を取得する
//****************************************************************************
function getPriceName($inData)
{
/*
    // 通貨単位のセット
    if inData->IsSetCurrencyCode() = True {

        switch inData.CurrencyCode
            case "JPY"
                outStr += "\\"
            case "USD"
                outStr += "$"
            case } else {
                outStr += "(" + inData->CurrencyCode + ")"
        }

    }

    // 金額のセット
    if inData.IsSetAmount() = True {
        outStr += String.Format("{0:0}", inData->Amount)
    }

    return outStr;*/

}

//****************************************************************************
// Function     : getItemSize
// Description  : 商品のサイズを取得する
//****************************************************************************
function getItemSize($weight, $width, $height, $length, $price)
{
    $decSizes = array($width, $height, $length);
    $resSize = "None";
	
	$res = sort($decSizes, SORT_NUMERIC);
	
    if (!$res) {
    	echo("ソート失敗");
    	return $resSize; 
    }


    if ($decSizes[2] < SIZE_SMALL_WIDTH_LENGTH && $decSizes[1] < SIZE_SMALL_HEIGHT_LENGTH && $decSizes[0] < SIZE_SMALL_LEN_LENGTH) {

		//重さから大きさを判断
		if ($weight < SIZE_SMALL_WEIGHT) {
		        //高額商品チェック
		        if ($price > SIZE_HIGH_PRICE) {
		            $resSize = "High";
		        }
		        
		        $resSize = "Small";

		} elseif ($weight < SIZE_BASIC_WEIGHT) {
		        //高額商品チェック
		        if ($price > SIZE_HIGH_PRICE) {
		            $resSize = "High";
		        }

		        $resSize = "Basic";
		        
		} else {
		        $resSize = "Big1";
		}

		return $resSize;

	} elseif ($decSizes[2] < SIZE_BASIC_WIDTH_LENGTH && $decSizes[1] < SIZE_BASIC_HEIGHT_LENGTH && $decSizes[0] < SIZE_BASIC_LEN_LENGTH) {
	
		//重さから大きさを判断
		if ($weight < SIZE_BASIC_WEIGHT) {
			$resSize = "Basic";
		} else {
			$resSize = "Big1";
		}

		return $resSize;

    } elseif (($decSizes[0] + $decSizes[1] + $decSizes[2]) < SIZE_BIG1_LENGTHT) {
		$resSize = "Big1";
    } elseif (($decSizes[0] + $decSizes[1] + $decSizes[2]) < SIZE_BIG2_LENGTHT) {
		$resSize = "Big2";
    } else {
		$resSize = "Big3";
    }

}

//****************************************************************************
// Function     : getFBAItemHokanPrice
// Description  : 保管料を取得する
//****************************************************************************
function getFBAItemHokanPrice($width, $height, $length)
{
    $res_item_price = 0;

    $res_item_price = HOKAN_BASIC_PRICE * $width * $height * $length;

    //一月の保管料を計算
    $res_item_price = $res_item_price / (10 * 10 * 10);

    //保管日数をかける
    $res_item_price = $res_item_price;

    //小数点以下を四捨五入
    return round($res_item_price);

}

//****************************************************************************
// Function     : getFBAItemSyukaPrice
// Description  : 出荷作業手数料を取得する
//****************************************************************************
function getFBAItemSyukaPrice($kind, $weight, $width, $height, $length, $price)
{

    $item_size;
    $syuka_price = 0;

    //商品サイズを取得する
    $item_size = getItemSize($weight, $width, $height, $length, $price);

    switch ($item_size)
    {
        case "Small":
            if ($kind == 1) {
                return SYUKA_MEDIA_SMALL_PRICE;
            } else {
                return SYUKA_NOMEDIA_SMALL_PRICE;
            }
            break;
        case "Basic":
            if ($kind == 1) {
                return SYUKA_MEDIA_BASIC_PRICE;
            } else {
                return SYUKA_NOMEDIA_BASIC_PRICE;
            }
            break;
        case "Big1":
            return SYUKA_BIG1_PRICE;
            break;
        case "Big2":
            return SYUKA_BIG2_PRICE;
            break;
        case "Big3":
            return SYUKA_BIG3_PRICE;
            break;
        case "High":
            return SYUKA_HIGH_PRICE;
            break;
        default:
            return 0;
            break;
    }

}
//****************************************************************************
// Function     : getFBAItemHaisoPrice
// Description  : 配送作業手数料を取得する
//****************************************************************************
function getFBAItemHaisoPrice($kind, $weight, $width, $height, $length, $price)
{
    $item_size;
    $syuka_price = 0;

    //商品サイズを取得する
    $item_size = getItemSize($weight, $width, $height, $length, $price);

    switch ($item_size)
    {
        case "Small":
            if ($kind == 1) {
                return HAISO_MEDIA_SMALL_PRICE;
            } else {
                return HAISO_NOMEDIA_SMALL_PRICE;
            }
            break;
        case "Basic":
        
			$resPrice = 0;
            if ($kind == 1) {
                $resPrice = HAISO_MEDIA_BASIC_PRICE;
            } else {
                $resPrice = HAISO_NOMEDIA_BASIC_PRICE;
            }

            //加重料金2kg以上1kgごとに6円
            $resPrice = $resPrice + (ceil($weight - 2) * 6);
            
            return $resPrice;

        case "Big1":
            return HAISO_BIG1_PRICE;
        case "Big2":
            return HAISO_BIG2_PRICE;
        case "Big3":
            return HAISO_BIG3_PRICE;
        case "High":
            return HAISO_HIGH_PRICE;
        default:
            return 0;
	}

}

//****************************************************************************
// Function     : getItemSize
// Description  : 販売手数料を取得する
//****************************************************************************
function getFBAItemSellPrice($price, $genre)
{

    $resitemprice = 0;
    $category_price = 0;
    $selling_price = 0;

	switch ($genre)
	{
	    case "Books":    //本
	        $category_price = 60;
			break;
	    case "Music" :   //CD&レコード    
	        $category_price = 140;
			break;
	    case "Video": //ビデオ
	        $category_price = 30;
			break;
	    case "DVD": //DVD
	        $category_price = 140;
			break;
	}

	switch ($genre)
	{
        case "Baby":
            $selling_price = $price * 0.15;
			break;
        case "Books":
            $selling_price = $price * 0.15;
			break;
        case "DVD":
            $selling_price = $price * 0.15;
			break;
        case "Blu-ray":
            $selling_price = $price * 0.15;
			break;
        case "Electronics":
            $selling_price = $price * 0.1;
			break;
        case "Home Theater":
            $selling_price = $price * 0.1;
			break;
        case "PCHardware":
            $selling_price = $price * 0.1;
			break;
        case "Personal Computer":
            $selling_price = $price * 0.1;
			break;
        case "Automotive":
            $selling_price = $price * 0.15;
			break;
        case "GPS or Navigation System":
            $selling_price = $price * 0.15;
			break;
        case "ForeignBooks":
            $selling_price = $price * 0.15;
			break;
        case "Hobby":
            $selling_price = $price * 0.15;
			break;
        case "Hobbies":
            $selling_price = $price * 0.15;
			break;
        case "Kitchen":
            $selling_price = $price * 0.15;
			break;
        case "Music":
            $selling_price = $price * 0.15;
			break;
        case "MusicTracks":
            $selling_price = $price * 0.15;
			break;
        case "Software":
            $selling_price = $price * 0.15;
			break;
        case "SportingGoods":
            $selling_price = $price * 0.15;
			break;
        case "Toy":
            $selling_price = $price * 0.15;
			break;
        case "Toys":
            $selling_price = $price * 0.15;
			break;
        case "VHS":
            $selling_price = $price * 0.15;
			break;
        case "Video":
            $selling_price = $price * 0.15;
			break;
        case "Video Games":
            $selling_price = $price * 0.15;
			break;
        case "VideoGames":
            $selling_price = $price * 0.15;
			break;
        case "Watches":
            $selling_price = $price * 0.15;
			break;
        default: //その他
            $selling_price = $price * 0.15;
			break;
	}

	return ceil($category_price + $selling_price);

}

//****************************************************************************
// Function     : getFBAItemFee
// Description  : 各種手数料合計の取得
//****************************************************************************
function getFBAItemFee($price, $genre, $weight, $width, $height, $length)
{


    $decFee = 0;
    
    //サイズが不明のものは単純に2割
    if (($width + $height + $length) == 0) {
	    return floor($price * 0.2); 
    }

	switch ($genre)
	{
		case "Books":
		case "Music":
		case "DVD":
			//メディア系出荷手数料＆配送手数料
			$decFee = $decFee + getFBAItemSyukaPrice(1, $weight, $width, $height, $length, $price);
			$decFee = $decFee + getFBAItemHaisoPrice(1, $weight, $width, $height, $length, $price);
			break;
		default:
			//メディア以外系出荷手数料＆配送手数料
			$decFee = $decFee + getFBAItemSyukaPrice(0, $weight, $width, $height, $length, $price);
			$decFee = $decFee + getFBAItemHaisoPrice(0, $weight, $width, $height, $length, $price);
			break;
	}

	//手数料に取得に失敗している場合は、単純に2割
	if ($decFee == 0) {
	    return floor($price * 0.2); 
	}

    $decFee = $decFee + getFBAItemHokanPrice($width, $height, $length);
	$decFee = $decFee + getFBAItemSellPrice($price, $genre);

	return $decFee;

}

//****************************************************************************
// Function     : getFBAItemFee
// Description  : 各種手数料合計の取得(販売手数料を含めない)
//****************************************************************************
function getFBAItemFee2($price, $genre, $weight, $width, $height, $length)
{


    $decFee = 0;
    
    //サイズが不明のものは単純に2割
    if (($width + $height + $length) == 0) {
		switch ($genre)
		{
			case "Books":
			case "Music":
			case "DVD":
			case "VideoGames":
			    return 300; 
				break;
			default:
			    return 600; 
		}
    }

	switch ($genre)
	{
		case "Books":
		case "Music":
		case "DVD":
			//メディア系出荷手数料＆配送手数料
			$decFee = $decFee + getFBAItemSyukaPrice(1, $weight, $width, $height, $length, $price);
			$decFee = $decFee + getFBAItemHaisoPrice(1, $weight, $width, $height, $length, $price);
			break;
		default:
			//メディア以外系出荷手数料＆配送手数料
			$decFee = $decFee + getFBAItemSyukaPrice(0, $weight, $width, $height, $length, $price);
			$decFee = $decFee + getFBAItemHaisoPrice(0, $weight, $width, $height, $length, $price);
			break;
	}

	//手数料に取得に失敗している場合は、ジャンルでおおよそを出す
	if ($decFee == 0) {
		switch ($genre)
		{
			case "Books":
			case "Music":
			case "DVD":
			case "VideoGames":
			    return 300; 
				break;
			default:
			    return 600; 
		}
	}

    $decFee = $decFee + getFBAItemHokanPrice($width, $height, $length);

	return $decFee;

}

//****************************************************************************
// Function     : sizetocm($Key, $value)
// Description  : センチメートルに変換
//****************************************************************************
function sizetocm($Key, $value)
{
	switch ($Key)
	{
		case "inches":
			return $value * 2.54;
			break;
		case "hundredths-inches":
			return $value * 0.0254;
			break;
		default:
			echo($Key . "\n");
			return $value;
			break;
	}
}

//****************************************************************************
// Function     : weighttoKg($Key, $value)
// Description  : Kgに変換
//****************************************************************************
function weighttoKg($Key, $value)
{
	switch ($Key)
	{
		case "pounds":
			return $value * 0.45359237;
			break;
		case "hundredths-pounds":
			return $value * 0.0045359237;
			break;
		default:
			echo($Key . "\n");
			return $value;
			break;
	}
}

?>