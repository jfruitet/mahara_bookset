
{include file="header.tpl"}

<div id="booksetwrap">
{if $d}
    {foreach from=$indexform item=itemform}
        {$itemform|safe}
    {/foreach}
{else}
    {foreach from=$indexform item=itemform}
        {$itemform|safe}
    {/foreach}
{/if}

</div>

<div id="booksetwrap">
<br />
<br />
<br />
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/bookset/new.php">{str section="artefact.bookset" tag="newbookset"}</a>
    </div>
{if !$booksets.data}
    <div class="message">{$strnobooksetaddone|safe}</div>
{else}
<div id="booksetlist" class="fullwidth listing">
        {$booksets.tablerows|safe}
</div>
   {$booksets.pagination|safe}
   <div align="center">{$urlalllists|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
