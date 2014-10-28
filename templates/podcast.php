<?
header('Content-type: text/xml');
echo "<?xml version='1.0' encoding='utf-8' ?>"
?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
  <channel>
    <title><?= $page->title ?></title>
    <itunes:subtitle><?= $page->subtitle ?></itunes:subtitle>
    <itunes:author><?= $page->author ?></itunes:author>
    <itunes:summary><![CDATA[<?= $page->summary ?>]]></itunes:summary>
    <description><![CDATA[<?= $page->summary ?>]]></description>
    <link><?= $page->website ?></link>
    <language><?= $page->language ?: 'en-us' ?></language>
    <copyright>&#169;<?= date('Y') ?> <?= $page->author ?></copyright>
    <itunes:owner>
    	<itunes:name><?= $page->author ?></itunes:name>
    	<? if($page->owner_email) { ?>
    		<itunes:email><?= $page->owner_email ?></itunes:email>
    	<? } ?>
    </itunes:owner>
    <? if($page->image) { ?>
    	<itunes:image href="<?= $page->image->httpUrl ?>" />
    <? } ?>
    <? 
    foreach($page->categories as $category) {
    ?>
    	<itunes:category text="<?= $category->category ?>">
    		<itunes:category text="<?= $category->subcategory ?>" />
    	</itunes:category>
    <? 
    }
    ?>
    <? if($page->content_rating) { ?>
    	<itunes:explicit><?= $page->content_rating->name ?></itunes:explicit>
    <? } ?>
    <? if($page->complete) { ?>
    	<itunes:complete>yes</itunes:complete>
    <? } ?>
    <? if($page->block) { ?>
    	<itunes:block>yes</itunes:block>
    <? } ?>
    <? if($page->new_feed_url) { ?>
    	<itunes:new-feed-url><?= $page->new_feed_url ?></itunes:new-feed-url>
    <? } ?>
    
    <?
    // Create episode list
    foreach($page->children('template=episode,sort=-pub_date') as $item) {
    
    	// Deterimine file values for each episode
    	$fileUrl = $item->file ? $item->file->httpUrl : $item->file_url;
    	$fileSize = $item->file ? $item->file->filesize : $item->file_size;
    	$fileExt = $item->file ? $item->file->ext : pathinfo($item->file_url, PATHINFO_EXTENSION);
    	$fileType = '';
    	
    	// Auto-Detect file type
    	$fileTypes = array(
    		'audio/mpeg' => 'mp3',
    		'audio/x-m4a' => 'm4a',
    		'video/mp4' => 'mp4',
    		'video/x-m4v' => 'm4v',
    		'video/quicktime' => 'mov',
    		'application/pdf' => 'pdf',
    		'document/x-epub' => 'epub'
    	);
    	foreach($fileTypes as $key => $value) {
    		if(strcasecmp($value, $fileExt) == 0) { //If the value matches the file extension
    			$fileType = $key; //Set $fileType to the corresponding File Type string from the $fileTypes array
    			break;
    		}
    	}
    	
    ?>
    <item>
      <title><?= $item->title ?></title>
      <itunes:subtitle><?= $item->subtitle ?></itunes:subtitle>
      <itunes:summary><![CDATA[<?= $item->summary ?>]]></itunes:summary>
      <enclosure url="<?= $fileUrl ?>" length="<?= $fileSize ?>" type="<?= $fileType ?>" />
      <? if($item->image) { ?>
      	<itunes:image href="<?= $item->image->httpUrl ?>" />
      <? } ?>
      <guid><?= $item->httpUrl ?></guid>
      <pubDate><?= date(DATE_RFC2822, $item->pub_date) ?></pubDate>
      <itunes:duration><?= $item->duration ?></itunes:duration>
      <itunes:author><?= $item->author ?: $page->author ?></itunes:author>
      <? if($item->content_rating) { ?>
      	<itunes:explicit><?= $item->content_rating->name ?></itunes:explicit>
      <? } ?>
      <? if($item->closed_captioned) { ?>
      	<itunes:isClosedCaptioned>yes</itunes:isClosedCaptioned>
      <? } ?>
      <? if($item->order) { ?>
      	<itunes:order><?= $item->order ?></itunes:order>
      <? } ?>
      <? if($item->block) { ?>
      	<itunes:block>yes</itunes:block>
      <? } ?>
    </item>
    <? 
    }
    ?>
  </channel>
</rss>