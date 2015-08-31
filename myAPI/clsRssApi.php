<?php
/**
 * clsRssApi
 *
 * @package		RSS一括取得クラス
 * @subpackage	
 * @category	API
 * @author		作成者
 * @link		
 **/
 
 
//インクルード
require_once(realpath(dirname(__FILE__) . "/../cls_cmn/clsDB.php"));
 
//実効結果データ格納用クラス(構造体のかわり)
class ItemRssapi {
	
	public $genre = "";			//ジャンル
	public $site_name = "";		//サイト名
	public $title = ""; 		//記事のタイトルです。
	public $link = ""; 			//記事の URL です。
	public $description = "";	//記事の粗筋です。
	public $author = ""; 		//記事の著者の Email アドレスです。
	public $category = ""; 		//一つ以上のカテゴリー内に記事を含みます。
	public $comments = ""; 		//記事に関連するコメントのページ URL です。
	public $enclosure = ""; 	//記事に添付されるメディアオブジェクトを記述します。
	public $guid = ""; 			//記事を一意に特定できる文字列です。
	public $pubdate = ""; 		//記事が発行された日時を表します。
	public $source = ""; 		//記事が引用された RSS チャンネルです。

}

//取得するサイト格納用クラス(構造体のかわり)
class urlRssapi {
	
	public $genre = "";			//ジャンル
	public $site_name = "";		//サイト名
	public $rss_url = "";		//RSSのURL

}

//RSS一括取得クラス
class clsRssApi
{

	private $rss_urls = array();
	
	public $title = "";
	public $link = "";
	public $description = "";
	
	public $items = array();
	
	public $errormsg = "";
	
    // メソッドの宣言
    public function init() {
		$rss_urls = array();
		$title = "";
		$link = "";
		$description = "";
		$items = array();
    }
    
	//****************************************************************************
	// Function     : getRssUrl($site)
	// Description  : 名称でRSSURLをDBから取得
	//****************************************************************************
    public function getRssUrl($site)
    {
		$db = new clsDB();
	
		//DBコネクション
		if ($db->connect() == -1){
			$this->errormsg = $db->errormsg;
			return -1;
		}
		
		//SQL生成
		$db->setSQL("select GENRE, SITE_URL from mst_rss Where SITE_NAME = '" . $site . "' ");

		//SQL実行
		if ($db->execute() == -1){
			//dbクローズ
			$db->close();
			$this->errormsg = $db->errormsg;
			return -1;
		}
		
		//結果取得
		if (!$db->fetch())
		{
			$this->errormsg = "サイト情報の取得に失敗しました。";
			return -1;
		}
		
		//追加インデックスを取得
    	if (!isset($this->rss_urls))
    	{
    		$idx = 0;
    	} else {
			$idx = count($this->rss_urls);
    	}
		
		//リクエストURLの追加
		$this->rss_urls[$idx] = new urlRssapi();
		
		$this->rss_urls[$idx]->genre = $db->row('GENRE');
		$this->rss_urls[$idx]->site_name = $site;
		$this->rss_urls[$idx]->rss_url = $db->row('SITE_URL');
		
		//dbクローズ
		$db->close();
		
		return 0;
    }
    
	//****************************************************************************
	// Function     : setRssUrl($genre, $site, $url)
	// Description  : URL設定
	//****************************************************************************
    public function setRssUrl($genre, $site, $url)
    {
		//追加インデックスを取得
    	if (!isset($this->rss_urls))
    	{
    		$idx = 0;
    	} else {
			$idx = count($this->rss_urls);
    	}
		
		//リクエストURLの追加
		$this->rss_urls[$idx] = new urlRssapi();
		
		$this->rss_urls[$idx]->genre = $genre;
		$this->rss_urls[$idx]->site_name = $site;
		$this->rss_urls[$idx]->rss_url = $url;
   }
    

