<?php
// Function called at the installation of the plugin
function GCS_Install() {

}
// Function called at the uninstallation of the plugin
function GCS_Uninstall() {

}

//In Depth Articles Generator
add_action('add_meta_boxes','GCS_meta_box');
function GCS_meta_box(){
	$args = array('capability_type' => 'post','objects');
	$post_types = get_post_types($args); 
	add_meta_box('GCS-meta-box', 'GrepWords Content Suggestion Engine', 'GCS_meta_box_function', $post_types);
}

add_action('save_post','save_GCS_metaboxe');
function save_GCS_metaboxe($post_ID){
  if(isset($_POST['GCS_description'])){
	update_post_meta($post_ID,'GCS_headline', $_POST['GCS_headline']);
	update_post_meta($post_ID,'GCS_alternativeHeadline', $_POST['GCS_alternativeHeadline']);
	update_post_meta($post_ID,'GCS_description', $_POST['GCS_description']);
  }
 }
function GCS_meta_box_function($post){
?>




<div class="wrap">

<div id="grepwordswidget" style='width:100%'>         

<?php

$options = get_option('grepwords_cse_options');
function _isCurl(){
    return function_exists('curl_version');
}
if(_isCurl()) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,"http://api.grepwords.com/lookup?q=baseball&apikey=".$options[API]);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$k = curl_exec($ch);
	if(!stristr($k,"missing")) { $s = 1; } else { $s = 0; }
} else {
	$k = file_get_contents("http://api.grepwords.com/lookup?q=baseball&apikey=".$options[API]);
	if(!stristr($k,"missing")) { $s = 1; } else { $s = 0; }
}


if($s==1) {

?>
                               
        <input type="text" id='grepq' placeholder="Keyword" name="q" style='width:300px' />
        <input type="button" onClick="grepReq(document.getElementById('grepq').value);return false" value="Suggest Content Ideas" />
    
<?php } else { echo "You need to update your GrepWords API key <a href='options-general.php?page=content-suggestions'>here</a> in settings or get your api key at <a href='http://grepwords.com/pricing.php'>Grepwords.com</a>"; } ?>


    <table id="greptable" cellpadding='8' cellspacing='0' style="border-width:1px;border-color:#ccc;border-style:solid;display:none;margin-top:15px;width:100%;font-size:16px;font-family:verdana;">
        <thead>
            <tr style='text-align:left;background:#ccc;color:#000;font-weight:bold;'>
                <th style='width:80%'>Question</th>
                <th style='width:10%;text-align:center;'>Searches</th>
                <th style='width:10%;text-align:center;'>CPC</th>
            </tr>
        </thead>
        <tbody id="greprows">

        </tbody>
    </table>                                                               
</div>

<script>
    function grepReq(q) {
        var a = document.createElement("script");
        a.type = "text/javascript";
        a.src = 'http://api.grepwords.com/questions?q=' + q;
    // console.log('inject:',a);
        (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(a)
    }

    function grepquestions(res) {
    // console.log('callback here',res)
        var greprows = document.getElementById('greprows'),
            i,tmp,cpc,volume,question,row;
    document.getElementById('greptable').style.display = 'block';
        while (greprows.firstChild) {
            greprows.removeChild(greprows.firstChild);
        }
        for (i = 0; i < res.length; i++) {
            
        tmp = res[i];
        row = document.createElement('tr');
        row.style.background = (i%2==0)?'':'#eee'
            question = document.createElement('td');
            //spn = document.createElement("<span>");
            //spn.style="text-decoration:underline;cursor:hand;cursor:pointer";
            //spn.onClick="document.getElementByTitle('title').value=this.innerContent";
            // spn.appendChild(document.createTextNode(tmp.keyword));
            // question.appendChild(spn);
            question.innerHTML="<span style='text-decoration:underline;cursor:hand;cursor:pointer' onClick=\"document.getElementById('title').value=this.innerHTML\">"+tmp.keyword+"</span>";
            row.appendChild(question);
        volume = document.createElement('td');
            volume.appendChild(document.createTextNode(tmp.gms));
            row.appendChild(volume);
            cpc = document.createElement('td');
            cpc.appendChild(document.createTextNode('$'+tmp.cpc));
            row.appendChild(cpc);
        greprows.appendChild(row);
        }
    }
</script>
 </div>

<?PHP 
}

function get_GCS_option($meta,$post_id){
	$meta_value=get_post_meta($post_id,$meta,true);
	if(strlen($meta_value)==0){
		return get_option($meta);
	}else{
		return $meta_value;
	}
}

add_action('admin_menu', 'GCS_create_menu');

function GCS_create_menu() {
	add_menu_page('GCS Settings', 'GCS Settings', 'administrator', __FILE__, 'GCS_settings_page');
	add_action( 'admin_init', 'register_mysettings' );
}


function GCS_register_mysettings() {
	register_setting( 'GCS-settings-group', 'GCS_headline' );
	register_setting( 'GCS-settings-group', 'GCS_alternativeheadline' );
	register_setting( 'GCS-settings-group', 'GCS_description' );
}

function GCS_settings_page() {
?>
<div class="wrap">                    
<h2>Content Suggestion Engine</h2>
<div id="grepwordswidget">
    <form onsubmit="grepReq(this.elements.q.value);return false">
        <input type="text" placeholder="Keyword" name="q" />
        <input type="submit" value="Suggest" />
    </form>
    <table id="greptable" style="display:none;">
        <thead>
            <tr>
                <th>Question</th>
                <th>Search Volume</th>
                <th>CPC</th>
            </tr>
        </thead>
        <tbody id="greprows">

        </tbody>
    </table>
</div>

<script>
    function grepReq(q) {
        var a = document.createElement("script");
        a.type = "text/javascript";
        a.src = 'http://api.grepwords.com/questions?q=' + q;
    console.log('inject:',a);
        (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(a)
    }

    function grepquestions(res) {
    console.log('callback here',res)
        var greprows = document.getElementById('greprows'),
            i,tmp,cpc,volume,question,row;
    document.getElementById('greptable').style.display = 'block';
        while (greprows.firstChild) {
            greprows.removeChild(greprows.firstChild);
        }
        for (i = 0; i < res.length; i++) {
        tmp = res[i];
        row = document.createElement('tr');
            question = document.createElement('td');
        question.appendChild(document.createTextNode(tmp.keyword));
            row.appendChild(question);
        volume = document.createElement('td');
            volume.appendChild(document.createTextNode(tmp.gms));
            row.appendChild(volume);
            cpc = document.createElement('td');
            cpc.appendChild(document.createTextNode('$'+tmp.cpc));
            row.appendChild(cpc);
        greprows.appendChild(row);
        }
    }
</script>

<!--

!-->
</div>
<?php } 
?>
