{*
 *  Leo Prestashop SliderShow for Prestashop 1.6.x
 *
 * @package   leosliderlayer
 * @version   3.0
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
*}

<article class="blog-item blogs media">
	{if $blog.image && $config->get('listing_show_image',1)}
		<div class="blog-image pull-left">
			<img src="{$blog.preview_url}" title="{$blog.title}" class="img-responsive" />
		</div>
	{/if}
	<div class="media-body">
		{if $config->get('listing_show_title','1')}
			<h4 class="title"><a href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title}">{$blog.title}</a></h4>
		{/if}
		<div class="blog-meta">
			{if $config->get('listing_show_author','1')&&!empty($blog.author)}
				<span class="blog-author">
					<span class="icon-user">{l s='Posted By' mod='leoblog'}:</span> 
					<a href="{$blog.author_link|escape:'html':'UTF-8'}" title="{$blog.author}">{$blog.author}</a> 
				</span>
			{/if}
			{if $config->get('listing_show_created','1')}
				<span class="blog-created">
					<span class="icon-user">{l s='On' mod='leoblog'}:</span>{strtotime($blog.date_add)|date_format:"%d %b, %Y"}
				</span>
			{/if}
			{if $config->get('listing_show_category','1')}
				<span class="blog-cat"> 
					<span class="icon-list"> {l s='In' mod='leoblog'}:</span> 
					<a href="{$blog.category_link|escape:'html':'UTF-8'}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title}</a>
				</span>
			{/if}
			
			
			{if isset($blog.comment_count)&&$config->get('listing_show_counter','1')}	
				<span class="blog-nbcomment">
					<span class="icon-comment"> {l s='Comment' mod='leoblog'}:</span> 
					{$blog.comment_count}
				</span>
			{/if}

			{if $config->get('listing_show_hit','1')}	
				<span class="blog-hits">
					<span class="icon-heart"> {l s='Hit' mod='leoblog'}:</span> 
					{$blog.hits}
				</span>
			{/if}
		</div>
		<div class="blog-shortinfo">
			{if $config->get('listing_show_description','1')}
				{$blog.description|strip_tags:'UTF-8'|truncate:160:'...'}
			{/if}
		</div>
		{if $config->get('listing_show_readmore',1)}
			<p>
				<a href="{$blog.link}" title="{$blog.title|escape:'html':'UTF-8'}" class="more btn btn-outline">{l s='Read more' mod='leoblog'}</a>
			</p>
		{/if}
	</div>
</article>