<?php

	class RssSetting{
			
			protected $xml_template = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<language>ja</language>
	</channel>
</rss>
XML;
			protected $page_title,$page_url,$page_description,$rss_file_path,$rss_template,$xml_doc;
			function __construct($page_title,$page_url,$page_description,$rss_file_path){	
				$this->page_title = $page_title;
				$this->page_url = $page_url;
				$this->page_description = $page_description;
				$this->rss_file_path = $rss_file_path;
				
				if(is_file($rss_file_path)){
					$this->xml_doc = simplexml_load_file($rss_file_path);
					
				}
				else{
					
					echo htmlspecialchars($this->xml_template);
					$this->xml_doc = new SimpleXMLElement($this->xml_template);
					$this->xml_doc->channel->addChild("title",$page_title);
					$this->xml_doc->channel->addChild("link",$rss_file_path);
					$this->xml_doc->channel->addChild("description",$page_description);
					$fp = fopen($this->rss_file_path,"w");
					fwrite($fp,$this->xml_doc->asXML());
				}
				
				
			}
			
			function update_rss($item_title,$item_link,$item_description,$item_category,$item_content,$item_date){
				$item = $this->xml_doc->channel->addChild("item");
				$item->addChild("title",$item_title);
				$item->addChild("link",$item_link);
				$item->addChild("category",$item_category);
				$item->addChild("description",$item_description);
				$item->addChild("content",$item_content);
				$item->addChild("date",$item_date);
				
				$fp = fopen($this->rss_file_path,"w");
				fwrite($fp,$this->xml_doc->asXML());
			}
	}
?>
