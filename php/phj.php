<?php
/**
 *
* @author Tanase Razvan
* @return Events created in the main.phj file.This is a PARSER for PHJ.
* @version 1.40 Official 29/04/2015
*/
header('Content-Type: text/html; charset=utf-8');

function phj($value)
{
	$configs=array();
	$lines=preg_split('/\n\s*(?!\s+)/', file_get_contents ( ".phj_vars/phj.config" ));
	foreach ($lines as $line)
	{
		$configs=preg_split('/>\s*(?!\s+)/', $line);
		foreach ($configs as $config)
		{
			$settings[]=$config;
		}
	}

	$phj = new PHJ($settings[1], $settings[3], $settings[5],"$value");
	$phj->fill();
}
function thisDNS($dns)
{
	$_SESSION["this-dns"]=str_replace(array("http://","https://"), "", $dns);
}
function newPHJ($path,$content){
    file_put_contents($path, $content);
}
class PHJ
{
    public $title;		/*side_news (e.g)*/
	public $lang;		/*it*/
	public $root;		/*document root*/
	public $source;		/*.PHJ*/

	public function __construct($title,$lang,$root,$source)
	{
		$this->title=$title;
		$this->lang=$lang;
		$this->root=$root;
		$this->source=$source;
	}

	public function fill()
	{
		$title=$this->title;
		$lang=$this->lang;
		$root=$this->root;
		$_SESSION["PHJ_ROOT"]=$root;
		$file=$this->source;
		
		if(!file_exists(".phj_vars"))
		{
			mkdir(".phj_vars");
		}
		
		if(!file_exists(".phj_vars/phj.config"))
		{
			file_put_contents(".phj_vars/phj.config", "title>$title\nlang>$lang\nroot>$root");
		}
		
		if(file_exists($file))
		{
			$my_file=file_get_contents($file);
		}
		else
		{
			$my_file=$file;
		}
			$code_type="phj";
			
			$array = preg_split('/(?<!\\\\);\s+(?!\s+)/', $my_file);
			$line_number=1;
			foreach ($array as $content)
			{
				$break=array("\\s","\\");
				$content=str_replace($break, "", $content);
				
				if ($content==">") {
					print "<br />";
				}
				elseif(substr($content,0,4)=='php:')
				{
					$phpString=str_replace("\s", "", preg_replace('/!\s+(?!\s+)/', ";", substr($content,4)));
					eval("$phpString");
				}
				elseif(substr($content,0,1)=="$")
				{
					$phj_tag=strtolower(substr($content,1,strpos($content," ")-1));
					switch ($phj_tag)
					{
						case "html":
							print "<!DOCTYPE HTML>
							<html lang='$lang'>";
							break;
						case "head":
							print "<head>
								<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">";
							break;
						default:
							print "<".substr($content,1).">";
							break;
					}
				}
				elseif(substr($content,0,1)=="!")
				{
					print "</".substr($content,1).">";
				}
				elseif( substr($content,0,1)==":")
				{
					print "<!-- ".substr($content,1)." -->";
				}
				elseif( substr($content,0,1)=="&")
				{
					print "&".substr($content, 1).";";
				}
				elseif(substr($content,0,1)=="<")
				{
					switch (substr($content,1,strpos($content," ")-1))
					{
						case "html":
							print "<!DOCTYPE HTML>
							<html lang='$lang'>";
							break;
						case "head":
							print "<head>
								<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">";
							break;
						default:
							print "$content";
							break;
					}
				}
				
				elseif ($content==null)
				{
					/*do nothing*/
				}
				elseif(strlen($content)>2)
				{
					$index=substr($content,0,strpos(substr($content, 0, strpos($content, "(\"")), "."));
					$indexPos=strpos($content,$index);
					
					$ss=strpos($content,"(\"")+2;
					if($ss)
					{	
						$es=strpos($content, "\")");
						if($es)
						{
							$type=substr($content,0,$ss);
	
							if($type)
							{
								
								$attributes=array(
										"HTML:HTML",
										"tooltip:tooltip",
										"as:as",
										"data_alias:data_alias",
										"align:align",
										"limit:limit",
										"from:from",
										"to:to",
										"extract:extract",
										"data:data",
										"method:method",
										"is_temp:is_temp",
										"or:or",
										"height:height",
										"width:width",
										"editable:editable",
										"VAR:VAR_name",
										"var:var",
										"style:style",
										"name:name",
										"wait:wait",
										"time:time",
										"else:else",
										"target:t",
										"#:id",
										".:class",
										"value:value_item",
										"to:to",
										"except:except",
										"subject:subject",
										"paypal_form:paypal_form",
										"vendor:vendor",
										"cmd:cmd",
										"return:return",
										"cancel_return:cancel_return",
										"notify_url:notify_url",
										"rm:rm",
										"currency:currency",
										"lc:lc",
										"cbt:cbt",
										"shipping:shipping",
										"cs:cs",
										"item_name:item_name",
										"item_price:item_price",
										"custom:custom",
										"inside:inside",
										"upload:upload",
										"args:args",
										"max:max",
										"min:min",
										"title:title",
										"split:split",
										"controls:controls",
										"custom:custom",
										"loop:loop",
										"muted:muted"
								);
								foreach($attributes as $attr)
								{
									$attr=explode(":",$attr);
									$attribute_name=$attr[0];
									$attribute_alias=$attr[1];
									
									/*//////////////////////////////////////////////////////////...[""]*/
									if(strpos(strtolower(substr($content, $ss)), "$attribute_name"."[") == true)
									{
										$attribute_start=strpos(strtolower(substr($content, $es)), "$attribute_name"."[");
									
										$attribute_length=strpos(substr($content, $es+$attribute_start), "]");
									
										$attribute=str_replace($clean_attribute=array("[\"","\"]"), "", substr($content, $es+$attribute_start+strlen($attribute_name), $attribute_length-(strlen($attribute_name)-1)));
										
										eval('$'."$attribute_alias".'=$attribute;');
										if(isset($var))
										{
											$_SESSION["var$var"]=false;
											if($_SESSION["var_$var"]==false)
											{
												$content=str_replace("var[\"$var\"]", "", $content);
												file_put_contents(".phj_vars/$var.phj.var", "$content;");
												$_SESSION["var_$var"]=0;
											}
											else
											{
												/*variables have already been updated, there's nothing else to do*/
											}
										}
									}
									else
									{
										$attribute=null;
										eval('$'."$attribute_alias".'=$attribute;');
										$attribute_length=null;
									}
								}
								
								/*//////////////////////////////////////////////////////////where[]*/
								if(strpos(substr($content, $ss), "where[") == true)
								{
									$where_start=strpos(substr($content, $es), "where[");
								
									$where_length=strpos(substr($content, $es+$where_start), "]");
								
									$where=preg_replace('/!\s+(?=\S*)/', ";",str_replace($clean_e=array("[","]"), "", substr($content, $es+$where_start+5, $where_length-4)));
								}
								else
								{
									$where=null;
									$where_length=null;
								}
								
								
								/*//////////////////////////////////////////////////////////animate[]*/
								if(strpos(substr($content, $ss), "animate[") == true)
								{
									$animate_start=strpos(substr($content, $es), "animate[");
								
									$animate_length=strpos(substr($content, $es+$animate_start), "]");
								
									$animate=str_replace(")!", ");",str_replace($clean_e=array("[","]"), "", substr($content, $es+$animate_start+7, $animate_length-6)));
									

								}
								else
								{
									$animate="";
									$animate_length=null;
								}
								
								/*//////////////////////////////////////////////////////////do[]*/
								if(strpos(substr($content, $ss), "do[") == true)
								{
									$do_start=strpos(substr($content, $es), "do[");
								
									$do_length=strpos(substr($content, $es+$do_start), "]");
								
									$do=str_replace($clean_e=array("[","]"), "", substr($content, $es+$do_start+2, $do_length-1));
									
									/*$do=preg_replace('//', $replacement, $subject);
									preg_grep($pattern, $input);***
									*******************
									*************
									**
									***
									**
									*/
								}
								else
								{
									$do="";
									$do_length=null;
								}
								
								/*///////////////////////////////////////////////////////////*/
								
								
								$value=str_replace("\\n", "<br />", substr($content,$ss,$es-$ss));
								
								$tag=substr($content, $indexPos+strlen($index)+1, $ss-(strlen($index)+2)-1);
								
								
								switch ($index)
								{
									case "tool":
										switch ($tag){
											case "tip":
												switch (strtolower($value)){
													case "on":
													case "enable":
													case "enabled";
													case "1";
														print"<script type=\"text/javascript\">
																$(document).on(\"mousemove\", function(e){
																
															    $(\"#$id\").css({
															        left:  e.pageX+20,
															        top:   e.pageY+10
															     });
															    $(\".$class\").on(\"mouseover\",function(){
															    	var tooltip=$(this).attr(\"tooltip\");
															    	$(\"#$id\").fadeIn(0).html(tooltip);
															    });
															    $(\".$class\").on(\"mouseout\",function(){
															    	$(\"#$id\").fadeOut(0);
															    });
															 });
															</script>
														".phj("@.div(\"\")#[\"$id\"];");
														break;
												}
												break;
										}
										break;
									case "email":
									case "EMAIL":
									case "e-mail":
									case "E-MAIL":
									case "E-mail":
										switch($tag)
										{
											case "send":
												if(isset($_POST["$t"]))
												{
													if(substr($to,0,7)=="string:")
														{$email_to      = $to;}
													else{$email_to      = $_POST["$to"];}
													
													if(substr($subject,0,7)=="string:")
														{$email_subject      = $subject;}
													else{$email_subject      = $_POST["$subject"];}
													
													if(substr($value,0,7)=="string:")
														{$email_message      = $value;}
													else{$email_message      = $_POST["$value"];}
													
													if(substr($from,0,7)=="string:")
														{$email_from      = $from;}
													else{$email_from      = $_POST["$from"];}
													
														$email_headers = "From: ".$email_from . "\r\n" .
															"Reply-To: ".$email_from . "\r\n" .
															"X-Mailer: PHP/" . phpversion();
													
													$email_to		=str_replace(array("\n"," ","\t"),"",$email_to);
													$email_subject	=str_replace(array("\n"," ","\t"),"",$email_subject);
													$email_from		=str_replace(array("\n"," ","\t"),"",$email_from);
													$send_email=mail($email_to, $email_subject, $email_message, $email_headers);
													
													
													if($send_email)
													{
														print $do;
													}
													else
													{
														print $do;
													}
												}
												break;
										}
										break;
									case "php":
										switch ($tag)
										{
											case "function":
													print eval("$value".'('.$data.');');
												break;
											case "go":
												eval("$value");
												break;
										}
										break;
									case "VIEW":
									case "view":
										switch ($tag)
										{
											case "data_from":
											case "data":
												switch (strtoupper($method))
												{
													case "GET":
													
														if(isset($_GET["$value"]))
														{
															$mydata=$_GET["$value"];
															$mydata=rtrim($mydata,'/');
															$mydata=end(explode("/", $mydata));
															
															print str_replace('$_PHJ_DATA', $mydata, $as);
														}
														break;
													case "POST":
														if(isset($_POST["$value"]))
														{
															$mydata=$_POST["$value"];
															$mydata=rtrim($mydata,'/');
															$mydata=explode("/", $mydata);
															print str_replace('$_PHJ_DATA', $mydata, $as);
														}
														break;
													default:
														$evMe='if(isset($_'.$method.'["'.$value.'"]))
														{
															$url=$_POST["$value"];
															$url=rtrim($url,'/');
															$url=explode("/", $url);
															$send=str_replace("$_PHJ_DATA", $url, $as);
														}';
														eval($evMe);
														break;
												}
												break;
										}
										break;
										
									case "SET":
									case "set":
											switch ($tag)
											{
												case "data_from":
												case "data":
													if(isset($method))
													{
														if(isset($name))
														{
															switch ($method)
															{
																case "get":
																	$_GET["$name"]=$value;
																	break;
																case "post":
																	$_POST["$name"]=$value;
																	break;
															}
														}else{ throw new Exception("Specify a <b>name</b> for your variable.");}
													}else{ throw new Exception("Specify a <b>method</b> for your variable.");}
													break;
											}
										break;
									case "sql":
										switch ($tag)
										{
											/*case "fetch":
												$fetch_query=mysql_query("select $value from $from where $where $limit;");
												$fetch_rows
												break;*/
											case "query":
											case "go":
												
												if(!$send)
												{
													print mysql_error();
												}
												else
												{
													$_SESSION["$value"]=mysql_query(str_replace("\"", "\"", $do));
												}
												break;
										}
										break;
									
									
									case "harvest":
										switch ($tag)
										{
											case "directory":
											case "dir":
												if(isset($_GET["$name"]))
												{
													$url=$_GET["$name"];
													$url=rtrim($url,'/');
													$url=explode("/", $url);
													
													if(file_exists("controllers/".$url[0].".php"))
													{
														if(!isset($id))
														{
															$id=null;
														}
														if(!isset($url[0]))
														{
															$classobject=null;
														}
														else
														{
															if($url[0]!=$except)
															{
																$classobject=$url[0].$id;
																if(!file_exists("$value/$classobject.php"))
																{
																	
																		$classobject=$else.$id;
																	
																	
																	if(!file_exists("$value/$classobject.php"))
																	{
																		$classobject=null;
																	}
																}
															}
															else 
															$classobject=null;
														}
													}else 
													{
														header("Location: error");
													}
												}else
												{
													if(!isset($id))
													{
														$id=null;
													}
													if(!isset($else))
													{
														$classobject=null;
													}
													else
													{
														if($else!=$except){
															$classobject=$else.$id;
															if(!file_exists("$value/$classobject.php"))
															{
																$classobject=null;
															}
														}
														else
															$classobject=null;
													}
												}
												if($classobject!=$except && $classobject!="" )
												{
													if(file_exists("$value/".$classobject.".php"))
													{
														require "$value/".$classobject.".php";
														$controller=new $classobject;
														
														if(isset($url[2]))
														{
															$controller->{$url[1]}($url[2]);
														}
														else
														{
															if(isset($url[1]))
															{
																$controller->{$url[1]}();
															}
														}
													}
												}
												/*print "$value/$classobject.php-> except: $except.php-> else: $else<br />";*/
												break;
										}
										break;
										
									case "js":
										switch ($tag)
										{
											case "prettify":
												print "<script type='text/javascript' src='https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js?autoload=true&amp'></script>";
												print "
													<script>//<![CDATA[
													(function () {
													  function html(s) {
													    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
													  }
							
													  var quineHtml = html(
													        '<!DOCTYPE html>\n<html>\n'
													      + document.documentElement.innerHTML
													      + '\n<\/html>\n');
							
													  // Highlight the operative parts:
													  quineHtml = quineHtml.replace(
													    /&lt;script src[\s\S]*?&gt;&lt;\/script&gt;|&lt;!--\?[\s\S]*?--&gt;|&lt;pre\b[\s\S]*?&lt;\/pre&gt;/g,
													    '<span class=\"operative\">$&</span>');
							
													  document.getElementById(\"quine\").innerHTML = quineHtml;
													})();
													//]]>
													</script>
													";
												break;
												
											case "uselist":
													listFolderFiles("$value","js","$except");
												break;
									
											case "usefile":
												print "<script type=\"text/javascript\" src=\"$root/$value\" ></script>";
												break;
											case "":
												print "<script type=\"text/javascript\" >
														$value
														</script>";
												break;
											default:
												print "<script type=\"text/javascript\" >
												$value
												</script>";
												break;
										}
										break;
										
									case "css":
										switch ($tag)
										{
											case "uselist":
													listFolderFiles("$value","css","$except");
												break;
												
											case "usefile":
												print "<link rel=\"stylesheet\" type=\"text/css\" href=\"$root/$value\" />";
												break;
											case "":
												$value=preg_replace("/!\s*(?!\s+)/", ";", $do);
												print "<style>$do</style>";
												break;
											default:
												$value=preg_replace("/!\s*(?!\s+)/", ";", $do);
												print "<style>$do</style>";
												break;
										}
										break;
									
									default:
									case "inline":
									/*case "onblur":
									case "onchange":
									case "ondblclick":
									case "onfocus";
									case "onfocusin":
									case "onfocusout":
									case "onmouseenter":
									case "onmouseout":
									case "onmouseover":
									case "onready":
									case "onresize":
									case "onselect":
									case "onclick":
									case "onmousedown":
									case "onmouseup":
									case "onmouseout":
									case "onkeypress":
									case "onscroll":*/
											$on_event=substr($index, 2, strlen($index)-2);
											if($index!="inline")
											if(substr(trim($t), 0,1)=="#" || 
												substr(trim($t), 0,1)=="." || 
												substr(trim($t), 0,4)=="dom." ||
												substr(trim($t), 0,5)=="html."
												) 
											{
												
												if(substr(trim($t), 0,4)=="dom.")
													$t=substr($t, strpos($t, "dom.")+4);
													elseif (substr(trim($t), 0,5)=="html.")
														$t="\"".substr($t, strpos($t, "html.")+5)."\"";
													elseif (substr(trim($t), 0,1)==".")
														$t="\".".substr($t, strpos($t, ".")+1)."\"";
													elseif (substr(trim($t), 0,1)=="#")
														$t="\"#".substr($t, strpos($t, "#")+1)."\"";
													else
														$t="\"$t\"";
													
													
												/*switch (substr(trim($t), 0,4))
												{
													case "dom.": 
														$t=substr($t, strpos($t, "dom.")+4); 
														break;
												}*/
												/*switch (substr(trim($t), 0,5))
												{
													case "html.": 
														$t="\"".substr($t, strpos($t, "html.")+5)."\"";
														break;
												}*/
												/*switch (substr(trim($t), 0,1))
												{
													case ".": 
														$t="\"".substr($t, strpos($t, ".")+1)."\"";
														break;
													case "#": 
														$t="\"".substr($t, strpos($t, "#")+1)."\"";
														break;
												}*/
											}
											else
											{
												print("<p color=#f45>Invalid target at index <b>$line_number</b>.Content: <b>\"$content\"</b> in $file</p>");
											}
											print "<script type=\"text/javascript\">";
											
											if($index!="inline")
											{
												print "
													$(document).ready(function(){";
												if(isset($time))
												if($time!="none")
												{
													print "setInterval(function() {";
												}
												print "
													$($t).on('$on_event',function(){";
											}
											switch ($tag)
											{
												case "data_set":
													print "			
														$(\"$value\").data('$data_alias','$data')!
														var $data_alias=$(\"$value\").data('$data_alias')!
										
														$do";
													break;
												case "load":
														if(file_exists($data))
														{
															print("$(\"$to\").load($value);");
														}
														else
														{
															print("$(\"$to\").load(window.location.href+\" $value\");");
														}
														print "$do";
													break;
												case "js":
												case "javascript":
														print $do;
													break;
												case "ajax":
													$_SESSION["data_list"]=preg_split('/,\s*(?=\S*)/', $data);
													print "\n";
													foreach ($_SESSION["data_list"] as $ajax_var)
													{
														
														$ajax_var_name=str_replace("HTML:", "", str_replace(".", "class_", str_replace("#", "id_", $ajax_var)));
														
														$ajax_htmlcheck_var=$ajax_var;
														$old_ajax_var=$ajax_var;
														$is="";
														if(count(explode(":", $old_ajax_var))>1){
															$ajax_var=explode(":", $old_ajax_var);
															$is=":".$ajax_var[1];
															$ajax_var=$ajax_var[0];
															$checked=true;
														} 
														else $checked=false;

														$ajax_htmlcheck_var=preg_replace('/\s+/', '', $ajax_htmlcheck_var);
														$ajax_var=preg_replace('/\s+/', '', $ajax_var);
														
														switch (substr(trim(preg_replace('/\s+/', '', $ajax_htmlcheck_var)), 0,1))
														{
															case "#": 
																print "var id_".substr($ajax_var, strpos(trim($ajax_var), "#")+1)."=$(\"$ajax_var\").val();"; 
																break;
															case ".": 
																print "var class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1)."=[];
																		$(\"$ajax_var$is\").each(function(){";
																		if($checked){
																			print "class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1).".push($(this).val());";
																		}
																		else {print "class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1).".push($(this).val());";}
																		print "
																	    });
														       			class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1)."=JSON.stringify(class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1).");
																		"; 
																break;
														}
														switch (substr(trim(preg_replace('/\s+/', '', $ajax_htmlcheck_var)), 0,5))
														{
															case "html.": 
																print "var html_".substr($ajax_var, strpos(trim($ajax_var), "html.")+5)."=$(\"".substr($ajax_var, strpos(trim($ajax_var), "html.")+5)."\").html();"; 
																break;
														}
														switch (substr(trim(preg_replace('/\s+/', '', $ajax_htmlcheck_var)), 0,4))
														{
															case "dom.": 
																print "var dom_".substr($ajax_var, strpos(trim($ajax_var), "dom.")+4)."=$(".substr($ajax_var, strpos(trim($ajax_var), "dom.")+4).").val();";
																break;
														}
														
														
													}
													print "
														$.ajax({
															type: \"$method\",
															url: \"$value\",
															data: ";
															$count_var=1;
															foreach ($_SESSION["data_list"] as $ajax_var)
															{
																$old_ajax_var=$ajax_var;
																if($ajax_var=explode(":", $old_ajax_var)){
																	$ajax_var=$ajax_var[0];
																	$checked=true;
																}
																else $checked=false;
																$ajax_var=preg_replace('/\s+/', '', $ajax_var);
																switch (substr(trim($ajax_var), 0,1))
																{
																	case "#": 
																		
																		$ajax_var_name="id_".substr($ajax_var, strpos(trim($ajax_var), "#")+1); 
																		break;
																	case ".": 
																		$ajax_var_name="class_".substr($ajax_var, strpos(trim($ajax_var), ".")+1); 
																		break;
																}
																switch (substr(trim($ajax_var), 0,5))
																{
																	case "html.": 
																		$ajax_var_name="html_".substr($ajax_var, strpos(trim($ajax_var), "html.")+5); 
																		break;
																}
																switch (substr(trim($ajax_var), 0,4))
																{
																	case "dom.": 
																		$ajax_var_name="dom_".substr($ajax_var, strpos(trim($ajax_var), "dom.")+4);
																		break;
																}
																
																if ($count_var==1)
																{
																	print "'$ajax_var_name='+$ajax_var_name";
																	$count_var++;
																}
																else
																{
																	print "+'&$ajax_var_name='+$ajax_var_name";
																}
															}
															print ",\n$do
															
														});
														";
												break;
												case "animate": 
													if ($time=="default")
													{
														$time=2000;
													}
													$not_accepted_css=array("\n","\t");
													$accepted_css=array("<",">");
													$value=str_replace($accepted_css,"\"",str_replace($not_accepted_css, "", $value));
													print "
															$($t).animate({$do},$time);";
													break;
											}
											if($index!="inline")
											{
												print "});";
												
												if(isset($time))
												if($time!="none")
												{
													print "}, $time);";
												}
												
												print "});";
											}
											print "</script>";
										
										break;
										
									case "@":
										switch ($tag)
										{
											case "reclink":
											case "relink":
											case "recognizelink":
												print recognizeLink($value);
												break;
											case "prettify":
													$value=recognizeLink(htmlspecialchars(file_get_contents($value)));
													print "<pre to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" title=\"$title\" align=\"$align\" name=\"$name\" $custom";
													if(isset($id))
													{
														print " id=\"$id\" ";
													}
													else
													{
														print " id=\"quine\" ";
													}
													if (isset($class)) {
														print " class=\"prettyprint linenums $class\" ";
													}
													else
													{
														print " class=\"prettyprint linenums\" ";
													}
													
													print ">$value</pre>";
												break;
											case "\\n":
												print str_replace("\n", "<br />", $value);
												break;
											case "reclink\\n":
											case "relink\\n":
											case "recognizelink\\n":
											case "\\nreclink":
											case "\\nrelink":
											case "\\nrecognizelink":
												print str_replace("\n", "<br />", recognizeLink($value));
												break;
											case "style":
												$value=str_replace("!", ";", $value);
												print "<style $custom>$value</style>";
												break;
											case "script":
													print "<script type='text/javascript' $custom>$value</script>";
												break;
											case "[button]":
													if(isset($inside))
													{
														$button_url="$inside";
													}
													else
													{
														$button_url="$value";
													}
													print "
													<input to=\"$to\" style=\"$style\" onclick='window.location.replace(\"$button_url\")' tooltip=\"$tooltip\" title=\"$title\" align=\"$align\" target=\"$t\" id=\"$id\" class=\"$class\" name=\"$name\" type='button' value=\"$value\" $custom />
													
													";
												break;
											case "":
												print $value;
												break;
											default:
												print "<$tag to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" title=\"$title\" align=\"$align\" editable=\"$editable\" id=\"$id\" class=\"$class\" name=\"$name\" $custom>$value</$tag>";
												break;
											
										}
										break;
									case ">":
										$tag=strtolower($tag);
										switch ($tag)
										{
											case "paypal":
											case "pay-pal":
												
												print "
														<form to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" method=\"$method\" name=\"$name\" action=\"$value\" $custom>
															<input type=\"hidden\" name=\"business\" value=\"$vendor\" />
															<input type=\"hidden\" name=\"cmd\" value=\"$cmd\" />
															<input type=\"hidden\" name=\"upload\" value=\"$upload\">
															     
															<!-- Transaction information -->
															<input type=\"hidden\" name=\"return\" value=\"http://".$_SERVER['HTTP_HOST']."/$return\" />
															<input type=\"hidden\" name=\"cancel_return\" value=\".http://".$_SERVER['HTTP_HOST']."/$cancel_return\" />
															<input type=\"hidden\" name=\"notify_url\" value=\"http://".$_SERVER['HTTP_HOST']."/$notify_url\" />
															<input type=\"hidden\" name=\"rm\" value=\"$rm\" />
															<input type=\"hidden\" name=\"currency_code\" value=\"$currency\" />
															<input type=\"hidden\" name=\"lc\" value=\"$lc\" />
															<input type=\"hidden\" name=\"cbt\" value=\"$cbt\" />
															     
															<!-- Payment informations -->
															<input type=\"hidden\" name=\"cs\" value=\"$cs\" />
															     
															<!-- Product informations
															....
															undefined for security reasons.
															Please define it using a different php script -->
															     
															<!-- Selling informations -->
															<input type=\"hidden\" name=\"custom\" value=\"$custom\" />
															
															$inside
															$HTML
														</form>
														";
												break;
											case "txt":
											case "text":
												print "<input to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='text' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
												break;
											case "voice":
												print "<input to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" maxlength=\"$max\" $custom type=\"text\" speech>";
												break;
											case "pass":
											case "password":
												print "<input to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='password' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
												break;
											case "mail":
											case "email":
												print "<input to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='email' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
												break;
											case "txtarea":
											case "textarea":
												print "<textarea to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" name=\"$name\" placeholder=\"$value\" >$HTML</textarea>";
												break;
											case "option":
												print "<option to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" value='$value_item'>$value</option>";
											break;
											default:
												print "<input to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='$tag' name=\"$name\" value=\"$value\" />";
												break;
											
										}
										break;
										case ">>": /*input richiesto*/
											switch ($tag)
											{
												case "txt":
												case "text":
													print "<input to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='text' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
													break;
												case "voice":
													print "<input to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" maxlength=\"$max\" $custom type=\"text\" speech>";
													break;
												case "pass":
												case "password":
													print "<input to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='password' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
													break;
												case "mail":
												case "email":
													print "<input to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='email' name=\"$name\" placeholder=\"$value\" value=\"$HTML\" />";
													break;
												case "txtarea":
												case "textarea":
													print "<textarea to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" name=\"$name\" placeholder=\"$value\" >$HTML</textarea>";
													break;
												case "option":
													print "<option to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" value='$value_item'>$value</option>";
												break;
												default:
													print "<input to=\"$to\" style=\"$style\" required='required' tooltip=\"$tooltip\" minlength=\"$min\" $custom title=\"$title\" maxlength=\"$max\" id=\"$id\" class=\"$class\" type='$tag' name=\"$name\" value=\"$value\" />";
													break;
											}
											break;
									case "phj":
										switch ($tag)
										{
											case "uselist":
												$phj_script=scandir($value);
												foreach ($phj_script as $phj_script_read)
												{
													if(!file_exists("$value") || !is_dir("$value"))
													{
														print "<font color='#d30'>The specified <b>directory</b> does not exist.</font>";
													}
													else/*creo un nuovo oggetto PHJ e sfrutto la funzione fill()*/
													{
														if($phj_script_read!="." && $phj_script_read!="..")
														{
															$src_phj = new PHJ("$title", "$lang", "$root","$value/$phj_script_read");/*nuovo oggetto; indica il codice PHJ dentro il codice PHJ.*/
															call_user_func_array(array($src_phj, "fill"), array());/*Self call della funzione.*/
														}
													}
												}
												break;
											
											case "usefile":
												if(!file_exists("$value"))
												{
													print "<font color='#d30'>The specified <b>file</b> does not exist.</font>";
												}
												else/*creo un nuovo oggetto PHJ e sfrutto la funzione fill()*/
												{
													$src_phj = new PHJ("$title", "$lang", "$root","$value");/*nuovo oggetto; indica il codice PHJ dentro il codice PHJ.*/
													call_user_func_array(array($src_phj, "fill"), array());/*Self call della funzione*/
												}
												break;
											case "use_var":
												if(!file_exists(".phj_vars/$value"))
												{
													print "<font color='#d30'>The specified <b>variable</b> does not exist.</font>";
												}
												else/*creo un nuovo oggetto PHJ e sfrutto la funzione fill()*/
												{
													$src_phj = new PHJ("$title", "$lang", "$root",".phj_vars/$value");/*nuovo oggetto; indica il codice PHJ dentro il codice PHJ.*/
													call_user_func_array(array($src_phj, "fill"), array());/*Self call della funzione.*/
												}
												break;
										}
										break;
									case "src":
										switch ($tag)
										{
												
											case "php":
													include ($value);
												break;
											case "html":
												print file_get_contents($value);
												break;
											case "img":
											case "image":
												print "<img to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" $custom title=\"$title\" align='$align' id=\"$id\" class=\"$class\" src=\"$value\" width=\"$width\" height=\"$height\" />";
												break;
												
											case "video":
												$skinned_name=str_replace(substr($value, strpos($value, "."),strlen($value)-strpos($value, ".")), "", $value);
												
												print "
													<video tooltip=\"$tooltip\" $loop $muted title=\"$title\" id=\"$id\" class=\"$class\" $controls width='$width' height='$height' $custom >
														<source src=\"$value\" type=\"video/mp4\">
														$or
													</video>";
												break;
											case "audio":
												print "
												<audio tooltip=\"$tooltip\" $custom title=\"$title\" id=\"$id\" class=\"$class\" controls>
													<source src='$value' type='audio/mpeg'>
													$or
												</audio>";
												break;
											case "mirror":
												print file_get_contents("$value");
												break;
											case "file":
												print file_get_contents("$root/$value");
												break;
											case "youtube":
												/*/////////////////////////////YouTube/////////////////////////////////////////////*/
												if(strpos($value, "youtube.com/watch?")==true)
												{
													$format_iframe="v=";
													$start_code=2;
													$article_video_code=substr($value, strpos($value, $format_iframe)+$start_code, 11);
												}
												elseif(strpos($value, "youtu.be/")==true)
												{
													$format_iframe="youtu.be/";
													$start_code=9;
													$article_video_code=substr($value, strpos($value, $format_iframe)+$start_code, 11);
												}
												else
												{
													$format_iframe=null;
													$send=null;
												}
												if($format_iframe!=null)
												{
												
													if(strpos($value, "list=")==true)
													{
														$article_video_list="?list=".substr($value, strpos($value, "list=")+5, 34);
													}
													else
													{
														$article_video_list=null;
													}
													print "<iframe to=\"$to\" style=\"$style\" tooltip=\"$tooltip\" $custom width=\"$width\" height=\"$height\" id=\"$id\" title=\"$title\" align='$align' class=\"$class\" src='//www.youtube.com/embed/$article_video_code$article_video_list?wmode=transparent' frameborder='0' allowfullscreen></iframe>";
												}
												/*/////////////////////////////YouTube/////////////////////////////////////////////*/
												break;
											case "wikipedia":
												$wiki=file_get_contents("$value");
												
												$wiki_start=strpos($wiki, "<div id=\"mw-content-text\"");
												$wiki_length=strpos($wiki, "<p><span id=\"interwiki-en-ga\"></span>")-$wiki_start;
												
												print substr($wiki, $wiki_start, $wiki_length)."</div>";
												break;
										}
										break;
									case "a":
											print "<a to=\"$to\" target=\"$t\" style\"$style\" tooltip=\"$tooltip\" $custom title=\"$title\" id=\"$id\" class=\"$class\" href='$value'>$tag</a>";
										break;
									case "VAR":
									case "var":
										switch ($tag)
										{
											case "clean":
												file_put_contents("./.vars/$value.phj.var", " ");
												break;
										}	
										break;
									case "mk":
										switch ($tag)
										{
											case "graph":
												$send="-----GRAPH-----";
												break;
										}
										break;
								}
								if(isset($send))
								{
									/*****************************************
									*
									*DO NOTHING if isset($send)
									*/
									
									/****************************************
									 * END
									 * 
									 */
								}
						
							}else{echo"<br /><font color=\"#d30\">PHJ error: specify an object type.PHJ stopped at <b><u>index $line_number</u></b> <b>\"$content\"</b> in $file</font><br />";}
				
						}else{echo"<br /><font color=\"#d30\">PHJ error: complete your object.PHJ stopped at <b><u>index $line_number</u></b> \"$content\" in $file</font><br />";}

					}else{echo"<br /><font color=\"#d30\">PHJ error: start your object.PHJ stopped at <b><u>index $line_number</u></b> \"$content\" in $file</font><br />";}
				}
				else {
					print "<br />";/*riga nuova su "\n;"*/
				}$line_number++;
			}
	}
}

function recognizeLink($content)
{
	/*/////////////////////////////////////////REC LINK*/
	$newStr = preg_replace('!(http|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/=-]+!', "<a target='_blank' href=\"\\0\">\\0</a>",$content);
	return $newStr;
	/*/////////////////////////////////////////REC LINK*/
}
function YouTubeVideo($Contenuto,$id,$class,$autoplay)
{
	/*/////////////////////////////////////////YOUTUBE*/
	if(strpos($Contenuto, "youtube.com/watch?")==true)
	{
		$format_iframe="v=";
		$start_code=2;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	elseif(strpos($Contenuto, "youtu.be/")==true)
	{
		$format_iframe="youtu.be/";
		$start_code=9;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	else
	{
		$format_iframe=null;
		$YoutubeVideo=null;
	}
	if($format_iframe!=null)
	{
	
		if(strpos($Contenuto, "list=")==true)
		{
			$article_video_list="?list=".substr($Contenuto, strpos($Contenuto, "list=")+5, 34);
		}
		else
		{
			$article_video_list=null;
		}
		$YoutubeVideo="<iframe id=\"$id\" class=\"$class\" src='//www.youtube.com/embed/$article_video_code$article_video_list?wmode=transparent&autoplay=$autoplay' frameborder='0' allowfullscreen></iframe>";
		return $YoutubeVideo;
	}
	/*////////////////////////////////////////YOUTUBE*/
}
function YouTubeThumb($Contenuto,$id,$class)
{
	/*/////////////////////////////////////////YOUTUBE*/
	if(strpos($Contenuto, "youtube.com/watch?")==true)
	{
		$format_iframe="v=";
		$start_code=2;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	elseif(strpos($Contenuto, "youtu.be/")==true)
	{
		$format_iframe="youtu.be/";
		$start_code=9;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	else
	{
		$format_iframe=null;
		$YoutubeVideo=null;
	}
	if($format_iframe!=null)
	{

		if(strpos($Contenuto, "list=")==true)
		{
			$article_video_list="?list=".substr($Contenuto, strpos($Contenuto, "list=")+5, 34);
		}
		else
		{
			$article_video_list=null;
		}
		$YoutubeThumb="<img align='right' id=\"$id\" class=\"$class\" src='http://i1.ytimg.com/vi/$article_video_code/default.jpg' />";
		return $YoutubeThumb;
	}
	/*////////////////////////////////////////YOUTUBE*/
}
function YouTubeSuggestions($Contenuto,$id,$class)
{
	/*/////////////////////////////////////////YOUTUBE*/
	if(strpos($Contenuto, "youtube.com/watch?")==true)
	{
		$format_iframe="v=";
		$start_code=2;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	elseif(strpos($Contenuto, "youtu.be/")==true)
	{
		$format_iframe="youtu.be/";
		$start_code=9;
		$article_video_code=substr($Contenuto, strpos($Contenuto, $format_iframe)+$start_code, 11);
	}
	else
	{
		$format_iframe=null;
		$YoutubeVideo=null;
	}
	if($format_iframe!=null)
	{
		$whole=file_get_contents("http://youtube.com/watch?v=$article_video_code");
		
		$start_list=strpos("$whole", "<ul id=\"watch-related\" class=\"video-list\">");
		
		$length_list=strpos($start_code, "</ul>")-$start_code;
		
		$sub_list=substr($whole, $start_list,$length_list);
		
		$elements=explode("<a href=\"/watch?v", $sub_list);
		
		$i=0;
		foreach ($elements as $suggestion)
		{
			$i++;
			if($i>1)
			{
				$element=str_replace(">", "&gt;", str_replace("<", "&lt;", substr($suggestion, 1,11)));
				
				$YoutubeThumb="<a id='a_$id' class='a_$class' target='_blank' href='http://youtube.com/watch?v=$element'><img id=\"$id\" class=\"$class\" src='http://i1.ytimg.com/vi/$element/default.jpg' /></a>";
				
				print "$YoutubeThumb";

			}
			
		}
	}
	/*////////////////////////////////////////YOUTUBE*/
}
function setupHtaccess($VariableName)
{
	file_put_contents(".htaccess","
	RewriteEngine on

	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-l

	RewriteRule ^(.+)$ $VariableName=$1 [QSA,L]

	", FILE_APPEND);
}
function listFolderFiles($dir,$type,$exceptthis){
	$ffs = scandir($dir);
	$sourcelist=array();
	if (strpos($exceptthis, ","))
		$exceptthis=explode(",", $exceptthis);
	foreach($ffs as $ff){
		$lastofthis=end(explode(".", $ff));
		
		switch ($exceptthis){
			case "none":
			case "":
			case "0":
			case null:
				if($ff != '.' && $ff != '..'){
					if($lastofthis==$type)
					{
						switch ($type){
							case "css":
								print "<link rel='stylesheet' type='text/css' href='$dir/$ff'>";
								break;
							case "js":
								print "<script type='text/javascript' src='$dir/$ff'></script>";
								break;
						}
					}
					if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff,$type,"none");
				}
				break;
			case "subdirectories":
			case "subfolders":
				if($ff != '.' && $ff != '..'){
					if($lastofthis==$type)
					{
						switch ($type){
							case "css":
								print "<link rel='stylesheet' type='text/css' href='$dir/$ff'>";
								break;
							case "js":
								print "<script type='text/javascript' src='$dir/$ff'></script>";
								break;
						}
					}
				}
				break;
			default:
				$pass=true;
				$fullpath="$dir/$ff";
				
				if($ff != '.' && $ff != '..' && $ff!=$exceptthis && $fullpath!="$exceptthis" && $dir!="$exceptthis" && $pass=true){
					if($lastofthis==$type)
					{
						switch ($type){
							case "css":
								print "<link rel='stylesheet' type='text/css' href='$dir/$ff'>";
								break;
							case "js":
								print "<script type='text/javascript' src='$dir/$ff'></script>";
								break;
						}
					}
					if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff,$type,"$exceptthis");
						
				}
				break;
			}
		
	}
}


?>