	//****************************************************************************
	// Function     : getGenreRssUrls($genre = "")
	// Description  : ジャンルでRSSURLリストをDBから一覧取得
	//****************************************************************************
    public function getGenreRssUrls($genre = "")
    {
		$db = new clsDB();
	
		//DBコネクション
		if ($db->connect() == -1){
			$this->errormsg = $db->errormsg;
			return -1;
		}
		
		//取得SQL
		$sql = "select GENRE, SITE_NAME, SITE_URL from mst_rss ";
		
		if ($genre <> "") $sql .= " where GENRE = '" . $genre . "' ";
		
		//SQL生成
		$db->setSQL($sql);
		
		//SQL実行
		if ($db->execute() == -1){
			//dbクローズ
			$db->close();
			$this->errormsg = $db->errormsg;
			return -1;
		}
		
		//結果取得
		while ($db->fetch())
		{
			//追加インデックスを取得
	    	if (!isset($this->rss_urls))
	    	{
	    		$idx = 0;
	    	} else {
				$idx = count($this->rss_urls);
	    	}
	    	
			//リクエストURLの追加
			$this->rss_urls[$idx] = new urlRssapi();
			
			$this->rss_urls[$idx]->genre = $db->row('GENRE');
			$this->rss_urls[$idx]->site_name = $db->row('SITE_NAME');
			$this->rss_urls[$idx]->rss_url = $db->row('SITE_URL');
		}
		
		//dbクローズ
		$db->close();
		
		return 0;
    }

	//****************************************************************************
	// Function     : execute() 
	// Description  : 処理の実行
	//****************************************************************************
    public function execute() 
	{
	
		//URLリストが設定されていなかったらエラー
		if (!isset($this->rss_urls))
		{
			$this->errormsg = "サイトが設定されていません。";
			return -1;
		}
	
		//xmlの取得
		$xml = $this->GetRequest($this->rss_urls);
		
		if ($xml < 0)
		{
			return -1;
		}
		
		return 0;
	}
	
	//****************************************************************************
	// Function     : GetRequest($urls)
	// Description  : RSSの取得
	//****************************************************************************
    public function GetRequest($urls)
    {

		// マルチ cURL ハンドルを作成します
		$mh = curl_multi_init();
		$ch = array();
		
		foreach($urls as $url)
		{
			//セッション初期化
			$ch[$url->site_name] = curl_init();
			
			//セッションハンドルのオプションを設定
			//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($ch[$url->site_name], CURLOPT_RETURNTRANSFER, 1);		//実行結果を返却します
			curl_setopt($ch[$url->site_name], CURLOPT_URL, $url->rss_url);		//URL設定
			curl_setopt($ch[$url->site_name], CURLOPT_TIMEOUT, 60);				//タイムアウト設定
	        curl_setopt($ch[$url->site_name], CURLOPT_SSL_VERIFYPEER, false);	//SSL証明書を無視します
	        
			//ハンドルの追加
	        curl_multi_add_handle($mh, $ch[$url->site_name]); 
	        
		}

		$active = null;
		 // ハンドルを実行します
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		
		//結果のチェック
		if ( ! $active || $mrc !== CURLM_OK) {
			$this->errormsg = "リクエストを開始できませんでした。";
			
			// ハンドルを閉じます
			foreach($ch as $c) 
			{
				curl_multi_remove_handle($mh, $c);
			}
			
			curl_multi_close($mh);
			
			return -1;
		} 
		
		while ($active) {
		
			$res = curl_multi_select($mh);
			
			switch ($res) {
				case -1:
			        usleep(10); //ちょっと待ってからretry。
			        do {
			            $mrc = curl_multi_exec($mh, $active);
			        } while ($mrc === CURLM_CALL_MULTI_PERFORM);
					break;
					
				case 0:
					//タイムアウト
					break;
					
				default:
					//どれかが成功 or 失敗した
					do {
						$mrc = curl_multi_exec($mh, $active);
					} while ($mrc == CURLM_CALL_MULTI_PERFORM);
					
			        do {
						if ($raised = curl_multi_info_read($mh, $remains)) {
						
							//変化のあったcurlハンドラを取得する
							$info = curl_getinfo($raised['handle']);
							
							//レスポンス取得
							$response = curl_multi_getcontent($raised['handle']);

							if ($response === false) {
								//エラー。404などが返ってきている
								$this->errormsg = "ERROR";
							} else {
							
								foreach($urls as $url)
								{
									if ($url->rss_url == $info['url'])
									{
										try {
										
											libxml_use_internal_errors(true);
											
											//XMLパース
											$xml = simplexml_load_string($response);
											
											if ($xml)
											{
												//結果をセット
												$this->setItems($url->genre, $url->site_name, $xml);
												break;
											}
										} catch (Exception $e) {
											
										}
									}
								}
							}
						}
						
			        } while ($remains);
			        
			}
		}
		
		// ハンドルを閉じます
		foreach($ch as $c) curl_multi_remove_handle($mh, $c);
		
		curl_multi_close($mh);		
		
		//------------------------------
		//後処理
		//------------------------------
		
		return 0;
	}

