<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>SocietasPro</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" type="text/css" href="{$root}style/admin.css" />
<script type="text/javascript" src="{$root}js/jquery.js"></script>
<script type="text/javascript" src="{$root}admin/resources/js"></script>
{block name=head}{/block}
</head>
<body>

	<div id="container">
	
		<div id="header">

			<h1>SocietasPro</h1>
			
			<div id="navigation">
				[ <a href="{$root}admin">{$lang_home}</a> ]
				[ <a href="{$root}admin/members">{$lang_members}</a> ]
				[ <a href="{$root}admin/mailinglist">{$lang_mailing_list}</a> ]
				[ <a href="{$root}admin/events">{$lang_events}</a> ]
				[ <a href="{$root}admin/pages">{$lang_pages}</a> ]
				[ <a href="{$root}admin/blog">{$lang_blog}</a> ]
				[ <a href="{$root}admin/config">{$lang_configuration}</a> ]
				[ <a href="{$root}admin/reporting">{$lang_reporting}</a> ]
				[ <a href="{$root}system/auth/logout">{$lang_logout}</a> ]
				[ <a href="{$root}public">{$lang_view}</a> ]
			</div>
			
			<hr />
		
		</div>
		
		{if $msg}
			{$msg}
			<hr />
		{/if}
		{block name=body}{/block}
	
	</div>

</body>
</html>