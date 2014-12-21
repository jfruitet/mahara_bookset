{include file="header.tpl"}
<div id="booksetswrap">
    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/booksets/new/item.php">{str section="artefact.booksets" tag="newitem"}</a>
    </div>
{if !$items.data}
    <div class="message">{$strnoitemsaddone|safe}</div>
{else}
<table id="booksetslist">
    <thead>
        <tr>
            <th class="completiondate">{str tag='completiondate' section='artefact.bookset'}</th>
            <th class="booksettitle">{str tag='title' section='artefact.bookset'}</th>
            <th class="booksetdescription">{str tag='description' section='artefact.bookset'}</th>
			<th class="booksetdescription">{str tag='motivation' section='artefact.bookset'}</th>
            <th class="booksetcontrols"></th>
            <th class="booksetcontrols"></th>
            <th class="booksetcontrols"></th>
        </tr>
    </thead>
    <tbody>
        {$items.tablerows|safe}
    </tbody>
</table>
   {$items.pagination|safe}
{/if}
</div>
{include file="footer.tpl"}
