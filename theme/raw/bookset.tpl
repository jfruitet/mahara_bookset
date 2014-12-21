{include file="header.tpl"}
<div id="booksetwrap">
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/bookset/new.php?id={$bookset}">{str section="artefact.bookset" tag="newitem"}</a>
    </div>
    {if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
{if !$items.data}
    <div>{$booksetitemsdescription}</div>
    <div class="message">{$strnoitemsaddone|safe}</div>
{else}
<table id="itemslist" class="fullwidth listing">
    <thead>
        <tr>
            <th>{str tag='code' section='artefact.bookset'}</th>
            <th>{str tag='title' section='artefact.bookset'}
			<th>{str tag='scale' section='artefact.bookset'}</th>
			<th>{str tag='valueindex' section='artefact.bookset'}</th>
        </tr>
    </thead>
    <tbody>
		{foreach from=$items.data item=itemr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$itemr->code|safe}</td>
				<td>{$itemr->title|safe}</td>
				<td>{$itemr->scale|clean_html|safe}</td>
				<td>{$itemr->valueindex|safe}</td>
			</tr>
			<tr>
				<td colspan="3"><i>{$itemr->description|clean_html|safe}</i></td>
				<td class="buttonscell btns2 planscontrols">
                <a href="{$WWWROOT}artefact/bookset/edit/item.php?id={$itemr->item}" title="{str tag=edit}">
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$itemr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/bookset/delete/item.php?id={$itemr->item}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$itemr->title)|escape:html|safe}">
                </a>
				</td>				
			</tr>
		{/foreach}
    </tbody>
</table>
   {$items.pagination|safe}
{/if}
</div>
{include file="footer.tpl"}
