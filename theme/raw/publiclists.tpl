
{include file="header.tpl"}

<div id="booksetwrap">
<br />
<br />
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/bookset/new.php">{str section="artefact.bookset" tag="newbookset"}</a>
    </div>
{if !$bookset.data}
    <div class="message">{$strnobooksetaddone|safe}</div>
{else}
<div id="booksetlist" class="fullwidth listing">
        {$bookset.tablerows|safe}
</div>
   {$bookset.pagination|safe}
   <div align="center">{$urlalllists|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}