	//****************************************************************************
	// Function     : setItems($genre, $site, $xml)
	// Description  : 結果の取得
	//****************************************************************************
    public function setItems($genre, $site, $xml)
	{
	
		$version = (String)$xml->attributes()->version;
		
 
		if(isset($xml->entry)){
		    //Atom用の処理
			$items = $xml->entry;
		}elseif(isset($xml->item)){
		    //RSS1.0用の処理
			$items = $xml->item;
		}elseif(isset($xml->channel->item)){
		    //RSS2.0用の処理
			$items = $xml->channel->item;
		} else {
			$this->errormsg = "記事がありません。";
			return -1;
		}

		//結果の取得
		foreach ($items as $item)
		{
			$idx = count($this->items);
			

			$this->items[$idx] = new ItemRssapi();
			
			$this->items[$idx]->genre		= $genre;														//ジャンル
			$this->items[$idx]->site_name	= $site;														//サイト名称
			$this->items[$idx]->title		= isset($item->title)       ? (String)$item->title : "";		//記事タイトル
			$this->items[$idx]->link		= isset($item->link)        ? (String)$item->link : "";			//URL
			$this->items[$idx]->description	= isset($item->description) ? (String)$item->description : "";	//粗筋
			$this->items[$idx]->author 		= isset($item->author)      ? (String)$item->author : "";		//著者の Email アドレス
			$this->items[$idx]->category 	= isset($item->category)    ? (String)$item->category : "";		//カテゴリー
			$this->items[$idx]->comments 	= isset($item->comments)    ? (String)$item->comments : "";		//コメントのページ URL
			$this->items[$idx]->enclosure 	= isset($item->enclosure)   ? (String)$item->enclosure : "";	//メディアオブジェクト
			$this->items[$idx]->guid 		= isset($item->guid)        ? (String)$item->guid : "";			//一意ID
			$this->items[$idx]->pubdate	 	= isset($item->pubDate)     ? (String)$item->pubDate : "";		//発行日時
			$this->items[$idx]->source 		= isset($item->source)      ? (String)$item->source : "";		//引用ソース
			
			if ($this->items[$idx]->pubdate == "") $this->items[$idx]->pubdate = (string)$item->children('http://purl.org/dc/elements/1.1/')->date;
			
			//日付を整形
			if ($this->items[$idx]->pubdate <> "")
			{
				$this->items[$idx]->pubdate = date("Y/m/d H:i:s", strtotime($this->items[$idx]->pubdate));
			}
		}
		
	}

	//****************************************************************************
	// Function     : sort_pubDate($order = "a")
	// Description  : 発行日時でソート
	//****************************************************************************
    public function sort_pubDate($order = "a")
	{
		//昇順
		function asc_cmp($a, $b)
		{
		
			if ($a->pubdate == "") return -1;
			if ($b->pubdate == "") return 1;
		
			return (strtotime($a->pubdate) > strtotime($b->pubdate)) ? 1 : -1;
		}
		
		//降順
		function desc_cmp($a, $b)
		{
			if ($a->pubdate == "") return 1;
			if ($b->pubdate == "") return -1;
			
			return (strtotime($a->pubdate) < strtotime($b->pubdate)) ? 1 : -1;
		}
		
		// クラス配列をソート
		if ($order == "a") {
			usort($this->items , "asc_cmp");
		} elseif ($order == "d")  {
			usort($this->items , "desc_cmp");
		}
	}

}
?>