{include file="header.tpl"}
<div id="booksetwrap">
<br /><br /><br />
    {if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
{if !$components.data}
    <div>{$booksetcomponentsdescription}</div>
	<div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/bookset/select/index.php?id={$bookset}">{str section="artefact.bookset" tag="selectcomponents"}</a>
    </div>
    <div class="message">{$strnocomponentsaddone|safe}</div>
{else}
<table id="componentslist" class="fullwidth listing">
<tr><td width="90%">
<h3 align="center"><a href="{$WWWROOT}artefact/bookset/index.php?id={$bookset}">{$artefacttitle|safe}</a></h3>
{if $artefactdescription}
	<table>
	<tr><td><b>{str tag='description' section='artefact.bookset'}</b> {$artefactdescription|safe}</td>
	{if $artefactstatus}
		<td><b>{str tag='status' section='artefact.bookset'}</b> {$artefactstatus|safe}</td>
	{/if}
	{if $artefactpublic}
		<td><b>{str tag='public' section='artefact.bookset'}</b> {$artefactpublic|safe}'</td>
	{/if}
	</tr>
	</table>
{/if}
</td>
</tr></table>
<table id="componentslist" class="fullwidth listing">
    <thead>
        <tr>
            <th colspan="4">{str tag='booklets' section='artefact.bookset'}</th>
			<th with="10%">&nbsp;</th>
        </tr>	
        <tr>
            <th width="15%">{str tag='title' section='artefact.bookset'}</th>
			<th>{str tag='status' section='artefact.bookset'}</th>
			<th>{str tag='public' section='artefact.bookset'}</th>
			<th>{str tag='displayorder' section='artefact.bookset'}</th>
			<th with="10%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		{foreach from=$components.data item=component}
			<tr class="{cycle values='r0,r1'}">
				<td rowspan="2" width="15%">{$component->title|safe}</td>	
				<td>{$component->status|safe}</td>
				<td>{$component->public|safe}</td>
				<td>{$component->displayorder|safe}</td> 
				<td class="buttonscell btns2 planscontrols" rowspan="2">  				
					<a href="{$WWWROOT}artefact/bookset/edit/component.php?id={$component->id}" title="{str tag=edit}">
						<img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$component->title)|escape:html|safe}">
					</a>
					<a href="{$WWWROOT}artefact/bookset/delete/component.php?id={$bookset}&componentid={$component->id}" title="{str tag=delete}">
						<img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$component->title)|escape:html|safe}">
					</a>
					<a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset}&amp;componentid={$component->id}&amp;direction=1&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=movecomponentdown}">
						<img src="{theme_url filename='images/btn_movedown.png'}" alt="{str(tag=editspecific arg1=$component->title)|escape:html|safe}">
					</a>
					<a href="{$WWWROOT}artefact/bookset/bookset.php?id={$bookset}&amp;componentid={$component->id}&amp;direction=0&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=movecomponentup}">
						<img src="{theme_url filename='images/btn_moveup.png'}" alt="{str(tag=editspecific arg1=$component->title)|escape:html|safe}">
					</a>		
					<a href="{$WWWROOT}artefact/bookset/select/index.php?id={$bookset}" title="{str tag=insertcomponentafter section=artefact.bookset}">
						<img src="{theme_url filename='images/btn_add.png'}" alt="{str(tag=editspecific arg1=$component->title)|escape:html|safe}">
					</a>
				</td>
			</tr>				
			<tr>		
				<td colspan="3"><i>{$component->help|safe}</i></td>
			</tr>
		{/foreach}
    </tbody>
</table>
   {$components.pagination|safe}
   <div align="center">{$urlallcomponents|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
